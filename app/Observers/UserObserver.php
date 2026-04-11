<?php

namespace App\Observers;

use App\Models\User;
use App\Services\FirebaseSyncService;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    protected $syncService;

    public function __construct(FirebaseSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle the User "saved" event.
     */
    public function saved(User $user): void
    {
        // Cargamos roles para que Firebase sepa qué permisos tiene
        $user->load('roles');

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'position' => $user->position,
            'avatar_url' => $user->avatar_url,
            'responsable_id' => $user->responsable_id,
            'is_cmd' => $user->isCmd(),
            'roles' => $user->getRoleNames()->toArray(),
            'updated_at' => $user->updated_at?->toIso8601String() ?? now()->toIso8601String(),
        ];

        // Sincronizar con la colección 'users'
        // Usaremos el ID del usuario como Document ID
        $this->syncService->syncData($data, 'users', (string)$user->id);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        $this->syncService->deleteDocument('users', (string)$user->id);
    }
}
