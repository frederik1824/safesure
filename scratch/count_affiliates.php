<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Afiliado;
use Illuminate\Support\Facades\DB;

$totals = Afiliado::withoutGlobalScopes()
    ->select('responsable_id', DB::raw('count(*) as total'))
    ->groupBy('responsable_id')
    ->get();

echo "--- TOTALES DE AFILIADOS LOCALES POR RESPONSABLE ---\n";
foreach ($totals as $t) {
    echo "Responsable ID: " . ($t->responsable_id ?? 'NULL') . " | Total: " . $t->total . "\n";
}
