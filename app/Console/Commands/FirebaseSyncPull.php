<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\CloudSyncCheckpoint;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FirebaseSyncPull extends Command
{
    protected $signature = 'firebase:pull-all 
                            {--full : Fuerza una carga completa ignorando checkpoints} 
                            {--hours= : Sincronización incremental de las últimas N horas}
                            {--log-id= : ID de log para seguimiento UI}
                            {--collection= : Colección específica (afiliados, empresas)}
                            {--responsable-id= : Filtrar solo por este ID de responsable}';
    
    protected $description = 'Sincronización Cloud Resiliente con Control de Cuota y Checkpoints.';
    
    protected $syncLog;
    protected $globalSynced = 0;
    protected $added = 0;
    protected $updated = 0;
    protected $skipped = 0;
    protected $failed = 0;
    protected $batchSize = 100; // Reducido para mayor sensibilidad al botón detener

    public function handle(FirebaseSyncService $firebase)
    {
        // 0. Pre-vuelo: Circuit Breaker Check
        if ($firebase->isCircuitOpen()) {
            $this->error("❌ SafeSync: El circuito está abierto. Firebase ha bloqueado las peticiones temporalmente.");
            return 1;
        }

        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        DB::disableQueryLog();

        $lock = Cache::lock('firebase_sync_lock', 7200);
        if (!$lock->get()) {
            $this->warn("⚠️ Ya hay una sincronización activa.");
            return 0;
        }

        // Vincular con el log de la UI si se proporciona
        if ($this->option('log-id')) {
            $this->syncLog = \App\Models\FirebaseSyncLog::find($this->option('log-id'));
            if ($this->syncLog) {
                $this->syncLog->update(['status' => 'in_progress']);
                $firebase->setSyncLog($this->syncLog);
            }
        }

        try {
            $this->info("🚀 Iniciando SafeSync Nexus v2...");
            
            // --- PRUEBA DE VUELO (PRE-FLIGHT) ---
            $this->info("🔍 Realizando prueba de conexión con Firebase...");
            if (!$firebase->ping()) {
                throw new \Exception("La prueba de conexión con Firebase falló. Verifique credenciales o cuota agotada.");
            }
            $this->info("✅ Conexión exitosa. Iniciando motor masivo.");
            // 1. Sincronizar Catálogos Críticos (Pequeños, sin checkpoints)
            $this->syncStaticCatalog($firebase);

            // 2. Sincronizar Colecciones Masivas con Checkpoints
            $target = $this->option('collection');

            if (!$target || $target === 'empresas') {
                $this->safeSyncProcess($firebase, 'empresas', Empresa::class, 'uuid');
            }
            
            if (!$target || $target === 'afiliados') {
                $filters = [];
                if ($this->option('responsable-id')) {
                    $filters['responsable_id'] = (int)$this->option('responsable-id');
                }
                $this->safeSyncProcess($firebase, 'afiliados', Afiliado::class, 'cedula', $filters);
            }

            $this->info("✅ SafeSync completado exitosamente.");
            
            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'completed',
                    'records_synced' => $this->globalSynced,
                    'records_added' => $this->added,
                    'records_updated' => $this->updated,
                    'records_skipped' => $this->skipped,
                    'records_failed' => $this->failed,
                    'completed_at' => now(),
                    'message' => 'Sincronización finalizada correctamente.'
                ]);
            }

            \Illuminate\Support\Facades\Cache::forget('firebase_sync_stop');
            $lock->release();
            return 0;

        } catch (\Throwable $e) {
            $this->error("❌ Error Crítico: " . $e->getMessage());
            
            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'failed',
                    'message' => 'Error: ' . substr($e->getMessage(), 0, 250),
                    'completed_at' => now()
                ]);
            }

            \Illuminate\Support\Facades\Cache::forget('firebase_sync_stop');
            if (isset($lock)) $lock->release();
            return 1;
        }
    }

    /**
     * Motor de Sincronización Resiliente con Checkpoints y Lotes
     */
    protected function safeSyncProcess($firebase, $collectionName, $modelClass, $uniqueKey, $filters = [])
    {
        $this->info("\n--- Procesando Colección: {$collectionName} ---");
        
        // 1. Obtener o crear checkpoint
        $checkpoint = CloudSyncCheckpoint::firstOrCreate(
            ['process_name' => $collectionName],
            ['sync_type' => $this->option('full') ? 'full' : 'incremental', 'status' => 'idle']
        );

        // Si es full sync forzado, reseteamos el checkpoint
        if ($this->option('full')) {
            $checkpoint->update([
                'last_successful_sync_at' => null,
                'last_document_id' => null,
                'sync_type' => 'full',
                'records_processed' => 0
            ]);
        }

        $since = null;
        if (!$this->option('full')) {
            $since = $checkpoint->last_successful_sync_at ? $checkpoint->last_successful_sync_at->toDateTimeString() : null;
            
            // Ajuste por --hours si se proporciona manualmente
            if ($this->option('hours')) {
                $since = Carbon::now()->subHours($this->option('hours'))->toDateTimeString();
            }
        }

        $checkpoint->update(['status' => 'running', 'started_at' => now(), 'error_message' => null]);

        // Obtener cantidad real en la nube para estimación precisa del total_records (solo en Reconstrucción Total)
        if ($this->option('full') && $this->syncLog) {
            try {
                $exactCloudCount = $firebase->getCollectionCount($collectionName);
                if ($exactCloudCount > 0) {
                    $this->syncLog->update(['total_records' => $exactCloudCount]);
                }
            } catch (\Throwable $e) {
                \Log::warning("SafeSync exact count failed: " . $e->getMessage());
            }
        }

        try {
            $processedInThisSession = 0;
            $hasMore = true;
            $pageCursor = null; // Token de página para getCollectionBatched o cursor de getIncremental
            $batchCursor = []; // Cursor de lote independiente para paginación por offset
            $lastProcessedDoc = []; // Para persistencia de checkpoints y actualización incremental

            while ($hasMore) {
                // Verificar Circuit Breaker antes de cada lote
                if ($firebase->isCircuitOpen()) throw new \Exception("Cuota de Firebase agotada durante el proceso.");

                // Verificar señal de parada manual
                if (\Illuminate\Support\Facades\Cache::has('firebase_sync_stop')) {
                    throw new \Exception("Sincronización cancelada por el usuario desde el Dashboard.");
                }

                $this->comment("   Descargando lote de {$this->batchSize} registros (Desde: " . ($since ?? 'Inicio') . ")...");
                
                $data = [];
                if (!empty($filters)) {
                    $response = $firebase->getFilteredBatched($collectionName, $filters, $this->batchSize, $batchCursor);
                    $data = $response['data'] ?? [];
                    $batchCursor = $response['cursor'] ?? null;
                } elseif ($since) {
                    $data = $firebase->getIncremental($collectionName, $since, $this->batchSize, $pageCursor);
                } else {
                    $response = $firebase->getCollectionBatched($collectionName, $this->batchSize, $pageCursor);
                    if (!$response || !isset($response['data'])) {
                        $hasMore = false;
                        continue;
                    }
                    $data = $response['data'] ?? [];
                    $pageCursor = $response['nextPageToken'] ?? null;
                }

                if (empty($data)) {
                    $hasMore = false;
                    continue;
                }

                // Ajuste dinámico del total si descubrimos que hay más de lo estimado
                $isFilteredPage = !empty($filters);
                $hasMorePageToken = !$isFilteredPage && !empty($pageCursor);
                if ($this->syncLog && ($this->globalSynced + count($data)) > $this->syncLog->total_records) {
                    $this->syncLog->update(['total_records' => $this->globalSynced + count($data) + ($hasMorePageToken ? 500 : 0)]);
                }

                $bar = $this->output->createProgressBar(count($data));
                $bar->start();

                foreach ($data as $mapped) {
                    // Verificación de parada inmediata (cada 2 registros para máxima respuesta)
                    if ($this->globalSynced % 2 === 0 && \Illuminate\Support\Facades\Cache::has('firebase_sync_stop')) {
                        throw new \Exception("Sincronización cancelada por el usuario (Interrupción Inmediata).");
                    }

                    if (!$mapped || !is_array($mapped)) {
                        $bar->advance();
                        continue;
                    }

                    // SEGURIDAD: Validar que el registro pertenezca al responsable autorizado
                    if ($collectionName === 'afiliados' && $this->option('responsable-id')) {
                        $remoteResponsableId = isset($mapped['responsable_id']) ? (int)$mapped['responsable_id'] : null;
                        $authorizedResponsableId = (int)$this->option('responsable-id');
                        if ($remoteResponsableId !== $authorizedResponsableId) {
                            $this->skipped++;
                            $this->addToLiveFeed("Seguridad: " . ($mapped['nombre_completo'] ?? $mapped[$uniqueKey]) . " omitido (no autorizado)");
                            $bar->advance();
                            continue;
                        }
                    }
                    try {
                        // IMPORTANTE: Usar withoutGlobalScopes para encontrar el registro aunque no pertenezca al usuario actual
                        $model = $modelClass::withoutGlobalScopes()->where($uniqueKey, $mapped[$uniqueKey] ?? 'INVALID')->first();
                        
                        if (!$model) {
                            // Crear nuevo registro evitando disparar Push redundante
                            $fillable = (new $modelClass)->getFillable();
                            $filtered = array_intersect_key($mapped, array_flip($fillable));
                            
                            $model = new $modelClass($filtered);
                            // Metadatos de sincronización SafeSync
                            if ($model instanceof \App\Models\Afiliado) {
                                $model->updated_from = 'firebase';
                                if (empty($model->corte_id)) {
                                    $model->corte_id = \App\Models\Corte::first()->id ?? 1;
                                }
                                if (empty($model->estado_id)) {
                                    $model->estado_id = \App\Models\Estado::first()->id ?? 1;
                                }
                                if (is_null($model->conflict_status)) {
                                    $model->conflict_status = false;
                                }
                            }
                            $model->firebase_sync_status = 'synced';
                            $model->firebase_synced_at = now();
                            $model->saveQuietly(); // Evitar disparar observers/push jobs

                            $this->added++;
                            $checkpoint->increment('records_synced');
                            $this->addToLiveFeed("Nuevo Registro: " . ($mapped['nombre_completo'] ?? $mapped[$uniqueKey]));
                        } else {
                            // Sincronización inteligente (Hash/Versión)
                            $updated = $firebase->syncLocalModel($model, $mapped, (bool)$this->option('full'));
                            if ($updated) {
                                $this->updated++;
                                $checkpoint->increment('records_synced');
                                $this->addToLiveFeed("Actualizado: " . ($mapped['nombre_completo'] ?? $mapped[$uniqueKey]));
                            } else {
                                $this->skipped++;
                                $this->addToLiveFeed("Omitido (Sin cambios): " . ($mapped['nombre_completo'] ?? $mapped[$uniqueKey]));
                            }
                        }

                        $processedInThisSession++;
                        $checkpoint->increment('records_processed');
                        $this->globalSynced++;

                        // Actualizar log de la UI periódicamente para rendimiento
                        if ($this->syncLog && $processedInThisSession % 20 === 0) {
                            $this->syncLog->update([
                                'records_synced' => $this->globalSynced,
                                'records_added' => $this->added,
                                'records_updated' => $this->updated,
                                'records_skipped' => $this->skipped,
                                'records_failed' => $this->failed,
                                'status' => 'in_progress'
                             ]);
                        }
                        
                        // Guardar último cursor para reanudar/paginar
                        if ($mapped && is_array($mapped)) {
                            $lastProcessedDoc = [
                                'updated_at' => $mapped['firebase_updated_at_meta'] ?? $mapped['updated_at'] ?? null,
                                'id' => $mapped['firebase_id'] ?? null
                            ];
                        }

                    } catch (\Throwable $e) {
                        $this->failed++;
                        $checkpoint->increment('records_failed');
                        $this->addToLiveFeed("Error: " . ($mapped[$uniqueKey] ?? 'Reg.') . " - " . substr($e->getMessage(), 0, 40));
                        Log::error("Error en registro {$collectionName}: " . $e->getMessage());
                    }
                    $bar->advance();
                }

                $bar->finish();
                $this->line("");

                // Si es incremental, actualizamos el cursor de página con el último documento procesado
                if ($since && !empty($lastProcessedDoc)) {
                    $pageCursor = $lastProcessedDoc;
                }

                // Actualizar Checkpoint después de cada lote (Persistencia de progreso)
                if ($lastProcessedDoc && isset($lastProcessedDoc['updated_at'])) {
                    $checkpoint->update([
                        'last_document_id' => $lastProcessedDoc['id'] ?? null,
                        'last_firebase_updated_at' => $lastProcessedDoc['updated_at'] ? Carbon::parse($lastProcessedDoc['updated_at']) : null,
                        'last_successful_sync_at' => $lastProcessedDoc['updated_at'] ? Carbon::parse($lastProcessedDoc['updated_at']) : $checkpoint->last_successful_sync_at
                    ]);
                }

                // Si recibimos menos del batch size o si no hay nextPageToken en carga completa
                if (count($data) < $this->batchSize || (empty($filters) && !$since && empty($pageCursor))) {
                    $hasMore = false;
                }

                // Actualizar log de la UI (El polling de Livewire se encargará de refrescar)
                if ($this->syncLog) {
                    $this->syncLog->update([
                        'records_synced' => $this->globalSynced,
                        'records_added' => $this->added,
                        'records_updated' => $this->updated,
                        'records_skipped' => $this->skipped,
                        'records_failed' => $this->failed,
                        'status' => 'in_progress'
                    ]);
                }
                // broadcast(new \App\Events\SyncProgressEvent("Lote de " . count($data) . " registros procesado."));
            }

            $checkpoint->update([
                'status' => 'completed',
                'finished_at' => now(),
                'duration_seconds' => (int) max(0, round(now()->diffInSeconds($checkpoint->started_at)))
            ]);

        } catch (\Throwable $e) {
            $checkpoint->update([
                'status' => str_contains($e->getMessage(), 'Cuota') ? 'paused_quota' : 'failed',
                'error_message' => $e->getMessage(),
                'finished_at' => now()
            ]);
            throw $e;
        }
    }

    protected function syncStaticCatalog($firebase)
    {
        $force = (bool)$this->option('full');
        $this->info("--- Sincronizando Catálogos Estáticos ---");
        
        // Cortes
        if ($force || Corte::count() === 0) {
            $this->comment("Descargando catálogo de cortes...");
            $cortes = $firebase->getCollection('cortes', 100);
            foreach ($cortes as $c) {
                Corte::updateOrCreate(['nombre' => $c['nombre']], $c);
            }
        } else {
            $this->comment("Omitiendo catálogo de cortes (ya poblado localmente).");
        }

        // Estados
        if ($force || Estado::count() === 0) {
            $this->comment("Descargando catálogo de estados...");
            $estados = $firebase->getCollection('estados', 100);
            foreach ($estados as $e) {
                Estado::updateOrCreate(['nombre' => $e['nombre']], $e);
            }
        } else {
            $this->comment("Omitiendo catálogo de estados (ya poblado localmente).");
        }

        // Roles
        if ($force || Role::count() === 0) {
            $this->comment("Descargando catálogo de roles...");
            $roles = $firebase->getCollection('roles', 50);
            foreach ($roles as $r) {
                Role::updateOrCreate(['name' => $r['name']], ['guard_name' => $r['guard_name'] ?? 'web']);
            }
        } else {
            $this->comment("Omitiendo catálogo de roles (ya poblado localmente).");
        }
    }

    protected function addToLiveFeed($msg)
    {
        $feed = \Illuminate\Support\Facades\Cache::get('firebase_live_feed', []);
        array_unshift($feed, [
            'msg' => $msg,
            'time' => now()->format('H:i:s'),
            'type' => str_contains($msg, 'Actualizado') ? 'success' : (str_contains($msg, 'Nuevo') ? 'primary' : (str_contains($msg, 'Error') ? 'danger' : 'info'))
        ]);
        
        // Mantener historial de 50 registros
        $feed = array_slice($feed, 0, 50);
        \Illuminate\Support\Facades\Cache::put('firebase_live_feed', $feed, 3600);
    }
}
