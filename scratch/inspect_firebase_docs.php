<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['cache.default' => 'array']);
config(['database.default' => 'sqlite']);

try {
    $service = new \App\Services\FirebaseSyncService();
    
    echo "Buscando los afiliados más recientemente actualizados en Firebase...\n";
    // Usamos el método search() con orderBy updated_at
    $docs = $service->search('afiliados', [], 'updated_at');
    // Invertimos para obtener los más recientes si es ascendente
    $docs = array_reverse($docs);
    
    echo "Se descargaron " . count($docs) . " documentos recientes.\n";
    
    $allKeys = [];
    $interestingFields = [];
    
    foreach ($docs as $doc) {
        foreach ($doc as $key => $val) {
            $allKeys[$key] = true;
            
            $lowerKey = strtolower($key);
            if (
                str_contains($lowerKey, 'fecha') || 
                str_contains($lowerKey, 'date') || 
                str_contains($lowerKey, 'time') || 
                str_contains($lowerKey, 'nac') || 
                str_contains($lowerKey, 'birth') || 
                str_contains($lowerKey, 'sex') || 
                str_contains($lowerKey, 'genero') ||
                str_contains($lowerKey, 'gen') ||
                str_contains($lowerKey, 'soli') ||
                str_contains($lowerKey, 'dep')
            ) {
                if ($val !== null && $val !== '') {
                    $interestingFields[$key][] = $val;
                }
            }
        }
    }
    
    echo "\n=== Todos los campos únicos encontrados en registros recientes ===\n";
    foreach (array_keys($allKeys) as $k) {
        echo "- $k\n";
    }
    
    echo "\n=== Valores de campos interesantes encontrados ===\n";
    foreach ($interestingFields as $key => $vals) {
        $uniqueVals = array_unique($vals);
        echo "- $key: count=" . count($vals) . ", unique_examples=" . implode(', ', array_slice($uniqueVals, 0, 5)) . "\n";
    }
    
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
