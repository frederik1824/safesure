<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firebase = new App\Services\FirebaseSyncService();
$data = $firebase->getCollectionBatched('afiliados', 50);

$stats = [];
foreach($data['data'] as $doc) {
    $eid = $doc['estado_id'] ?? 'NULL';
    $stats[$eid] = ($stats[$eid] ?? 0) + 1;
    if ($eid == 9) {
        print_r($doc);
    }
}
print_r($stats);
