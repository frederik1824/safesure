<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\User;
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
                'password' => $user->password, // Uploading hash for auth sync
                'roles' => $user->getRoleNames()->toArray()
            ]);
        }

        $this->info("✅ Firebase Cloud PUSH completed! Now you can PULL this from your production server.");
        return 0;
    }
}
