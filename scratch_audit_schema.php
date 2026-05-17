<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Services\FirebaseSyncService;
use App\Models\Afiliado;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Auditoría de Estructura Safesure Nexus ---\n";

try {
    $syncService = app(FirebaseSyncService::class);
    $projectId = env('FIREBASE_PROJECT_ID');
    
    // Obtenemos el token
    $reflection = new ReflectionClass($syncService);
    $method = $reflection->getMethod('getAccessToken');
    $method->setAccessible(true);
    $token = $method->invoke($syncService);

    $client = new \GuzzleHttp\Client();
    $baseUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/afiliados?pageSize=1";
    
    $response = $client->get($baseUrl, [
        'headers' => ['Authorization' => "Bearer {$token}"]
    ]);
    
    $data = json_decode($response->getBody(), true);
    
    if (!isset($data['documents'][0])) {
        echo "❌ No se encontraron documentos en Firebase para auditar.\n";
        exit;
    }

    $remoteFields = array_keys($data['documents'][0]['fields']);
    $localFields = (new Afiliado())->getFillable();

    echo "Campos en Firebase: " . count($remoteFields) . "\n";
    echo "Campos locales configurados: " . count($localFields) . "\n\n";

    $missingLocally = array_diff($remoteFields, $localFields);
    
    if (empty($missingLocally)) {
        echo "✅ ¡Perfecto! El esquema local coincide plenamente con Firebase.\n";
    } else {
        echo "⚠️  Atención: Se detectaron campos en Firebase que NO están en tu modelo local:\n";
        foreach ($missingLocally as $field) {
            echo "   - {$field}\n";
        }
        echo "\nNota: Estos campos se ignorarán de forma segura durante el Pull a menos que los agregues al modelo.\n";
    }

} catch (\Exception $e) {
    echo "❌ Error en auditoría: " . $e->getMessage() . "\n";
}
