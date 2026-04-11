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
use Carbon\Carbon;

class FirebaseSyncPull extends Command
{
    /**
     * @var string
     */
    protected $signature = 'firebase:pull-all 
                            {--full : Sync all data including companies and affiliates} 
                            {--hours= : Sync only records created in the last N hours}
                            {--reales : Sync only verified real companies}
                            {--verificadas : Sync only verified companies}
                            {--debug : Output field names for debugging}';
    
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

        $this->info("🚀 Starting Universal Firebase Cloud Sync (PULL)...");

        // 1. SYNC STATIC/CATALOG DATA
        $this->syncCollection($firebase, 'cortes', Corte::class, ['nombre'], $since);
        $this->syncCollection($firebase, 'estados', Estado::class, ['nombre'], $since);

        // 2. SYNC ROLES
        $this->info("--- Roles ---");
        $rolesData = $since ? $firebase->search('roles', ['updated_at' => $since]) : $firebase->getCollection('roles');
        if ($this->option('debug') && !empty($rolesData)) $this->line("Fields: " . implode(', ', array_keys($rolesData[0])));
        
        foreach ($rolesData as $mapped) {
            Role::updateOrCreate(
                ['name' => $mapped['name']],
                ['guard_name' => $mapped['guard_name'] ?? 'web']
            );
        }

        // 3. SYNC USERS
        $this->info("--- Users ---");
        $usersData = $since ? $firebase->search('users', ['updated_at' => $since]) : $firebase->getCollection('users');
        if ($this->option('debug') && !empty($usersData)) $this->line("Fields: " . implode(', ', array_keys($usersData[0])));

        $this->processCollection($firebase, User::class, $usersData, 'email', function($mapped) {
            return [
                'name' => $mapped['name'],
                'password' => $mapped['password'] ?? Hash::make('Password'),
                'phone' => $mapped['phone'] ?? null,
                'position' => $mapped['position'] ?? null,
            ];
        });

        // 4. SYNC COMPANIES & AFILIADOS (Heavy Data)
        if ($this->option('full') || $since || $this->option('reales') || $this->option('verificadas')) {
            $this->info("--- Companies ---");
            $filters = [];
            if ($since) $filters['created_at'] = $since;
            if ($this->option('reales')) $filters['es_real'] = true;
            if ($this->option('verificadas')) $filters['es_verificada'] = true;

            $companiesData = empty($filters) ? $firebase->getCollection('empresas') : $firebase->search('empresas', $filters);
            
            if ($this->option('debug') && !empty($companiesData)) $this->line("Fields: " . implode(', ', array_keys($companiesData[0])));

            $this->processCollection($firebase, Empresa::class, $companiesData, 'uuid');

            $this->info("--- Affiliates (Real-time Mirror) ---");
            $affiliatesFilters = $since ? ['created_at' => $since] : [];
            $afiliadosData = empty($affiliatesFilters) ? $firebase->getCollection('afiliados') : $firebase->search('afiliados', $affiliatesFilters);

            if ($this->option('debug') && !empty($afiliadosData)) $this->line("Fields: " . implode(', ', array_keys($afiliadosData[0])));

            $this->processCollection($firebase, Afiliado::class, $afiliadosData, 'cedula');
        }

        $this->info("✅ Firebase Cloud PULL completed!");
        return 0;
    }

    protected function syncCollection($firebase, $collection, $modelClass, $uniqueFields, $since = null)
    {
        $this->info("--- Syncing Catalog: {$collection} ---");
        $data = $since ? $firebase->search($collection, ['created_at' => $since]) : $firebase->getCollection($collection);

        
        $bar = $this->output->createProgressBar(count($data));
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

        $bar = $this->output->createProgressBar(count($data));
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
        }
        $bar->finish();
        $this->info("");
    }
}
