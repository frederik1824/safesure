<?php
include 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$sync = new FirebaseSyncService();
echo "--- Iniciando Inspección de Firebase ---\n";

$afiliados = $sync->getCollection('afiliados');
echo "Total de registros encontrados en Firebase: " . count($afiliados) . "\n";

if (count($afiliados) > 0) {
    echo "\nMuestra del primer registro:\n";
    print_r(array_slice($afiliados[0], 0, 15));
    
    $estadosEncontrados = [];
    foreach ($afiliados as $af) {
        $estado = $af['estado'] ?? $af['estado_id'] ?? 'N/A';
        $estadosEncontrados[$estado] = ($estadosEncontrados[$estado] ?? 0) + 1;
    }
    
    echo "\nResumen de Estados en Firebase:\n";
    print_r($estadosEncontrados);
} else {
    echo "❌ No se encontraron registros. Verifica el FIREBASE_PROJECT_ID en el .env de producción.\n";
}
