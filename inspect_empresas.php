<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Empresa;

echo "--- LISTA DE ÚLTIMAS EMPRESAS ---" . PHP_EOL;
$empresas = Empresa::latest()->take(10)->get();

if ($empresas->isEmpty()) {
    echo "NO HAY EMPRESAS EN LA BASE DE DATOS LOCAL." . PHP_EOL;
}

foreach ($empresas as $e) {
    echo sprintf(
        "Nombre: %-30s | V: %d | R: %d | UUID: %s", 
        substr($e->nombre, 0, 30), 
        (int)$e->es_verificada, 
        (int)$e->es_real,
        $e->uuid ?? 'N/A'
    ) . PHP_EOL;
}

echo "--- TOTALES ---" . PHP_EOL;
echo "Verificadas: " . Empresa::where('es_verificada', 1)->count() . PHP_EOL;
echo "Reales: " . Empresa::where('es_real', 1)->count() . PHP_EOL;
