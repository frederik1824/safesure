<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class DeleteFromFirebaseJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $collection;
    public $documentId;
    
    public $tries = 3;
    public $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(string $collection, string $documentId)
    {
        $this->collection = $collection;
        $this->documentId = $documentId;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseSyncService $syncService): void
    {
        $success = $syncService->deleteDocument($this->collection, $this->documentId);

        if (!$success) {
            Log::error("Firebase Sync Job Error: Falló al eliminar {$this->collection}/{$this->documentId}");
            $this->release($this->backoff);
        }
    }
}
