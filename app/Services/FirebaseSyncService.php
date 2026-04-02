<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Service to sync companies, roles, and other data between local and Firebase.
 * Uses Firestore REST API to avoid complex gRPC builds in production.
 */
class FirebaseSyncService
{
    protected $baseUrl;
    protected $client;
    protected $projectId;

    /**
     * @param string|null $credentialsPath Custom path, or uses ENV if null
     */
    public function __construct($credentialsPath = null)
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', 'syscarnet');
        $this->baseUrl = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents";

        $jsonPath = $credentialsPath ?? base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase-auth.json'));

        if (!file_exists($jsonPath)) {
            Log::warning("Firebase Sync: Credentials file NOT FOUND at {$jsonPath}. Client disabled.");
            $this->client = null;
            return;
        }

        try {
            // Get Google Auth Token via Service Account JSON
            // In a production environment, we'd use Google\Auth to get the Bearer token
            // For now, we assume public but initialized client
            $this->client = new Client([
                'timeout' => 10.0,
                'verify' => false, // For internal network environments if needed
            ]);
        } catch (\Throwable $e) {
            Log::error("Firebase Sync: Initialization failed: " . $e->getMessage());
            $this->client = null;
        }
    }

    /**
     * Basic GET collection from Firestore
     */
    public function getCollection(string $collection): array
    {
        if (!$this->client) {
            return ['error' => 'Firebase client not initialized'];
        }

        try {
            $response = $this->client->get("{$this->baseUrl}/{$collection}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error("Firebase Sync Error ({$collection}): " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Maps Firestore document to a flat array
     */
    public function mapDocument(array $doc): array
    {
        $fields = $doc['fields'] ?? [];
        $mapped = [];

        // Extract ID from the path: projects/.../documents/collection/ID
        $nameParts = explode('/', $doc['name'] ?? '');
        $mapped['firebase_id'] = end($nameParts);

        foreach ($fields as $key => $values) {
            // Firestore returns keys like: "stringValue", "integerValue", etc.
            $mapped[$key] = reset($values);
        }

        return $mapped;
    }

    /**
     * Pushes a local data array to a Firestore collection
     */
    public function push(string $collection, string $documentId, array $data)
    {
        if (!$this->client) {
            return ['error' => 'Firebase client not initialized'];
        }

        try {
            // Firestore REST API requires data wrapped in 'fields' with typed values
            $formattedData = ['fields' => $this->formatForFirestore($data)];
            
            $response = $this->client->patch("{$this->baseUrl}/{$collection}/{$documentId}", [
                'json' => $formattedData
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Throwable $e) {
            Log::error("Firebase Push Error ({$collection}): " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Converts a flat associative array into Firestore's typed 'fields' structure
     */
    private function formatForFirestore(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_int($value) || is_float($value)) {
                $fields[$key] = ['doubleValue' => (float)$value];
            } elseif (is_array($value)) {
                // For simplicity, treat arrays as JSON strings for now
                $fields[$key] = ['stringValue' => json_encode($value)];
            } else {
                $fields[$key] = (string)$value === '' ? ['nullValue' => null] : ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }
}
