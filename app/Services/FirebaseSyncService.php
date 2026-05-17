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

    // Métodos de Control de Cuota (SafeSync)
    protected $readsInCurrentExecution = 0;
    protected $writesInCurrentExecution = 0;
    protected $syncLog = null;
    protected $maxReadsPerExecution = 20000; // Límite de seguridad por ráfaga (Aumentado para reconstrucción total)
    protected $circuitOpenKey = 'firebase_circuit_open';
    protected $dailyReadsKey = 'firebase_daily_reads_count';

    public function setSyncLog($log)
    {
        $this->syncLog = $log;
    }

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', 'syscarnet');
        $this->maxReadsPerExecution = env('FIREBASE_MAX_READS_PER_SYNC', 20000);
        
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

        $this->client = new Client(['timeout' => 30.0]); // Aumentamos timeout para red inestable
    }

    /**
     * Verifica si el corta-circuitos está abierto (cuota agotada o error crítico)
     */
    public function isCircuitOpen(): bool
    {
        return Cache::get($this->circuitOpenKey, false);
    }

    /**
     * Registra lecturas y verifica presupuesto
     */
    protected function trackReads(int $count)
    {
        if ($count <= 0) return;
        
        $this->readsInCurrentExecution += $count;
        
        // Actualizar contador diario en caché
        $today = now()->format('Y-m-d');
        $dailyCount = Cache::get($this->dailyReadsKey . '_' . $today, 0);
        Cache::put($this->dailyReadsKey . '_' . $today, $dailyCount + $count, 86400);

        if ($this->syncLog) {
            try {
                $this->syncLog->increment('firebase_reads', $count);
            } catch (\Throwable $e) {
                Log::warning("Failed to increment firebase_reads: " . $e->getMessage());
            }
        }

        if ($this->readsInCurrentExecution > $this->maxReadsPerExecution) {
            $msg = "SafeSync: Presupuesto de lecturas agotado para esta ejecución ({$this->readsInCurrentExecution}).";
            \Illuminate\Support\Facades\Notification::route('mail', 'admin@safesure.com')->notify(new \App\Notifications\FirebaseAlertNotification('LÍMITE RÁFAGA SUPERADO', $msg));
            throw new \Exception($msg);
        }
        
        $dailyLimit = (int)env('FIREBASE_DAILY_READ_LIMIT', 5000000);
        if ($dailyCount + $count > $dailyLimit) {
            Cache::put($this->circuitOpenKey, true, 3600); // Bloquear por 1 hora
            $msg = "SafeSync: Cuota diaria de Firebase cercana al límite de seguridad de {$dailyLimit} lecturas. Sistema protegido (Circuit Breaker Abierto).";
            \Illuminate\Support\Facades\Notification::route('mail', 'admin@safesure.com')->notify(new \App\Notifications\FirebaseAlertNotification('CIRCUIT BREAKER ACTIVADO', $msg));
            throw new \Exception($msg);
        }
    }

    /**
     * Registra escrituras y verifica presupuesto
     */
    protected function trackWrites(int $count)
    {
        if ($count <= 0) return;

        $this->writesInCurrentExecution += $count;

        if ($this->syncLog) {
            try {
                $this->syncLog->increment('firebase_writes', $count);
            } catch (\Throwable $e) {
                Log::warning("Failed to increment firebase_writes: " . $e->getMessage());
            }
        }
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
     * Prueba de conexión mínima (Pre-flight check)
     * Realiza una petición de 1 solo documento para validar credenciales y cuota.
     */
    public function ping(): bool
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) return false;

            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $response = $this->client->get("{$baseUrl}/afiliados?pageSize=1", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);
            
            return $response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            if (method_exists($e, 'getResponse') && $e->getResponse() && $e->getResponse()->getStatusCode() === 429) {
                Cache::put($this->circuitOpenKey, true, 3600); // Bloquear por 1 hora
            } elseif ($e->getCode() === 429) {
                Cache::put($this->circuitOpenKey, true, 3600); // Bloquear por 1 hora
            }
            Log::error("SafeSync Ping Failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets the exact total number of documents in a collection using runAggregationQuery.
     * Costs only 1 read flat. Extremely fast and cost-effective.
     */
    public function getCollectionCount(string $collectionName): int
    {
        if ($this->isCircuitOpen()) return 0;
        $token = $this->getAccessToken();
        if (!$token) return 0;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runAggregationQuery";
            
            $body = [
                'structuredAggregationQuery' => [
                    'structuredQuery' => [
                        'from' => [['collectionId' => $collectionName]]
                    ],
                    'aggregations' => [
                        [
                            'count' => new \stdClass(),
                            'alias' => 'total_count'
                        ]
                    ]
                ]
            ];

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $body
            ]);

            $this->trackReads(1); // Cuesta 1 sola lectura

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (is_array($data) && isset($data[0]['result']['aggregateFields']['total_count']['integerValue'])) {
                return (int)$data[0]['result']['aggregateFields']['total_count']['integerValue'];
            }

            return 0;
        } catch (\Throwable $e) {
            Log::error("Firebase runAggregationQuery count error: " . $e->getMessage());
            return 0;
        }
    }

    public function getCollectionBatched(string $collectionName, int $pageSize = 300, ?string $pageToken = null): array
    {
        if ($this->isCircuitOpen()) return ['data' => [], 'nextPageToken' => null];
        $token = $this->getAccessToken();
        if (!$token) return ['data' => [], 'nextPageToken' => null];

        $results = [];

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $query = "?pageSize={$pageSize}" . ($pageToken ? "&pageToken={$pageToken}" : "");
            
            try {
                $response = $this->client->get("{$baseUrl}/{$collectionName}{$query}", [
                    'headers' => ['Authorization' => "Bearer {$token}"]
                ]);
                $this->trackReads(1);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                if ($e->getResponse()->getStatusCode() == 429) {
                    Cache::put($this->circuitOpenKey, true, 3600);
                    throw new \Exception("SafeSync: Cuota excedida (429). El circuito se ha abierto.");
                }
                throw $e;
            }

            $data = json_decode($response->getBody()->getContents(), true);
            $data = is_array($data) ? $data : [];
            $batchCount = count($data['documents'] ?? []);
            $this->trackReads($batchCount); // Each returned document = 1 read (Firestore billing)

            foreach ($data['documents'] ?? [] as $doc) {
                $results[] = $this->mapFirestoreRestDoc($doc);
            }

            return [
                'data' => $results,
                'nextPageToken' => (isset($data) && is_array($data)) ? ($data['nextPageToken'] ?? null) : null
            ];

        } catch (\Throwable $e) {
            Log::error("Firebase Sync Error (GET $collectionName): " . $e->getMessage());
            return ['data' => $results, 'nextPageToken' => null];
        }
    }

    /**
     * Simple wrapper to get a collection's data without manual pagination.
     */
    public function getCollection(string $collectionName, int $limit = 500): array
    {
        $response = $this->getCollectionBatched($collectionName, $limit);
        return $response['data'];
    }

    /**
     * Trae un solo documento para resolución de conflictos.
     */
    public function getDocument(string $collectionName, string $documentId): ?array
    {
        if ($this->isCircuitOpen()) return null;
        $token = $this->getAccessToken();
        if (!$token) return null;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $response = $this->client->get("{$baseUrl}/{$collectionName}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);
            $this->trackReads(1);

            $data = json_decode($response->getBody()->getContents(), true);
            return $this->mapFirestoreRestDoc($data);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) return null; // No existe
            Log::error("Firebase Sync Error (GET DOC $documentId): " . $e->getMessage());
            return null;
        } catch (\Throwable $e) {
            Log::error("Firebase Sync Error (GET DOC $documentId): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Borra un documento de Firebase.
     */
    public function deleteDocument(string $collectionName, string $documentId): bool
    {
        $token = $this->getAccessToken();
        if (!$token) return false;

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            $this->client->delete("{$baseUrl}/{$collectionName}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"]
            ]);
            $this->trackWrites(1);
            return true;
        } catch (\Throwable $e) {
            Log::error("Firebase Delete Error ({$collectionName}/{$documentId}): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Sincronización Incremental Optimizada (Basada en Cursors)
     */
    public function getIncremental(string $collection, string $since, int $limit = 500, array $cursor = null): array
    {
        if ($this->isCircuitOpen()) return [];
        $token = $this->getAccessToken();
        if (!$token) return [];

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            // Convertir fecha a formato ISO Firebase
            $sinceDate = \Carbon\Carbon::parse($since)->toIso8601ZuluString('microsecond');

            $structuredQuery = [
                'from' => [['collectionId' => $collection]],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'updated_at'],
                        'op' => 'GREATER_THAN',
                        'value' => ['stringValue' => $sinceDate]
                    ]
                ],
                'orderBy' => [
                    ['field' => ['fieldPath' => 'updated_at'], 'direction' => 'ASCENDING']
                ],
                'limit' => $limit
            ];

            // Paginación basada en cursor si se proporciona
            if ($cursor) {
                $structuredQuery['startAt'] = [
                    'values' => [
                        ['stringValue' => $cursor['updated_at'] ?? ''],
                        // ['referenceValue' => $cursor['id']] // Opcional para desambiguar
                    ],
                    'before' => false // false = startAfter
                ];
            }

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $this->trackReads(1);

            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];
            $docCount = 0;
            
            foreach ($data as $entry) {
                if (isset($entry['document'])) {
                    $results[] = $this->mapFirestoreRestDoc($entry['document']);
                    $docCount++;
                }
            }
            
            // Track only actual document reads (not the query request itself)
            // Firestore billing = 1 read per returned document
            if ($docCount > 0) {
                $this->trackReads($docCount);
            }

            return $results;

        } catch (\Throwable $e) {
            if ($e->getCode() == 429) Cache::put($this->circuitOpenKey, true, 3600);
            Log::error("Firebase Incremental Sync Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene registros filtrados por campos específicos con soporte de lotes y paginación
     */
    public function getFilteredBatched(string $collection, array $filters, int $pageSize = 100, ?array $cursor = null): array
    {
        if ($this->isCircuitOpen()) return ['data' => [], 'cursor' => null];
        $token = $this->getAccessToken();
        if (!$token) return ['data' => [], 'cursor' => null];

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            $structuredQuery = [
                'from' => [['collectionId' => $collection]],
                'limit' => $pageSize
            ];

            $filterList = [];
            foreach ($filters as $field => $value) {
                if (is_int($value)) {
                    $filterList[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => 'EQUAL',
                            'value' => ['integerValue' => $value]
                        ]
                    ];
                } else {
                    $filterList[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => 'EQUAL',
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

            // Paginación por offset para evitar requerir índices compuestos en Firebase
            $offset = isset($cursor['offset']) ? (int)$cursor['offset'] : 0;
            if ($offset > 0) {
                $structuredQuery['offset'] = $offset;
            }

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $this->trackReads(1);
            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];

            if (is_array($data)) {
                foreach ($data as $entry) {
                    if (isset($entry['document'])) {
                        $mapped = $this->mapFirestoreRestDoc($entry['document']);
                        $results[] = $mapped;
                        $this->trackReads(1);
                    }
                }
            }

            return [
                'data' => $results,
                'cursor' => [
                    'offset' => $offset + count($results)
                ]
            ];

        } catch (\Throwable $e) {
            Log::error("Firebase Filtered Sync Error: " . $e->getMessage());
            return ['data' => [], 'cursor' => null];
        }
    }

    /**
     * Search documents using Structured Query (Incremental Sync)
     * Supports multiple filters (field => value)
     */
    public function search(string $collection, array $filters = [], string $orderBy = null): array
    {
        $token = $this->getAccessToken();
        if (!$token) return [];

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
            
            $structuredQuery = [
                'from' => [['collectionId' => $collection]],
            ];

            $filterList = [];
            $firstFilterField = null;

            foreach ($filters as $field => $value) {
                if ($firstFilterField === null) $firstFilterField = $field;

                if (is_bool($value)) {
                    $filterList[] = [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => 'EQUAL',
                            'value' => ['booleanValue' => $value]
                        ]
                    ];
                    } elseif (is_int($value)) {
                        $filterList[] = [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => $field],
                                'op' => 'EQUAL',
                                'value' => ['integerValue' => $value]
                            ]
                        ];
                    } else {
                        // Si el valor parece una fecha, usamos el formato ISO que usa Firebase
                        if (preg_match('/^\d{4}-\d{2}-\d{2}/', (string)$value)) {
                            $value = \Carbon\Carbon::parse($value)->toIso8601String();
                        }

                        $filterList[] = [
                            'fieldFilter' => [
                                'field' => ['fieldPath' => $field],
                                'op' => 'EQUAL',
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

            // En Firestore, el primer orderBy debe ser el mismo que el campo del filtro de rango
            $sortField = $orderBy ?? $firstFilterField;
            if ($sortField) {
                $structuredQuery['orderBy'] = [
                    ['field' => ['fieldPath' => $sortField], 'direction' => 'ASCENDING']
                ];
            }

            $response = $this->client->post($baseUrl, [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => ['structuredQuery' => $structuredQuery]
            ]);

            $this->trackReads(1); // Query overhead
            $data = json_decode($response->getBody()->getContents(), true);
            $results = [];

            if (is_array($data)) {
                foreach ($data as $item) {
                    if (isset($item['document'])) {
                        $results[] = $this->mapFirestoreRestDoc($item['document']);
                        $this->trackReads(1); // Document read
                    }
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
    /**
     * Circuit Breaker Logic: Evita llamadas si Firebase está fallando sistemáticamente.
     */
    protected function checkCircuitBreaker(): void
    {
        if (Cache::get('firebase_circuit_open')) {
            throw new \Exception("Firebase Circuit Breaker: El circuito está ABIERTO debido a fallos previos. Operación abortada.");
        }
    }

    protected function recordFailure(): void
    {
        $failures = Cache::increment('firebase_failure_count');
        if ($failures >= 5) {
            Cache::put('firebase_circuit_open', true, now()->addMinutes(5));
            Log::alert("FIREBASE CIRCUIT OPEN: Sincronización suspendida por 5 minutos.");
        }
    }

    protected function recordSuccess(): void
    {
        Cache::forget('firebase_failure_count');
    }

    public function syncData(array $data, string $collection, string $documentId)
    {
        $this->checkCircuitBreaker();
        $success = $this->push($collection, $documentId, $data);
        
        if ($success) {
            $this->recordSuccess();
        } else {
            $this->recordFailure();
        }
        
        return $success;
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
    public function push(string $collection, string $documentId, array $data, $model = null)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            if ($model) $this->markAsPending($model, "No se pudo obtener Access Token");
            return false;
        }

        // --- OPTIMIZACIÓN POR HASH (Senior Architecture) ---
        $currentHash = $this->calculateDataHash($data);

        if ($model && $model->last_sync_hash === $currentHash && $model->firebase_sync_status === 'synced') {
            // Ya está sincronizado con este contenido exacto. Saltamos escritura (Ahorro de Cuota).
            \Illuminate\Support\Facades\Cache::increment('firebase_sync_savings');
            return 'skipped';
        }

        try {
            $baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";
            
            // Añadimos el metadato de versión a Firebase para control externo
            if ($model) {
                $data['sync_version'] = $model->firebase_sync_version;
                $data['last_system_update'] = now()->toIso8601String();
            }

            $formattedData = ['fields' => $this->formatForFirestoreRest($data)];

            $this->client->patch("{$baseUrl}/{$collection}/{$documentId}", [
                'headers' => ['Authorization' => "Bearer {$token}"],
                'json' => $formattedData
            ]);

            $this->trackWrites(1);

            if ($model) $this->markAsSynced($model, $currentHash);
            return 'synced';

        } catch (\Throwable $e) {
            Log::error("Firebase Push Error ({$collection}/{$documentId}): " . $e->getMessage());
            if ($model) $this->markAsFailed($model, $e->getMessage());
            return 'failed';
        }
    }

    protected function markAsSynced($model, string $hash = null)
    {
        $model->updateQuietly([
            'firebase_sync_status' => 'synced',
            'firebase_synced_at' => now(),
            'last_sync_hash' => $hash,
            'firebase_error_log' => null,
            'conflict_status' => false
        ]);
    }

    protected function markAsFailed($model, string $error)
    {
        $model->updateQuietly([
            'firebase_sync_status' => 'error',
            'firebase_error_log' => substr($error, 0, 500),
            'conflict_status' => true
        ]);
    }

    protected function markAsPending($model, string $reason)
    {
        $model->updateQuietly([
            'firebase_sync_status' => 'pending',
            'firebase_error_log' => "Esperando reintento: $reason"
        ]);
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
     * Compares local model with remote data and updates local if remote is newer or different.
     */
    public function syncLocalModel($model, array $remoteData, bool $force = false): bool
    {
        $remoteUpdatedAt = isset($remoteData['firebase_updated_at_meta']) ? \Carbon\Carbon::parse($remoteData['firebase_updated_at_meta']) : 
                          (isset($remoteData['updated_at']) ? \Carbon\Carbon::parse($remoteData['updated_at']) : null);
        
        $localUpdatedAt = $model->firebase_updated_at ?? $model->updated_at;

        // --- PRE-FLIGHT VALIDATION (Error-Proofing) ---
        $required = ['nombre_completo', 'cedula'];
        foreach ($required as $field) {
            if (empty($remoteData[$field])) {
                Log::warning("SafeSync Validation Failed: Missing {$field} for document {$model->id}");
                $model->updateQuietly([
                    'firebase_sync_status' => 'error',
                    'firebase_error_log' => "Error de Validación: Campo '{$field}' ausente en Nube."
                ]);
                return false;
            }
        }

        // 1. Generar Hash de los datos remotos relevantes (Detección por Checksum)
        $remoteHash = $this->calculateDataHash($remoteData);

        // 2. Si el hash es idéntico, saltamos la actualización (Ahorro de CPU y DB)
        if (!$force && $model->last_sync_hash === $remoteHash) {
            return false;
        }

        // 3. Verificar si el dato remoto es realmente más reciente
        $remoteVersion = (int)($remoteData['firebase_sync_version'] ?? 0);
        $localVersion = (int)($model->firebase_sync_version ?? 0);

        // --- DETECCIÓN DE CONFLICTOS (Bidirectional Architecture) ---
        $isLocallyModified = ($model->firebase_sync_status === 'pending' || $model->firebase_sync_status === 'modified');
        $hasRemoteChanges = ($remoteVersion > $localVersion);

        if ($isLocallyModified && $hasRemoteChanges) {
            $model->conflict_status = true;
            $model->firebase_error_log = "⚠️ CONFLICTO DETECTADO: Cambios locales y remotos detectados simultáneamente.";
            Log::warning("SafeSync Conflict: Registro {$model->id} modificado en ambas fuentes.");
        }

        if ($force || $remoteVersion > $localVersion || ($remoteUpdatedAt && (!$localUpdatedAt || $remoteUpdatedAt->gt($localUpdatedAt)))) {
            
            // 4. Mapeo de campos descriptivos para transparencia UI
            if (isset($remoteData['estado_nombre'])) $remoteData['estado_nombre_remote'] = $remoteData['estado_nombre'];
            if (isset($remoteData['responsable_nombre'])) $remoteData['responsable_nombre_remote'] = $remoteData['responsable_nombre'];

            // 5. Preparar datos para el modelo
            unset($remoteData['firebase_id'], $remoteData['id'], $remoteData['firebase_updated_at_meta'], $remoteData['last_sync_hash']);
            $fillableData = array_intersect_key($remoteData, array_flip($model->getFillable()));
            
            $model->fill($fillableData);
            
            // Metadatos de sincronización SafeSync
            if ($model instanceof \App\Models\Afiliado) {
                $model->updated_from = 'firebase';
                if (empty($model->corte_id)) {
                    $model->corte_id = \App\Models\Corte::first()->id ?? 1;
                }
                if (empty($model->estado_id)) {
                    $model->estado_id = \App\Models\Estado::first()->id ?? 1;
                }
                if (is_null($model->conflict_status)) {
                    $model->conflict_status = false;
                }
            }
            $model->last_sync_hash = $remoteHash;
            $model->firebase_updated_at = $remoteUpdatedAt;
            $model->firebase_sync_version = $remoteVersion;
            $model->firebase_synced_at = now();
            $model->firebase_sync_status = 'synced';
            $model->last_sync_attempt_at = now();

            try {
                $model->saveQuietly(); 
                return true;
            } catch (\Throwable $e) {
                Log::error("SafeSync Save Error: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Maps a Firestore REST API document to a flat array, including system metadata.
     */
    protected function mapFirestoreRestDoc(array $doc): array
    {
        $fields = $doc['fields'] ?? [];
        $mapped = [];

        $nameParts = explode('/', $doc['name'] ?? '');
        $mapped['firebase_id'] = end($nameParts);
        
        // Capturar metadatos del sistema (Útil para sincronización incremental robusta)
        $mapped['firebase_updated_at_meta'] = $doc['updateTime'] ?? null;
        $mapped['firebase_created_at_meta'] = $doc['createTime'] ?? null;

        foreach ($fields as $key => $values) {
            $val = reset($values);
            $type = key($values);

            if ($type === 'integerValue') $val = (int)$val;
            if ($type === 'doubleValue') $val = (float)$val;
            if ($type === 'booleanValue') $val = (bool)$val;
            
            // Especial: Aplanado de objetos anidados (como estado => {id: 9})
            if (is_array($val)) {
                if (isset($val['id'])) {
                    $mapped[$key . '_id'] = $val['id'];
                }
                if (isset($val['nombre'])) {
                    $mapped[$key . '_nombre_remote'] = $val['nombre'];
                }
            }

            $mapped[$key] = $val;
        }

        return $mapped;
    }

    /**
     * Calcula un hash MD5 de los datos ignorando metadatos de sincronización volátiles.
     */
    public function calculateDataHash(array $data): string
    {
        $hashFields = $data;
        $toIgnore = [
            'firebase_id', 'id', 'firebase_sync_version', 'firebase_updated_at_meta', 
            'last_sync_hash', 'last_system_update', 'firebase_synced_at', 
            'updated_at', 'created_at', 'last_sync_attempt_at'
        ];
        
        foreach ($toIgnore as $field) {
            unset($hashFields[$field]);
        }
        
        return md5(json_encode($hashFields));
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
