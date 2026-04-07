<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\User;
use App\Models\Empresa;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class FirebaseSyncPush extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'firebase:push {--all : Sync everything from local to cloud}';

    /**
     * The console command description.
     */
    protected $description = 'Pushes local Roles, Permissions, and Users to Firebase Firestore.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $this->info("🚀 Starting Firebase Cloud Sync (PUSH)...");

        // 🛡️ SYNC ROLES
        $this->info("--- Roles ---");
        $roles = Role::with('permissions')->get();
        foreach ($roles as $role) {
            $this->comment("Pushing role: {$role->name}");
            $firebase->push('roles', $role->name, [
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name')->toArray()
            ]);
        }

        // 🛡️ SYNC PERMISSIONS
        $this->info("--- Permissions ---");
        $permissions = Permission::all();
        foreach ($permissions as $perm) {
            $this->comment("Pushing permission: {$perm->name}");
            $firebase->push('permissions', $perm->name, [
                'name' => $perm->name,
                'guard_name' => $perm->guard_name
            ]);
        }

        // 👤 SYNC USERS
        $this->info("--- Users ---");
        $users = User::with('roles')->get();
        foreach ($users as $user) {
            $this->comment("Pushing user: {$user->email}");
            $firebase->push('users', (string)$user->id, [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password, 
                'roles' => $user->getRoleNames()->toArray()
            ]);
        }
        // 🏢 SYNC COMPANIES
        $this->info("--- Companies ---");
        $companies = Empresa::all();
        foreach ($companies as $emp) {
            $this->comment("Pushing company: {$emp->nombre}");
            $firebase->push('empresas', (string)$emp->uuid, [
                'uuid' => $emp->uuid,
                'nombre' => $emp->nombre,
                'rnc' => $emp->rnc,
                'direccion' => $emp->direccion,
                'telefono' => $emp->telefono,
                'es_real' => (bool)$emp->es_real,
                'es_filial' => (bool)$emp->es_filial,
                'es_verificada' => (bool)$emp->es_verificada,
                'provincia_id' => $emp->provincia_id,
                'municipio_id' => $emp->municipio_id,
                'contacto_nombre' => $emp->contacto_nombre,
                'contacto_puesto' => $emp->contacto_puesto,
                'contacto_telefono' => $emp->contacto_telefono,
                'contacto_email' => $emp->contacto_email,
                'comision_tipo' => $emp->comision_tipo,
                'comision_valor' => (float)$emp->comision_valor,
                'promotor_id' => $emp->promotor_id,
                'estado_contacto' => $emp->estado_contacto,
                'latitude' => $emp->latitude,
                'longitude' => $emp->longitude,
            ]);
        }

        $this->info("✅ Firebase Cloud PUSH completed! Now you can PULL this from your production server.");
        return 0;
    }
}
