<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class FirebaseRetrySync extends Command
{
    protected $signature = 'firebase:retry';
    protected $description = 'Busca registros pendientes o con error y reintenta la sincronización con Firebase';

    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle()
    {
        $this->info("Iniciando reintento de sincronización...");
        Log::info("Firebase Retry Sync: Iniciando proceso de reconciliación.");

        // 1. Procesar Empresas Pendientes
        $empresas = Empresa::whereIn('firebase_sync_status', ['pending', 'error', 'modified'])->limit(50)->get();
        $this->info("Procesando " . $empresas->count() . " empresas...");
        
        foreach ($empresas as $empresa) {
            $this->syncService->push('empresas', $empresa->uuid, $empresa->toArray(), $empresa);
        }

        // 2. Procesar Afiliados Pendientes
        $afiliados = Afiliado::whereIn('firebase_sync_status', ['pending', 'error', 'modified'])->limit(100)->get();
        $this->info("Procesando " . $afiliados->count() . " afiliados...");

        foreach ($afiliados as $afiliado) {
            // Usamos uuid para el documento en Firebase
            $this->syncService->push('afiliados', $afiliado->uuid, $afiliado->toArray(), $afiliado);
        }

        $this->info("Proceso completado.");
        Log::info("Firebase Retry Sync: Finalizado.");
    }
}
