<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\FirebaseSyncService;

$syncService = app(FirebaseSyncService::class);
$response = $syncService->getCollectionBatched('afiliados', 1);
if (!empty($response['data'])) {
    $first = $response['data'][0];
    echo "--- DETALLES DEL PRIMER REGISTRO DE AFILIADO EN FIREBASE ---\n";
    foreach ($first as $key => $val) {
        $type = gettype($val);
        if ($type === 'array') {
            echo "$key ($type): " . json_encode($val) . "\n";
        } else {
            echo "$key ($type): " . var_export($val, true) . "\n";
        }
    }
} else {
    echo "No records found.\n";
}
