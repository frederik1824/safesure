<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\Empresa;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class FirebaseSyncPull extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'firebase:pull-all {--full : Sync all data including companies}';

    /**
     * The console command description.
     */
    protected $description = 'Syncs Companies, Roles, and Users FROM Firebase Firestore TO local database.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $this->info("🚀 Starting Firebase Cloud Sync (PULL)...");

        // 🛡️ SYNC ROLES
        $this->info("--- Roles ---");
        $rolesData = $firebase->getCollection('roles');
        foreach ($rolesData as $mapped) {
            $this->comment("Checking role: {$mapped['name']}");
            
            $role = Role::updateOrCreate(
                ['name' => $mapped['name']],
                ['guard_name' => $mapped['guard_name'] ?? 'web']
            );
        }

        // 👤 SYNC USERS
        $this->info("--- Users ---");
        $usersData = $firebase->getCollection('users');
        foreach ($usersData as $mapped) {
            $this->comment("Checking user: {$mapped['email']}");
            
            $user = User::updateOrCreate(
                ['email' => $mapped['email']],
                [
                    'name' => $mapped['name'],
                    'password' => $mapped['password'] ?? Hash::make('Password')
                ]
            );

            if (isset($mapped['roles'])) {
                $roles = is_array($mapped['roles']) ? $mapped['roles'] : json_decode($mapped['roles'], true);
                if (is_array($roles)) {
                    $user->syncRoles($roles);
                }
            }
        }

        // 🏢 SYNC COMPANIES (Optional)
        if ($this->option('full')) {
            $this->info("--- Companies ---");
            $companiesData = $firebase->getCollection('empresas');
            foreach ($companiesData as $mapped) {
                $this->comment("Saving company: {$mapped['nombre']}");
                
                Empresa::updateOrCreate(
                    ['uuid' => $mapped['uuid'] ?? $mapped['firebase_id']],
                    [
                        'nombre' => $mapped['nombre'],
                        'rnc' => $mapped['rnc'] ?? null,
                        'email' => $mapped['email'] ?? null,
                        'telefono' => $mapped['telefono'] ?? null,
                        'direccion' => $mapped['direccion'] ?? null,
                        'es_real' => (bool)($mapped['es_real'] ?? false),
                        'es_filial' => (bool)($mapped['es_filial'] ?? false),
                        'es_verificada' => (bool)($mapped['es_verificada'] ?? false),
                        'provincia_id' => $mapped['provincia_id'] ?? null,
                        'municipio_id' => $mapped['municipio_id'] ?? null,
                        'contacto_nombre' => $mapped['contacto_nombre'] ?? null,
                        'contacto_puesto' => $mapped['contacto_puesto'] ?? null,
                        'contacto_telefono' => $mapped['contacto_telefono'] ?? null,
                        'contacto_email' => $mapped['contacto_email'] ?? null,
                        'comision_tipo' => $mapped['comision_tipo'] ?? null,
                        'comision_valor' => $mapped['comision_valor'] ?? 0,
                        'promotor_id' => $mapped['promotor_id'] ?? null,
                        'estado_contacto' => $mapped['estado_contacto'] ?? 'Nuevo',
                        'latitude' => $mapped['latitude'] ?? null,
                        'longitude' => $mapped['longitude'] ?? null,
                    ]
                );
            }
        }

        $this->info("✅ Firebase Cloud PULL completed!");
        return 0;
    }
}
