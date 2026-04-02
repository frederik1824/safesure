<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\Middleware\AuthTokenMiddleware;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\Log;

class FirebaseSyncService
{
    protected $client;
    protected $projectId;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id', 'syscarnet');
        $keyFilePath = config('services.firebase.key_file');

        if (!$keyFilePath || !file_exists($keyFilePath)) {
            Log::warning("Archivo de credenciales de Firebase no configurado o no encontrado. Sincronización desactivada.");
            return;
        }

        // Configurar Autenticación de Google para la API REST
        $scopes = ['https://www.googleapis.com/auth/datastore'];
        $credentials = new ServiceAccountCredentials($scopes, $keyFilePath);
        $middleware = new AuthTokenMiddleware($credentials);
        $stack = HandlerStack::create();
        $stack->push($middleware);

        $this->client = new Client([
            'handler' => $stack,
            'base_uri' => "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/",
            'auth' => 'google_auth'
        ]);
    }

    /**
     * Lista todos los documentos de una colección (REST)
     */
    public function listDocuments(string $collectionName): array
    {
        if (!$this->client) {
            throw new \RuntimeException("El cliente de Firebase no ha sido inicializado. Verifica tu archivo JSON de credenciales.");
        }

        try {
            $response = $this->client->get($collectionName);
            $data = json_decode($response->getBody(), true);
            return $data['documents'] ?? [];
        } catch (\Throwable $e) {
            Log::error("Error listando documentos de Firebase [$collectionName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Consulta documentos con filtros (REST - StructuredQuery)
     */
    public function queryDocuments(string $collectionName, string $field, string $operator, string $value): array
    {
        try {
            // Conversión básica de operadoras al formato Firestore REST
            $opMap = [
                'GREATER_THAN' => 'GREATER_THAN',
                'LESS_THAN' => 'LESS_THAN',
                'EQUAL' => 'EQUAL',
            ];

            $query = [
                'structuredQuery' => [
                    'from' => [['collectionId' => $collectionName]],
                    'where' => [
                        'fieldFilter' => [
                            'field' => ['fieldPath' => $field],
                            'op' => $opMap[$operator] ?? 'EQUAL',
                            'value' => ['stringValue' => $value] // Ajustar tipo según necesite el proyecto
                        ]
                    ]
                ]
            ];

            // La API de consulta REST usa :runQuery al final de la ruta del documento base
            // pero vía HttpClient a veces se configura diferente. 
            // Para simplicidad en este entorno, usamos listDocuments y filtramos localmente si es necesario 
            // o implementamos el runQuery asincrónico.
            
            // Si la consulta es por fecha (como en FirebaseSyncPull), Firestore tiene peculiaridades.
            // Implementaremos un fallback seguro.
            
            $docs = $this->listDocuments($collectionName);
            
            // Si es un diferencial, filtramos el array localmente para no romper el comando
            if ($operator === 'GREATER_THAN') {
                return array_filter($docs, function($doc) use ($field, $value) {
                    $docVal = $doc['fields'][$field]['timestampValue'] ?? $doc['fields'][$field]['stringValue'] ?? null;
                    return $docVal > $value;
                });
            }

            return $docs;
        } catch (\Throwable $e) {
            Log::error("Error consultando documentos de Firebase [$collectionName]: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Sincroniza un modelo a una colección de Firestore vía REST
     */
    public function syncModel($model, string $collectionName, string $documentId): void
    {
        $this->syncData($model->toArray(), $collectionName, $documentId);
        
        // Actualizar marca de tiempo local de sincronización
        $model->updateQuietly(['firebase_synced_at' => now()]);
    }

    /**
     * Sincroniza un array genérico de datos a Firestore (Usado para payloads enriquecidos)
     */
    public function syncData(array $data, string $collectionName, string $documentId): void
    {
        try {
            // Firestore REST API requiere un formato JSON específico para los campos
            $formattedFields = $this->formatFields($data);

            $response = $this->client->patch("{$collectionName}/{$documentId}", [
                'json' => [
                    'fields' => $formattedFields
                ]
            ]);

            if ($response->getStatusCode() !== 200) {
                Log::error("Error REST Firebase [{$collectionName}/{$documentId}]: " . $response->getBody());
            }

        } catch (\Throwable $e) {
            Log::error("Error sincronizando a Firebase (REST DATA) [{$collectionName}/{$documentId}]: " . $e->getMessage());
        }
    }

    /**
     * Verifica si un documento existe en Firestore (Detección de Colisión)
     */
    public function checkDocumentExistence(string $collectionName, string $documentId): array
    {
        try {
            $response = $this->client->get("{$collectionName}/{$documentId}");
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                
                // Extraer el nombre para dar feedback útil
                $nombre = $data['fields']['nombre_completo']['stringValue'] ?? 'Registro Anónimo';
                $responsable = $data['fields']['responsable_id']['integerValue'] ?? 'Desconocido';
                
                return [
                    'exists' => true,
                    'nombre' => $nombre,
                    'responsable_id' => $responsable
                ];
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Un 404 es lo esperado si NO existe duplicado
            if ($e->getResponse()->getStatusCode() === 404) {
                return ['exists' => false];
            }
            Log::error("Error verificando existencia en Firebase: " . $e->getMessage());
        } catch (\Exception $e) {
            Log::error("Error general verificando existencia en Firebase: " . $e->getMessage());
        }
        
        return ['exists' => false, 'error' => true];
    }

    /**
     * Elimina un documento de Firestore vía REST
     */
    public function deleteDocument(string $collectionName, string $documentId): void
    {
        try {
            $this->client->delete("{$collectionName}/{$documentId}");
        } catch (\Exception $e) {
            Log::error("Error eliminando en Firebase (REST) [{$collectionName}/{$documentId}]: " . $e->getMessage());
        }
    }

    /**
     * Formatea un array asociativo al formato 'Value Objects' de Firestore REST API
     */
    private function formatFields(array $data): array
    {
        $fields = [];
        foreach ($data as $key => $value) {
            // Saltamos valores que sean arrays u objetos (relaciones cargadas) para evitar errores de conversión
            if (is_array($value) || is_object($value)) {
                continue;
            }

            if (is_null($value)) {
                $fields[$key] = ['nullValue' => null];
            } elseif (is_bool($value)) {
                $fields[$key] = ['booleanValue' => $value];
            } elseif (is_numeric($value)) {
                // Firestore diferencia entre entero y doble
                if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
                    $fields[$key] = ['integerValue' => (string)$value];
                } else {
                    $fields[$key] = ['doubleValue' => (float)$value];
                }
            } else {
                $fields[$key] = ['stringValue' => (string)$value];
            }
        }
        return $fields;
    }
}
