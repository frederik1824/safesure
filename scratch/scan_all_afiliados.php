<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['cache.default' => 'array']);
config(['database.default' => 'sqlite']);

try {
    $service = new \App\Services\FirebaseSyncService();
    
    echo "Escaneando hasta 2,000 afiliados desde Firebase...\n";
    $docs = [];
    $pageToken = null;
    $totalScanned = 0;
    
    for ($i = 0; $i < 20; $i++) {
        $response = $service->getCollectionBatched('afiliados', 100, $pageToken);
        $batch = $response['data'] ?? [];
        if (empty($batch)) break;
        
        $docs = array_merge($docs, $batch);
        $totalScanned += count($batch);
        echo "Descargados $totalScanned documentos...\n";
        
        $pageToken = $response['nextPageToken'] ?? null;
        if (!$pageToken) break;
    }
    
    echo "Total descargados: " . count($docs) . " documentos.\n";
    
    $sexCount = 0;
    $dobCount = 0;
    $dobKeysFound = [];
    $sexKeysFound = [];
    
    foreach ($docs as $doc) {
        foreach ($doc as $key => $val) {
            $lowerKey = strtolower($key);
            if (str_contains($lowerKey, 'sex') || str_contains($lowerKey, 'gen')) {
                if ($val !== null && $val !== '') {
                    $sexCount++;
                    $sexKeysFound[$key] = ($sexKeysFound[$key] ?? 0) + 1;
                }
            }
            if (str_contains($lowerKey, 'nac') || str_contains($lowerKey, 'dob') || str_contains(strtolower($key), 'birth') || str_contains(strtolower($key), 'edad')) {
                if ($val !== null && $val !== '') {
                    $dobCount++;
                    $dobKeysFound[$key] = ($dobKeysFound[$key] ?? 0) + 1;
                }
            }
        }
    }
    
    echo "\n=== Campos de Sexo Encontrados ===\n";
    foreach ($sexKeysFound as $k => $c) {
        echo "- $k: $c veces con valor\n";
    }
    
    echo "\n=== Campos de Nacimiento/Edad Encontrados ===\n";
    foreach ($dobKeysFound as $k => $c) {
        echo "- $k: $c veces con valor\n";
    }
    
    echo "\nTotal de registros con sexo con valor: $sexCount\n";
    echo "Total de registros con nacimiento con valor: $dobCount\n";
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
