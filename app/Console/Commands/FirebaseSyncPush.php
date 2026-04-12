<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Afiliado;
use App\Models\FirebaseSyncLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;

class FirebaseSyncPush extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'firebase:push 
                            {--all : Sync everything from local to cloud}
                            {--log-id= : Database log ID to update progress}';

    protected $syncLog;
    protected $globalSynced = 0;

    /**
     * The console command description.
     */
    protected $description = 'Pushes local data (Companies, Affiliates, users) to Firebase Firestore.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $firebase)
    {
        $lock = Cache::lock('firebase_sync_lock', 7200);

        if (!$lock->get()) {
            $this->error("⚠️ A synchronization is already in progress.");
            return 1;
        }

        try {
            $this->info("🚀 Starting Firebase Cloud Sync (PUSH)...");

            $logId = $this->option('log-id');
            if (!$logId) {
                $this->syncLog = FirebaseSyncLog::create([
                    'user_id' => 1,
                    'type' => 'Full Push (SSH)',
                    'status' => 'started',
                    'started_at' => now(),
                ]);
            } else {
                $this->syncLog = FirebaseSyncLog::find($logId);
            }

            $this->globalSynced = 0;

            // 1. SYNC ROLES & PERMISSIONS (Quick)
            $this->syncAuthMetadata($firebase);

            // 2. SYNC USERS
            $users = User::with('roles')->get();
            $this->syncCollection($firebase, 'users', $users, function($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password, 
                    'roles' => $user->getRoleNames()->toArray()
                ];
            }, 'id');

            // 3. SYNC COMPANIES
            $companies = Empresa::all();
            $this->syncCollection($firebase, 'empresas', $companies, function($emp) {
                return [
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
                ];
            }, 'uuid');

            // 4. SYNC AFILIADOS
            $afiliados = Afiliado::all();
            $this->syncCollection($firebase, 'afiliados', $afiliados, function($af) {
                return array_filter($af->toArray());
            }, 'uuid');

            if ($this->syncLog) {
                $this->syncLog->update([
                    'status' => 'completed',
                    'records_synced' => $this->globalSynced,
                    'completed_at' => now(),
                    'last_heartbeat_at' => now()
                ]);
            }

            $lock->release();
            $this->info("✅ Firebase Cloud PUSH completed!");
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
            $this->error("❌ Push Error: " . $e->getMessage());
            return 1;
        }
    }

    protected function syncAuthMetadata($firebase)
    {
        $this->info("--- Auth Metadata ---");
        $roles = Role::with('permissions')->get();
        foreach ($roles as $role) {
            $firebase->push('roles', $role->name, [
                'name' => $role->name,
                'guard_name' => $role->guard_name,
                'permissions' => $role->permissions->pluck('name')->toArray()
            ]);
        }
        $permissions = Permission::all();
        foreach ($permissions as $perm) {
            $firebase->push('permissions', $perm->name, ['name' => $perm->name, 'guard_name' => $perm->guard_name]);
        }
    }

    protected function syncCollection($firebase, $collection, $items, $transform, $keyName)
    {
        $this->info("--- Syncing Collection: {$collection} ---");
        $total = count($items);
        if ($this->syncLog) {
            $this->syncLog->increment('total_records', $total);
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($items as $item) {
            $docId = (string)$item->$keyName;
            if (empty($docId)) {
                $bar->advance();
                continue;
            }

            $firebase->push($collection, $docId, $transform($item));
            
            $bar->advance();
            $this->globalSynced++;

            if ($this->syncLog && $this->globalSynced % 25 == 0) {
                $currentLog = $this->syncLog->fresh();
                if ($currentLog->status === 'cancelled') {
                    $this->warn("Aborted.");
                    return;
                }
                $this->syncLog->update([
                    'records_synced' => $this->globalSynced,
                    'last_heartbeat_at' => now()
                ]);
            }
        }
        $bar->finish();
        $this->line("");
    }
}
