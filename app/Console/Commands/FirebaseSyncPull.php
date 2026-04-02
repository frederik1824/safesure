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
        foreach ($rolesData['documents'] ?? [] as $doc) {
            $mapped = $firebase->mapDocument($doc);
            $this->comment("Checking role: {$mapped['name']}");
            
            $role = Role::updateOrCreate(
                ['name' => $mapped['name']],
                ['guard_name' => $mapped['guard_name'] ?? 'web']
            );

            // Note: Permissions should ideally exist first
            if (isset($mapped['permissions'])) {
                // $role->syncPermissions(json_decode($mapped['permissions']));
            }
        }

        // 👤 SYNC USERS
        $this->info("--- Users ---");
        $usersData = $firebase->getCollection('users');
        foreach ($usersData['documents'] ?? [] as $doc) {
            $mapped = $firebase->mapDocument($doc);
            $this->comment("Checking user: {$mapped['email']}");
            
            $user = User::updateOrCreate(
                ['email' => $mapped['email']],
                [
                    'name' => $mapped['name'],
                    'password' => $mapped['password'] ?? Hash::make('Password')
                ]
            );

            if (isset($mapped['roles'])) {
                $roles = json_decode($mapped['roles'], true);
                if (is_array($roles)) {
                    $user->syncRoles($roles);
                }
            }
        }

        // 🏢 SYNC COMPANIES (Optional)
        if ($this->option('full')) {
            $this->info("--- Companies ---");
            $companiesData = $firebase->getCollection('empresas');
            foreach ($companiesData['documents'] ?? [] as $doc) {
                $mapped = $firebase->mapDocument($doc);
                $this->comment("Saving company: {$mapped['nombre']}");
                
                Empresa::updateOrCreate(
                    ['firebase_uuid' => $mapped['firebase_id']],
                    [
                        'nombre' => $mapped['nombre'],
                        'rnc' => $mapped['rnc'] ?? null,
                        'email' => $mapped['email'] ?? null,
                        'telefono' => $mapped['telefono'] ?? null,
                        'direccion' => $mapped['direccion'] ?? null,
                        'es_verificada' => true,
                        // Add more fields if needed
                    ]
                );
            }
        }

        $this->info("✅ Firebase Cloud PULL completed!");
        return 0;
    }
}
