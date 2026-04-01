<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Observers\AfiliadoObserver;
use App\Services\FirebaseSyncService;

class SyncFirebaseAfiliado extends Command
{
    /**
     * @var string
     */
    protected $signature = 'firebase:sync-afiliado {cedula}';

    /**
     * @var string
     */
    protected $description = 'Sincroniza manualmente un afiliado con Firestore usando su cédula';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cedula = $this->argument('cedula');
        
        // Limpiar cédula para búsqueda
        $cleanCedula = preg_replace('/[^0-9]/', '', $cedula);
        
        // Buscar afiliado (probando con guiones y sin guiones)
        $afiliado = Afiliado::whereRaw("REPLACE(cedula, '-', '') = ?", [$cleanCedula])->first();

        if (!$afiliado) {
            $this->error("Afiliado con cédula {$cedula} no encontrado.");
            return 1;
        }

        $this->info("Sincronizando a {$afiliado->nombre_completo}...");

        try {
            // Instanciar el observer de forma manual para disparar la misma lógica EXACTA
            $observer = app(AfiliadoObserver::class);
            $observer->saved($afiliado);
            
            $this->info("¡Éxito! El registro de '{$afiliado->nombre_completo}' ha sido enviado a Firestore (ID: {$cleanCedula}).");
        } catch (\Exception $e) {
            $this->error("Error durante la sincronización: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
