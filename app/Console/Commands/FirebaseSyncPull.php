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

        if ($since) {
            $this->info("⏳ Performing Incremental Sync (Since: {$since})");
        } else {
            $this->warn("⚠️ Performing FULL Sync. This consumes significant Firebase quota!");
            if (!$this->confirm('Do you want to continue?', true)) return 1;
        }

        $lock = Cache::lock('firebase_sync_lock', 7200); // 2 hours lock

        if (!$lock->get()) {
            $this->error("⚠️ A synchronization is already in progress.");
            return 1;
        }

        try {
            $this->info("🚀 Starting Universal Firebase Cloud Sync (PULL)...");

            $logId = $this->option('log-id');
            
            // Auto-create log if run via console without ID
            if (!$logId) {
                $this->syncLog = \App\Models\FirebaseSyncLog::create([
                    'user_id' => 1, // System/Admin
                    'type' => ($since ? 'Incremental' : 'Full') . ' (SSH)',
                    'status' => 'started',
                    'started_at' => now(),
                ]);
            } else {
                $this->syncLog = \App\Models\FirebaseSyncLog::find($logId);
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
                    'password' => $mapped['password'] ?? Hash::make('Password'),
                    'phone' => $mapped['phone'] ?? null,
                    'position' => $mapped['position'] ?? null,
                ];
            });

            // 4. SYNC COMPANIES & AFILIADOS
            if ($this->option('full') || $since || $this->option('reales') || $this->option('verificadas')) {
                $filters = [];
                if ($since) $filters['updated_at'] = $since;
                if ($this->option('reales')) $filters['es_real'] = true;
                if ($this->option('verificadas')) $filters['es_verificada'] = true;

                $companiesData = empty($filters) ? $firebase->getCollection('empresas') : $firebase->search('empresas', $filters);
                $this->processCollection($firebase, Empresa::class, $companiesData, 'uuid');

                $affiliatesFilters = $since ? ['updated_at' => $since] : [];
                $afiliadosData = empty($affiliatesFilters) ? $firebase->getCollection('afiliados') : $firebase->search('afiliados', $affiliatesFilters);
                $this->processCollection($firebase, Afiliado::class, $afiliadosData, 'cedula');
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
            if (isset($lock)) $lock->release();
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

    protected function processCollection($firebase, $modelClass, $data, $uniqueKey, $transformCallback = null)
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
            if (isset($mapped[$uniqueKey])) {
                $model = (new $modelClass)->where($uniqueKey, $mapped[$uniqueKey])->first();
                if ($model) {
                    $firebase->syncLocalModel($model, $mapped);
                } else {
                    $attributes = $transformCallback ? $transformCallback($mapped) : $mapped;
                    // Filter attributes based on fillable
                    $fillable = array_intersect_key($mapped, array_flip((new $modelClass)->getFillable()));
                    $modelClass::create(array_merge($fillable, $attributes));
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
