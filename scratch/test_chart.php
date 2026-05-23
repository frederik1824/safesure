<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Traspaso;

$allCount = Traspaso::count();
echo "Total traspasos in DB: " . $allCount . "\n";

$trend = Traspaso::selectRaw("
    DATE_FORMAT(fecha_solicitud, '%Y-%m') as period,
    COUNT(*) as total_transfers,
    SUM(CASE WHEN estado = 'EFECTIVO' THEN 1 ELSE 0 END) as effective_transfers,
    SUM(cantidad_dependientes) as total_dependents
")
->whereNotNull('fecha_solicitud')
->where('fecha_solicitud', '>=', now()->subMonths(6)->startOfMonth())
->groupBy('period')
->orderBy('period')
->get();

echo "Trend results count: " . $trend->count() . "\n";
print_r($trend->toArray());
