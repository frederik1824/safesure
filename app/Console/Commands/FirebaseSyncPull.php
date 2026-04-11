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

class FirebaseSyncPull extends Command
{
    protected $signature = 'firebase:pull-all {--full : Sync all data including companies and affiliates}';
    protected $description = 'Syncs all entities FROM Firebase Firestore TO local database (Bidirectional Support).';

    public function handle(FirebaseSyncService $firebase)
    {
        $this->info("🚀 Starting Universal Firebase Cloud Sync (PULL)...");

        // 1. SYNC STATIC/CATALOG DATA (Cortes, Estados)
        $this->syncCatalog($firebase, 'cortes', Corte::class, ['nombre']);
        $this->syncCatalog($firebase, 'estados', Estado::class, ['nombre']);

        // 2. SYNC ROLES
        $this->info("--- Roles ---");
        $rolesData = $firebase->getCollection('roles');
        foreach ($rolesData as $mapped) {
            Role::updateOrCreate(
                ['name' => $mapped['name']],
                ['guard_name' => $mapped['guard_name'] ?? 'web']
            );
        }

        // 3. SYNC USERS
        $this->info("--- Users ---");
        $usersData = $firebase->getCollection('users');
        foreach ($usersData as $mapped) {
            $user = User::updateOrCreate(
                ['email' => $mapped['email']],
                [
                    'name' => $mapped['name'],
                    'password' => $mapped['password'] ?? Hash::make('Password'),
                    'phone' => $mapped['phone'] ?? null,
                    'position' => $mapped['position'] ?? null,
                ]
            );

            if (isset($mapped['roles'])) {
                $roles = is_array($mapped['roles']) ? $mapped['roles'] : json_decode($mapped['roles'], true);
                if (is_array($roles)) $user->syncRoles($roles);
            }
        }

        // 4. SYNC COMPANIES & AFILIADOS (Heavy Data)
        if ($this->option('full')) {
            $this->info("--- Companies ---");
            $companiesData = $firebase->getCollection('empresas');
            foreach ($companiesData as $mapped) {
                // RNC acts as unique ID for Companies in Firebase context
                Empresa::updateOrCreate(
                    ['rnc' => $mapped['rnc']],
                    array_intersect_key($mapped, array_flip((new Empresa)->getFillable()))
                );
            }

            $this->info("--- Affiliates (Real-time Mirror) ---");
            $afiliadosData = $firebase->getCollection('afiliados');
            $bar = $this->output->createProgressBar(count($afiliadosData));
            $bar->start();
            
            foreach ($afiliadosData as $mapped) {
                if (isset($mapped['cedula'])) {
                    // We use updateOrCreate but respect updated_at in syncLocalModel logic if we were calling it individually
                    // For bulk pull, we assume Firebase is the source of truth
                    $afiliado = Afiliado::where('cedula', $mapped['cedula'])->first();
                    if ($afiliado) {
                        $firebase->syncLocalModel($afiliado, $mapped);
                    } else {
                        Afiliado::create(array_intersect_key($mapped, array_flip((new Afiliado)->getFillable())));
                    }
                }
                $bar->advance();
            }
            $bar->finish();
            $this->info("");
        }

        $this->info("✅ Firebase Cloud PULL completed!");
        return 0;
    }

    protected function syncCatalog($firebase, $collection, $modelClass, $uniqueFields)
    {
        $this->info("--- Syncing Catalog: {$collection} ---");
        $data = $firebase->getCollection($collection);
        foreach ($data as $mapped) {
            $criteria = [];
            foreach ($uniqueFields as $field) $criteria[$field] = $mapped[$field];
            
            $modelClass::updateOrCreate($criteria, $mapped);
        }
    }
}
