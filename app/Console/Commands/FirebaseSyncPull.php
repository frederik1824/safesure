<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Empresa;
use App\Models\User;
use App\Models\Afiliado;
use App\Models\Corte;
use App\Models\Estado;
use App\Models\Provincia;
use App\Models\Municipio;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class FirebaseSyncPull extends Command
{
    /**
     * @var string
     */
    protected $signature = 'firebase:pull-all 
                            {--full : Sync all data including companies and affiliates} 
                            {--hours= : Sync only records modified in the last N hours}
                            {--reales : Sync only verified real companies}
                            {--verificadas : Sync only verified companies}
                            {--log-id= : Database log ID to update progress}
                            {--debug : Output field names for debugging}';
    
    protected $syncLog;
    protected $globalSynced = 0;

    /**
     * @var string
     */
    protected $description = 'Syncs all entities FROM Firebase Firestore TO local database (Supports Incremental Sync).';

    public function handle(FirebaseSyncService $firebase)
    {
        $hours = $this->option('hours');
        $since = $hours ? Carbon::now()->subHours($hours)->toDateTimeString() : null;

        if (!$since) {
            $this->warn("⚠️ Performing FULL Sync. This consumes significant Firebase quota!");
        }

        $logId = $this->option('log-id');
        $this->syncLog = $logId ? \App\Models\FirebaseSyncLog::find($logId) : null;

        $lock = Cache::lock('firebase_sync_lock', 7200); // 2 hours lock

        if (!$lock->get()) {
            $this->error("⚠️ A synchronization is already in progress.");
            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'failed',
                    'message' => 'Sincronización abortada: Ya hay un proceso activo (Lock detectado).',
                    'completed_at' => now(),
                ]);
            }
            return 1;
        }

        try {
            $this->info("🚀 Starting Universal Firebase Cloud Sync (PULL)...");

            // Auto-create log if run via console without ID (and not already found)
            if (!$this->syncLog) {
                $this->syncLog = \App\Models\FirebaseSyncLog::create([
                    'user_id' => 1, // System/Admin
                    'type' => ($since ? 'Incremental' : 'Full') . ' (SSH)',
                    'status' => 'started',
                    'started_at' => now(),
                ]);
            }
            
            $this->globalSynced = 0;

            // 1. SYNC STATIC/CATALOG DATA
            $this->syncCollection($firebase, 'cortes', Corte::class, ['nombre'], $since);
            $this->syncCollection($firebase, 'estados', Estado::class, ['nombre'], $since);

            // 2. SYNC ROLES
            $this->info("--- Roles ---");
            $rolesData = $since ? $firebase->search('roles', ['updated_at' => $since]) : $firebase->getCollection('roles');
            foreach ($rolesData as $mapped) {
                Role::updateOrCreate(
                    ['name' => $mapped['name']],
                    ['guard_name' => $mapped['guard_name'] ?? 'web']
                );
            }

            // 3. SYNC USERS
            $this->info("--- Users ---");
            $usersData = $since ? $firebase->search('users', ['updated_at' => $since]) : $firebase->getCollection('users');
            $this->processCollection($firebase, User::class, $usersData, 'email', function($mapped) {
                return [
                    'name' => $mapped['name'],
                    'email' => $mapped['email'],
                    'password' => $mapped['password'] ?? Hash::make('Password'),
                    'phone' => $mapped['phone'] ?? null,
                    'position' => $mapped['position'] ?? null,
                ];
            });

            // 4. SYNC COMPANIES & AFILIADOS
            \Illuminate\Support\Facades\Log::info("Firebase Sync: Iniciando bloque de Empresas y Afiliados. Full: " . ($this->option('full') ? 'SÍ' : 'NO'));
            
            if ($this->option('full') || $since || $this->option('reales') || $this->option('verificadas')) {
                $filters = [];
                if ($since) $filters['updated_at'] = $since;
                if ($this->option('reales')) $filters['es_real'] = true;
                if ($this->option('verificadas')) $filters['es_verificada'] = true;

                $this->info("--- Descargando Empresas de Firebase... ---");
                $companiesData = empty($filters) ? $firebase->getCollection('empresas') : $firebase->search('empresas', $filters);
                \Illuminate\Support\Facades\Log::info("Firebase Sync: Empresas encontradas: " . count($companiesData));
                
                $this->processCollection($firebase, Empresa::class, $companiesData, 'uuid', function($mapped) {
                    // Normalización de IDs (evitar objetos JSON de otras apps)
                    foreach (['estado_id', 'provincia_id', 'municipio_id', 'user_id'] as $field) {
                        if (isset($mapped[$field]) && (is_array($mapped[$field]) || is_object($mapped[$field]))) {
                            $data = (array)$mapped[$field];
                            $mapped[$field] = $data['id'] ?? $data[0]['id'] ?? null;
                        } elseif (isset($mapped[$field]) && is_string($mapped[$field]) && str_starts_with($mapped[$field], '{')) {
                            $data = json_decode($mapped[$field], true);
                            $mapped[$field] = $data['id'] ?? $data[0]['id'] ?? null;
                        }
                    }

                    return $mapped;
                }, 'rnc');

                $this->info("--- Descargando Afiliados de Firebase... ---");
                $affiliatesFilters = $since ? ['updated_at' => $since] : [];
                $afiliadosData = empty($affiliatesFilters) ? $firebase->getCollection('afiliados') : $firebase->search('afiliados', $affiliatesFilters);
                \Illuminate\Support\Facades\Log::info("Firebase Sync: Afiliados encontrados: " . count($afiliadosData));
                
                $this->processCollection($firebase, Afiliado::class, $afiliadosData, 'cedula', function($mapped) {
                    // Normalización de estados (algunos vienen como objetos JSON en lugar de IDs)
                    foreach (['estado_id', 'provincia_id', 'municipio_id', 'user_id', 'lote_id'] as $field) {
                        if (isset($mapped[$field]) && (is_array($mapped[$field]) || is_object($mapped[$field]))) {
                            // Intentamos extraer el ID si es un objeto
                            $data = (array)$mapped[$field];
                            $mapped[$field] = $data['id'] ?? $data[0]['id'] ?? null;
                        } elseif (isset($mapped[$field]) && is_string($mapped[$field]) && str_starts_with($mapped[$field], '{')) {
                            // Si es un string que parece JSON, lo decodificamos
                            $data = json_decode($mapped[$field], true);
                            $mapped[$field] = $data['id'] ?? $data[0]['id'] ?? null;
                        }
                    }
                    return $mapped;
                });
            } else {
                \Illuminate\Support\Facades\Log::warning("Firebase Sync: No se detectó ninguna opción de carga (Full/Since/etc)");
                $this->warn("--- No se detectó ninguna opción de carga (Full/Since/etc) ---");
            }

            if ($this->syncLog) {
                $currentLog = $this->syncLog->fresh();
                if ($currentLog->status !== 'cancelled') {
                    $this->syncLog->update([
                        'status' => 'completed',
                        'records_synced' => $this->globalSynced,
                        'completed_at' => now(),
                        'last_heartbeat_at' => now()
                    ]);
                }
            }

            $lock->release();
            $this->info("✅ Firebase Cloud PULL completed!");
            return 0;

        } catch (\Throwable $e) {
            if (isset($lock) && $lock) $lock->release();
            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'failed',
                    'message' => $e->getMessage(),
                    'completed_at' => now()
                ]);
            }
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
    }

    // resolveGeodata logic moved to ResolveCompanyGeodataJob

    protected function syncCollection($firebase, $collection, $modelClass, $uniqueFields, $since = null)
    {
        $this->info("--- Syncing Catalog: {$collection} ---");
        $data = $since ? $firebase->search($collection, ['updated_at' => $since]) : $firebase->getCollection($collection);
        
        if (empty($data) && $since) {
             $data = $firebase->search($collection, ['created_at' => $since]);
        }
        
        $totalItems = count($data);
        if ($this->syncLog) {
            $this->syncLog->increment('total_records', $totalItems);
        }

        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        foreach ($data as $mapped) {
            $criteria = [];
            foreach ($uniqueFields as $field) $criteria[$field] = $mapped[$field] ?? null;
            
            // Si no hay criterios de unicidad válidos, saltamos
            if (empty(array_filter($criteria))) {
                $bar->advance();
                continue;
            }

            $modelClass::updateOrCreate($criteria, $mapped);
            $bar->advance();
            
            $this->globalSynced++;
            if ($this->syncLog && $this->globalSynced % 25 == 0) {
                 $currentLog = $this->syncLog->fresh();
                 if ($currentLog->status === 'cancelled') {
                     $this->warn("Sincronización abortada por el usuario.");
                     return;
                 }
                 $this->syncLog->update([
                     'records_synced' => $this->globalSynced,
                     'last_heartbeat_at' => now()
                 ]);
            }
        }
        $bar->finish();
        $this->info("");
    }

    protected function processCollection($firebase, $modelClass, $data, $uniqueKey, $transformCallback = null, $priorityKey = null)
    {
        if (empty($data)) {
            $this->line("   No recent updates found.");
            return;
        }

        $totalItems = count($data);
        if ($this->syncLog) {
            $this->syncLog->increment('total_records', $totalItems);
        }

        $bar = $this->output->createProgressBar($totalItems);
        $bar->start();

        foreach ($data as $mapped) {
            $model = null;

            // Priority Check (e.g., RNC for Empresas)
            if ($priorityKey && isset($mapped[$priorityKey]) && !empty($mapped[$priorityKey])) {
                $model = (new $modelClass)->where($priorityKey, $mapped[$priorityKey])->first();
            }

            // Fallback to Unique Key (e.g., UUID or Cedula)
            if (!$model && isset($mapped[$uniqueKey])) {
                $keyValue = $mapped[$uniqueKey];
                
                // Si la columna es UUID, validamos el formato para evitar errores de PostgreSQL
                $isValidUuid = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $keyValue);
                
                if ($uniqueKey !== 'uuid' || $isValidUuid) {
                    $model = (new $modelClass)->where($uniqueKey, $keyValue)->first();
                }
            }

            if ($model) {
                $attributes = $transformCallback ? $transformCallback($mapped) : $mapped;
                $urlToResolve = $attributes['_resolve_geo'] ?? null;
                unset($attributes['_resolve_geo']);
                
                $firebase->syncLocalModel($model, $attributes);
                
                if ($urlToResolve && empty($model->latitude)) {
                    \App\Jobs\ResolveCompanyGeodataJob::dispatch($model->id, $urlToResolve);
                }
            } else {
                $attributes = $transformCallback ? $transformCallback($mapped) : $mapped;
                $urlToResolve = $attributes['_resolve_geo'] ?? null;
                unset($attributes['_resolve_geo']);

                // Si el UUID es inválido, lo quitamos para que no rompa la inserción en Postgres
                if (isset($attributes['uuid']) && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $attributes['uuid'])) {
                    unset($attributes['uuid']);
                }

                // Filter attributes based on fillable
                $fillableData = array_intersect_key($attributes, array_flip((new $modelClass)->getFillable()));
                $newModel = $modelClass::create($fillableData);

                if ($urlToResolve) {
                    \App\Jobs\ResolveCompanyGeodataJob::dispatch($newModel->id, $urlToResolve);
                }
            }

            $bar->advance();
            $this->globalSynced++;
            if ($this->syncLog && $this->globalSynced % 25 == 0) {
                 $currentLog = $this->syncLog->fresh();
                 if ($currentLog->status === 'cancelled') {
                     $this->warn("Sincronización abortada por el usuario.");
                     return;
                 }
                 $this->syncLog->update([
                     'records_synced' => $this->globalSynced,
                     'last_heartbeat_at' => now()
                 ]);
            }
        }
        $bar->finish();
        $this->info("");
    }
}

