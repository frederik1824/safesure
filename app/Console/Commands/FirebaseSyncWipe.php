<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseSyncService;

class FirebaseSyncWipe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:wipe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra todas las colecciones de Firebase para empezar de cero.';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseSyncService $service)
    {
        $this->info("🧹 Iniciando limpieza completa de Firebase...");

        $collections = ['empresas', 'afiliados', 'roles', 'usuarios'];

        foreach ($collections as $collection) {
            $this->warn("Borrando coleccion: {$collection}...");
            
            $documents = $service->getCollection($collection);
            $total = count($documents);
            $bar = $this->output->createProgressBar($total);

            foreach ($documents as $doc) {
                $service->deleteDocument($collection, $doc['firebase_id']);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✅ {$collection} borrado.");
        }

        $this->info("🚀 Firebase limpio. Puedes re-sincronizar.");
    }
}
