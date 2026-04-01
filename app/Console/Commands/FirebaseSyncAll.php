<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Services\FirebaseSyncService;

class FirebaseSyncAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:sync-all {--force : Forzar la subida de todos los registros}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza la base de datos local con Firebase Firestore (Diferencial)';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $syncService)
    {
        $force = $this->option('force');
        $this->info('🚀 Iniciando sincronización masiva con Firebase...');

        // 1. Sincronizar Empresas
        $queryEmpresas = Empresa::whereNotNull('rnc');
        if (!$force) {
            $queryEmpresas->where(function($q) {
                $q->whereNull('firebase_synced_at')
                  ->orWhereColumn('updated_at', '>', 'firebase_synced_at');
            });
        }

        $totalEmpresas = $queryEmpresas->count();
        if ($totalEmpresas > 0) {
            $this->info("- Sincronizando {$totalEmpresas} Empresas...");
            $bar = $this->output->createProgressBar($totalEmpresas);
            
            $queryEmpresas->chunk(200, function ($empresas) use ($syncService, $bar) {
                foreach ($empresas as $empresa) {
                    $documentId = preg_replace('/[^0-9]/', '', $empresa->rnc);
                    $syncService->syncModel($empresa, 'empresas', $documentId);
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine();
        } else {
            $this->comment("- Empresas: Nada nuevo que sincronizar.");
        }

        // 2. Sincronizar Afiliados
        $queryAfiliados = Afiliado::whereNotNull('cedula');
        if (!$force) {
            $queryAfiliados->where(function($q) {
                $q->whereNull('firebase_synced_at')
                  ->orWhereColumn('updated_at', '>', 'firebase_synced_at');
            });
        }

        $totalAfiliados = $queryAfiliados->count();
        if ($totalAfiliados > 0) {
            $this->info("- Sincronizando {$totalAfiliados} Afiliados...");
            $bar = $this->output->createProgressBar($totalAfiliados);
            
            $queryAfiliados->chunk(200, function ($afiliados) use ($syncService, $bar) {
                foreach ($afiliados as $afiliado) {
                    $documentId = preg_replace('/[^0-9]/', '', $afiliado->cedula);
                    $syncService->syncModel($afiliado, 'afiliados', $documentId);
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine();
        } else {
            $this->comment("- Afiliados: Nada nuevo que sincronizar.");
        }

        $this->info('✅ Sincronización diferencial finalizada correctamente.');
    }
}
