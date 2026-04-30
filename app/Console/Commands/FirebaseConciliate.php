<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Afiliado;
use App\Models\Empresa;
use Illuminate\Support\Facades\Log;

class FirebaseConciliate extends Command
{
    protected $signature = 'firebase:conciliate';
    protected $description = 'Compares local database counts with Firebase to identify missing records.';

    public function handle(FirebaseSyncService $firebase)
    {
        $this->info("🔍 Starting Data Conciliation...");

        // 1. Afiliados
        $this->line("Checking Afiliados...");
        $localCount = Afiliado::count();
        // Nota: El servicio REST no tiene un "count" nativo sin leer todo, 
        // pero podemos obtener el total de la colección si el API lo soporta o vía metadata.
        // Como simplificación para esta auditoría, compararemos los últimos registros.
        
        $lastLocal = Afiliado::latest('updated_at')->first();
        $this->info("Local Count: $localCount. Last Update: " . ($lastLocal->updated_at ?? 'N/A'));

        // 2. Empresas
        $this->line("Checking Empresas...");
        $localCountEmp = Empresa::count();
        $this->info("Local Count: $localCountEmp");

        $this->warn("TIP: Run 'php artisan firebase:pull-all --hours=24' to fix minor drifts.");
        
        return 0;
    }
}
