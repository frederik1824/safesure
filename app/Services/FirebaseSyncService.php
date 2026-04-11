<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Service to sync data with Firebase Firestore using the REST API.
 * This version uses manual JWT signing (RS256) to authenticate, 
 * avoiding the need for the gRPC extension or heavy Google SDKs.
 */
class FirebaseSyncService
{
    protected $client;
    protected $projectId;
    protected $credentials;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', 'syscarnet');
        
        // 1. Intentar cargar desde variable de entorno (JSON directo)
        $rawJson = env('FIREBASE_CREDENTIALS_JSON');
        
        if ($rawJson) {
            $this->credentials = json_decode($rawJson, true);
        } else {
            // 2. Fallback al archivo físico si no hay variable de entorno
            $jsonPath = base_path(env('FIREBASE_CREDENTIALS', 'firebase-auth.json'));
            if (file_exists($jsonPath)) {
                $this->credentials = json_decode(file_get_contents($jsonPath), true);
            }
        }

        if (!$this->credentials) {
            Log::warning("Firebase Sync: No se encontraron credenciales válidas (verifica FIREBASE_CREDENTIALS_JSON o el archivo físico).");
            return;
        }

        $this->client = new Client(['timeout' => 15.0]);
    }

    /**
     * Gets an OAuth2 Access Token from Google using the Service Account JSON
     */
    protected function getAccessToken(): ?string
    {
        // Check cache first to avoid repetitive auth calls
        if ($token = Cache::get('firebase_access_token')) {
            return $token;
        }

        try {
            $now = time();
            $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $payload = base64_encode(json_encode([
                'iss' => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/datastore',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600
            ]));

            // Helper for base64UrlEncode
            $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], $header);
            $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], $payload);

            $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
            $signature = '';
            
            openssl_sign($signatureInput, $signature, $this->credentials['private_key'], 'sha256');
            $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

            $jwt = $signatureInput . "." . $base64UrlSignature;

            $response = $this->client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $token = $data['access_token'];

            // Cache for 55 minutes
            Cache::put('firebase_access_token', $token, 3300);
            return $token;

        } catch (\Throwable $e) {
            Log::error("Firebase Auth Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Retrieve all documents from a Firestore collection via REST (Handles Pagination)
     */
    public function getCollection(string $collectionName): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        $results = [];
        $pageToken = null;
        $retries = 0;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            
            do {
                $query = $pageToken ? "?pageToken={$pageToken}" : "";
                
                try {
                    $response = $this->client->get("{$baseUrl}/{$collectionName}{$query}", [
                        'headers' => ['Authorization' => "Bearer {$token}"]
                    ]);
                } catch (\GuzzleHttp\Exception\ClientException $e) {
                    if ($e->getResponse()->getStatusCode() == 429 && $retries < 3) {
                        $retries++;
                        sleep(pow(2, $retries)); // Exponential backoff
                        continue;
                    }
                    throw $e;
                }

                $data = json_decode($response->getBody()->getContents(), true);
                
                foreach ($data['documents'] ?? [] as $doc) {
                    $results[] = $this->mapFirestoreRestDoc($doc);
                }

                $pageToken = $data['nextPageToken'] ?? null;

            } while ($pageToken);

            return $results;

        } catch (\Throwable $e) {
            Log::error("Firebase Sync Error (GET $collectionName): " . $e->getMessage());
            return $results;
        }
    }

    /**
     * Search documents using Structured Query (Incremental Sync)
     */
    /**
     * Search documents using Structured Query (Incremental Sync)
     * Supports multiple filters (field => value)
     */
    public function search(string $collection, array $filters = [], string $orderBy = 'created_at'): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            $structuredQuery = [
                'from' => [['collectionId' => $collection]],
            ];

            $filterList = [];
            foreach ($filters as $field => $value) {
                if (is_bool($value)) {
                    $filterList[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => 'EQUAL',
                            'value' => ['booleanValue' => $value]
                        ]
                    ];
                } else {
                    // Si el valor parece una fecha, usamos el formato ISO que usa Firebase
                    if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value)) {
                        $value = \Carbon\Carbon::parse($value)->toIso8601ZuluString('microsecond');
                    }

                    $filterList[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => 'GREATER_THAN_OR_EQUAL',
                            'value' => ['stringValue' => (string)$value]
                        ]
                    ];
                }
            }

            if (count($filterList) > 1) {
                $structuredQuery['where'] = ['compositeFilter' => ['op' => 'AND', 'filters' => $filterList]];
            } elseif (count($filterList) === 1) {
                $structuredQuery['where'] = $filterList[0];
            }

            if ($orderBy && count($filterList) > 0) {
                $structuredQuery['orderBy'] = [
                    ['field' => ['fieldPath' => $orderBy], 'direction' => 'ASCENDING']
                ];
            }

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];

            foreach ($data as $item) {
                if (isset($item['document'])) {
                    $results[] = $this->mapFirestoreRestDoc($item['document']);
                }
            }

            return $results;
        } catch (\Throwable $e) {
            Log::error("Firebase Search Error ($collection): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Compatibility alias for Push (used by Observers)
     */
    public function syncData(array $data, string $collection, string $documentId)
    {
        return $this->push($collection, $documentId, $data);
    }

    /**
     * Triggers a sync for an Eloquent model (used by legacy components)
     */
    public function syncModel($model, string $collectionName, string $documentId): void
    {
        $this->push($collectionName, $documentId, $model->toArray());
    }

    /**
     * Pushes a local data array to a Firestore collection via REST (PATCH)
     */
    public function push(string $collection, string $documentId, array $data)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $formattedData = ['fields' => $this->formatForFirestoreRest($data)];

            $this->client->patch("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $formattedData
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error("Firebase Push Error ({$collection}/{$documentId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Pulls a single document from Firestore and returns it as a flat array
     */
    public function pull(string $collection, string $documentId): ?array
    {
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $response = $this->client->get("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $this->mapFirestoreRestDoc($data);

        } catch (\Throwable $e) {
            if ($e->getCode() !== 404) {
                Log::error("Firebase Pull Error ({$collection}/{$documentId}): " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Compares local model with remote data and updates local if remote is newer
     */
    public function syncLocalModel($model, array $remoteData): bool
    {
        $remoteUpdatedAt = isset($remoteData['updated_at']) ? \Carbon\Carbon::parse($remoteData['updated_at']) : null;
        $localUpdatedAt = $model->updated_at;

        // If remote is newer or local is significantly older, we update
        if ($remoteUpdatedAt && (!$localUpdatedAt || $remoteUpdatedAt->gt($localUpdatedAt->addSeconds(2)))) {
            // Remove meta fields that shouldn't be mass assigned directly
            unset($remoteData['firebase_id']);
            
            // We use forceFill if we want to bypass fillable for certain system fields, 
            // but for safety we'll just use fill and filter
            $fillableData = array_intersect_key($remoteData, array_flip($model->getFillable()));
            
            // Special handling for firebase_synced_at to prevent infinite loops
            $model->fill($fillableData);
            $model->firebase_synced_at = now();
            $model->saveQuietly();
            
            return true;
        }

        return false;
    }

    /**
     * Deletes a document from Firestore via REST (DELETE)
     */
    public function deleteDocument(string $collection, string $documentId)
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $this->client->delete("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);
            return true;
        } catch (\Throwable $e) {
            Log::error("Firebase Delete Error ({$collection}/{$documentId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Maps a Firestore REST API document (with typed fields) to a flat array
     */
    protected function mapFirestoreRestDoc(array $doc): array
    {
        $fields = $doc['fields'] ?? [];
        $mapped = [];

        $nameParts = explode('/', $doc['name'] ?? '');
        $mapped['firebase_id'] = end($nameParts);

        foreach ($fields as $key => $values) {
            // Firestore REST returns: "stringValue": "...", "integerValue": "...", etc.
            $val = reset($values);
            $type = key($values);

            if ($type === 'integerValue') $val = (int)$val;
            if ($type === 'doubleValue') $val = (float)$val;
            if ($type === 'booleanValue') $val = (bool)$val;
            
            $mapped[$key] = $val;
        }

        return $mapped;
    }

    /**
     * Formats a flat array into Firestore REST's typed field structure
     */
    protected function formatForFirestoreRest(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value)) {
                $fields[$key] = ['integerValue' => (string)$value];
            } elseif (is_float($value)) {
                $fields[$key] = ['doubleValue' => $value];
            } elseif (is_array($value)) {
                $fields[$key] = ['stringValue' => json_encode($value)];
            } else {
                $fields[$key] = (string)$value === '' ? ['nullValue' => null] : ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }
}
