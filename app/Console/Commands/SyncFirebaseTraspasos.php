<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Traspaso;
use App\Models\CloudSyncCheckpoint;
use App\Models\FirebaseSyncLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SyncFirebaseTraspasos extends Command
{
    protected $signature = 'firebase:sync-traspasos 
                            {--full : Fuerza una carga completa ignorando checkpoints} 
                            {--log-id= : ID de log para seguimiento UI}';
    
    protected $description = 'Módulo Receptor de Traspasos: Sincronización Incremental Optimizada desde Firebase.';

    protected $syncLog;
    protected $globalSynced = 0;
    protected $added = 0;
    protected $updated = 0;
    protected $skipped = 0;
    protected $failed = 0;
    protected $batchSize = 100;

    public function handle(FirebaseSyncService $firebase)
    {
        // Pre-vuelo: Circuit Breaker Check
        if ($firebase->isCircuitOpen()) {
            $this->error("❌ SafeSync: El circuito está abierto. Firebase ha bloqueado las peticiones temporalmente.");
            return 1;
        }

        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        DB::disableQueryLog();

        $lock = Cache::lock('firebase_sync_traspasos_lock', 7200);
        if (!$lock->get()) {
            $this->warn("⚠️ Ya hay una sincronización de traspasos activa.");
            return 0;
        }

        // Vincular con el log de la UI si se proporciona
        if ($this->option('log-id')) {
            $this->syncLog = FirebaseSyncLog::find($this->option('log-id'));
            if ($this->syncLog) {
                $this->syncLog->update(['status' => 'in_progress']);
                $firebase->setSyncLog($this->syncLog);
            }
        } else {
            // Crear un log interno si no se pasó uno para trazabilidad
            $this->syncLog = FirebaseSyncLog::create([
                'user_id' => auth()->id(),
                'type' => $this->option('full') ? 'full' : 'incremental',
                'status' => 'started',
                'started_at' => now(),
                'message' => 'Sincronización de Traspasos iniciada.'
            ]);
            $firebase->setSyncLog($this->syncLog);
        }

        $startedAt = now();

        try {
            $this->info("🚀 Iniciando SafeSync Receptor de Traspasos...");

            // 1. Obtener o Crear Checkpoint
            $checkpoint = CloudSyncCheckpoint::firstOrCreate(
                ['process_name' => 'traspasos'],
                [
                    'sync_type' => 'incremental',
                    'status' => 'idle',
                    'records_processed' => 0,
                    'records_synced' => 0
                ]
            );

            $since = null;
            if (!$this->option('full')) {
                $since = $checkpoint->last_firebase_updated_at ? $checkpoint->last_firebase_updated_at->toDateTimeString() : null;
            }

            $checkpoint->update(['status' => 'running', 'started_at' => now(), 'error_message' => null]);

            // Actualizar total estimado si es Reconstrucción Total
            if ($this->option('full')) {
                try {
                    $exactCloudCount = $firebase->getCollectionCount('traspasos');
                    if ($exactCloudCount > 0 && $this->syncLog) {
                        $this->syncLog->update(['total_records' => $exactCloudCount]);
                    }
                } catch (\Throwable $e) {
                    Log::warning("SafeSync Traspasos count failed: " . $e->getMessage());
                }
            }

            $processedInThisSession = 0;
            $hasMore = true;
            $cursor = [];
            $latestFirebaseUpdatedAt = $checkpoint->last_firebase_updated_at;

            while ($hasMore) {
                // Verificar Circuit Breaker antes de cada lote
                if ($firebase->isCircuitOpen()) throw new \Exception("Cuota de Firebase agotada durante el proceso.");

                // Verificar señal de parada manual
                if (Cache::has('firebase_sync_stop')) {
                    throw new \Exception("Sincronización cancelada por el usuario desde el Dashboard.");
                }

                $this->comment("   Descargando lote de traspasos (Desde: " . ($since ?? 'Inicio') . ")...");
                
                $data = [];
                if ($since) {
                    $data = $firebase->getIncremental('traspasos', $since, $this->batchSize, $cursor);
                } else {
                    $response = $firebase->getCollectionBatched('traspasos', $this->batchSize, $cursor['nextPageToken'] ?? null);
                    if (!$response || !isset($response['data'])) {
                        $hasMore = false;
                        continue;
                    }
                    $data = $response['data'] ?? [];
                    $cursor['nextPageToken'] = $response['nextPageToken'] ?? null;
                }

                if (empty($data)) {
                    $hasMore = false;
                    continue;
                }

                // Ajuste dinámico del total en el log
                $hasMorePageToken = isset($cursor['nextPageToken']) && $cursor['nextPageToken'];
                if ($this->syncLog && ($this->globalSynced + count($data)) > $this->syncLog->total_records) {
                    $this->syncLog->update(['total_records' => $this->globalSynced + count($data) + ($hasMorePageToken ? 500 : 0)]);
                }

                foreach ($data as $mapped) {
                    // Verificación de parada inmediata
                    if ($this->globalSynced % 2 === 0 && Cache::has('firebase_sync_stop')) {
                        throw new \Exception("Sincronización cancelada por el usuario (Interrupción Inmediata).");
                    }

                    if (!$mapped || !is_array($mapped)) {
                        continue;
                    }

                    $docId = $mapped['firebase_id'] ?? null;
                    if (!$docId) {
                        $this->failed++;
                        continue;
                    }

                    try {
                        $firebaseUpdatedAtStr = $mapped['updated_at'] ?? $mapped['firebase_updated_at_meta'] ?? null;
                        $firebaseUpdatedAt = $firebaseUpdatedAtStr ? Carbon::parse($firebaseUpdatedAtStr) : null;

                        // Mantener el registro de la última fecha de actualización remota
                        if ($firebaseUpdatedAt && (!$latestFirebaseUpdatedAt || $firebaseUpdatedAt->gt($latestFirebaseUpdatedAt))) {
                            $latestFirebaseUpdatedAt = $firebaseUpdatedAt;
                        }

                        // Mapeo e Ingesta Atómica
                        DB::transaction(function() use ($docId, $mapped, $firebaseUpdatedAt, &$added, &$updated, &$skipped) {
                            $traspaso = Traspaso::where('firebase_document_id', $docId)->first();

                            $estado = strtoupper($mapped['estado'] ?? 'EFECTIVO');
                            $statusUnipago = strtoupper($mapped['status_unipago'] ?? '');
                            if (empty($statusUnipago)) {
                                if ($estado === 'RECHAZADO' || $estado === 'RECHAZADA') {
                                    $statusUnipago = 'RECHAZADO';
                                } elseif ($estado === 'EN PROCESO') {
                                    $statusUnipago = 'PENDIENTE';
                                } else {
                                    $statusUnipago = 'APROBADO';
                                }
                            }

                            $dataArray = [
                                'nombre_afiliado' => strtoupper($mapped['nombre_afiliado'] ?? 'JUAN PEREZ'),
                                'cedula_afiliado' => preg_replace('/[^0-9]/', '', $mapped['cedula_afiliado'] ?? ''),
                                'agente' => strtoupper($mapped['agente'] ?? 'SISTEMA'),
                                'estado' => $estado,
                                'cantidad_dependientes' => isset($mapped['cantidad_dependientes']) ? (int)$mapped['cantidad_dependientes'] : 0,
                                'fecha_solicitud' => isset($mapped['fecha_solicitud']) ? Carbon::parse($mapped['fecha_solicitud'])->toDateString() : null,
                                'fecha_efectivo' => isset($mapped['fecha_efectivo']) ? Carbon::parse($mapped['fecha_efectivo'])->toDateString() : null,
                                'periodo' => $mapped['periodo'] ?? null,
                                'status_unipago' => $statusUnipago,
                                'sync_status' => 'synced',
                                'firebase_updated_at' => $firebaseUpdatedAt,
                                'synced_at' => now(),
                                'source_system' => 'CMD',
                                'local_updated_at' => now()
                            ];

                            if ($traspaso) {
                                // Omitir si ya está al día basándonos en la marca de tiempo de Firebase
                                if ($traspaso->firebase_updated_at && $firebaseUpdatedAt && $traspaso->firebase_updated_at->eq($firebaseUpdatedAt)) {
                                    $skipped++;
                                    return;
                                }

                                $traspaso->update($dataArray);
                                $updated++;
                            } else {
                                $dataArray['firebase_document_id'] = $docId;
                                Traspaso::create($dataArray);
                                $added++;
                            }
                        });

                        $this->globalSynced++;
                        
                        // Enviar actualizaciones al log en tiempo real
                        if ($this->syncLog && $this->globalSynced % 5 === 0) {
                            $this->syncLog->update([
                                'records_synced' => $this->globalSynced,
                                'records_added' => $this->added,
                                'records_updated' => $this->updated,
                                'records_skipped' => $this->skipped,
                                'last_heartbeat_at' => now()
                            ]);
                        }

                    } catch (\Throwable $e) {
                        $this->failed++;
                        Log::error("Traspaso Sync Document Error ($docId): " . $e->getMessage());
                    }
                }

                // Si no hay más páginas para paginar, terminar loop
                if (empty($since) && !$hasMorePageToken) {
                    $hasMore = false;
                }

                // Si es incremental, el método getIncremental devuelve una lista plana sin token,
                // si la cantidad de registros devueltos es menor que batchSize, significa que no hay más.
                if ($since && count($data) < $this->batchSize) {
                    $hasMore = false;
                }

                // Actualizar cursor para siguiente iteración incremental
                if ($since && !empty($data)) {
                    $lastItem = end($data);
                    $cursor = ['updated_at' => $lastItem['updated_at'] ?? ''];
                }
            }

            // Actualizar Checkpoint Exitoso
            $checkpoint->update([
                'status' => 'completed',
                'last_successful_sync_at' => now(),
                'last_firebase_updated_at' => $latestFirebaseUpdatedAt,
                'records_processed' => $this->globalSynced,
                'records_synced' => $this->globalSynced,
                'records_failed' => $this->failed,
                'finished_at' => now(),
                'duration_seconds' => (int) max(0, round(now()->diffInSeconds($startedAt)))
            ]);

            // Finalizar Log
            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'completed',
                    'records_synced' => $this->globalSynced,
                    'records_added' => $this->added,
                    'records_updated' => $this->updated,
                    'records_skipped' => $this->skipped,
                    'message' => "Sincronización de traspasos completada con éxito. Agregados: {$this->added}, Modificados: {$this->updated}, Omitidos: {$this->skipped}, Fallidos: {$this->failed}",
                    'completed_at' => now()
                ]);
            }

            $this->info("✅ SafeSync Traspasos completado. Total: {$this->globalSynced}");
            $lock->release();
            return 0;

        } catch (\Throwable $e) {
            $lock->release();
            
            Log::error("CRITICAL: Sincronización de traspasos falló. " . $e->getMessage());

            if (isset($checkpoint)) {
                $checkpoint->update([
                    'status' => 'failed',
                    'error_message' => substr($e->getMessage(), 0, 500),
                    'finished_at' => now()
                ]);
            }

            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'failed',
                    'message' => 'Error: ' . $e->getMessage(),
                    'completed_at' => now()
                ]);
            }

            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }
}
