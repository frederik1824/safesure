<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class SyncToFirebaseJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $data;
    public $collection;
    public $documentId;
    
    // Si falla, reintentar 3 veces
    public $tries = 3;
    
    // Cola de alta prioridad
    public $queue = 'sync-high';
    
    // Esperar con backoff exponencial
    public $backoff = [10, 60, 300];

    /**
     * Create a new job instance.
     */
    public function __construct(array $data, string $collection, string $documentId)
    {
        $this->data = $data;
        $this->collection = $collection;
        $this->documentId = $documentId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseSyncService $syncService): void
    {
        $success = $syncService->syncData($this->data, $this->collection, $this->documentId);

        if (!$success) {
            Log::error("Firebase Sync Job Error: Falló al sincronizar {$this->collection}/{$this->documentId}");
            $this->release($this->backoff); // Reintenta el trabajo
        }
    }
}
