<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Observers\UserObserver;

class SyncFirebaseUsers extends Command
{
    protected $signature = 'firebase:sync-users';
    protected $description = 'Sincroniza todos los usuarios locales con la colección users de Firestore';

    public function handle()
    {
        $users = User::all();
        $total = $users->count();
        
        $this->info("Iniciando sincronización de {$total} usuarios...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $observer = app(UserObserver::class);

        foreach ($users as $user) {
            try {
                $observer->saved($user);
            } catch (\Exception $e) {
                $this->error("\nError sincronizando usuario ID {$user->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->info("\n✅ Sincronización completada.");
        
        return 0;
    }
}
