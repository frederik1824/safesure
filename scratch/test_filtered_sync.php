<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$syncService = app(FirebaseSyncService::class);
try {
    echo "Testing runQuery with offset and limit (filter by responsable_id = 2)...\n";
    $reflection = new ReflectionClass($syncService);
    $tokenMethod = $reflection->getMethod('getAccessToken');
    $tokenMethod->setAccessible(true);
    $token = $tokenMethod->invoke($syncService);
    
    $client = new \GuzzleHttp\Client();
    $baseUrl = "https://firestore.googleapis.com/v1/projects/syscarnet/databases/(default)/documents:runQuery";
    
    $structuredQuery = [
        'from' => [['collectionId' => 'afiliados']],
        'limit' => 2,
        'offset' => 2,
        'where' => [
            'fieldFilter' => [
                'field' => ['fieldPath' => 'responsable_id'],
                'op' => 'EQUAL',
                'value' => ['integerValue' => 2]
            ]
        ]
    ];
    
    $response = $client->post($baseUrl, [
        'headers' => ['Authorization' => "Bearer {$token}"],
        'json' => ['structuredQuery' => $structuredQuery]
    ]);
    
    echo "Success!\n";
    $data = json_decode($response->getBody()->getContents(), true);
    echo "Count returned: " . count($data) . "\n";
    foreach ($data as $i => $entry) {
        if (isset($entry['document'])) {
            $mapped = $syncService->mapDataHashOnlyForVerification($entry['document']); // Wait, let's just print the name
            echo "Record " . ($i+1) . ": " . ($entry['document']['fields']['nombre_completo']['stringValue'] ?? 'N/A') . "\n";
        }
    }
    
} catch (\GuzzleHttp\Exception\ClientException $e) {
    echo "Client Exception Caught!\n";
    echo "Status Code: " . $e->getResponse()->getStatusCode() . "\n";
    echo "Error Body:\n";
    echo $e->getResponse()->getBody()->getContents() . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
