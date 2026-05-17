<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$firebase = new App\Services\FirebaseSyncService();
$data = $firebase->getCollectionBatched('afiliados', 1);
print_r($data);
