<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['cache.default' => 'array']);
config(['database.default' => 'sqlite']);
config(['database.connections.sqlite.database' => database_path('database.sqlite')]);

try {
    echo "Columnas de la tabla afiliados:\n";
    $columns = \Illuminate\Support\Facades\DB::select("PRAGMA table_info(afiliados)");
    foreach ($columns as $c) {
        echo "- {$c->name} ({$c->type})\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
