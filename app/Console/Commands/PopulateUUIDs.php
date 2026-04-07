<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use Illuminate\Support\Str;

class PopulateUUIDs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:fix-uuids';

    /**
     * The console command description.
     */
    protected $description = 'Llena los campos UUID vacíos en Afiliados y Empresas para restaurar las rutas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔧 Reparando identificadores UUID...");

        // Fix Afiliados
        $afiliados = Afiliado::whereNull('uuid')->orWhere('uuid', '')->count();
        if ($afiliados > 0) {
            $this->warn("Arreglando {$afiliados} afiliados...");
            $bar = $this->output->createProgressBar($afiliados);
            Afiliado::whereNull('uuid')->orWhere('uuid', '')->chunkById(100, function ($batch) use ($bar) {
                foreach ($batch as $af) {
                    $af->uuid = (string) Str::uuid();
                    $af->saveQuietly();
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine();
        } else {
            $this->info("✅ Todos los afiliados tienen UUID.");
        }

        // Fix Empresas
        $this->warn("Checking for corrupt RNCs...");
        $corruptas = Empresa::where('rnc', 'LIKE', '%-%')->get();
        if ($corruptas->count() > 0) {
            foreach ($corruptas as $e) {
                $this->error("Empresa ID: {$e->id} | Name: {$e->nombre} | RNC IS UUID: {$e->rnc}");
            }
            $this->warn("⚠️  TEMA RNC: Estas empresas tienen un UUID en lugar de RNC. Debes corregirlo manualmente en el sistema.");
        }

        $empresas = Empresa::whereNull('uuid')->orWhere('uuid', '')->count();
        if ($empresas > 0) {
            $this->warn("Arreglando {$empresas} empresas...");
            $bar = $this->output->createProgressBar($empresas);
            Empresa::whereNull('uuid')->orWhere('uuid', '')->chunkById(50, function ($batch) use ($bar) {
                foreach ($batch as $emp) {
                    $emp->uuid = (string) Str::uuid();
                    $emp->saveQuietly();
                    $bar->advance();
                }
            });
            $bar->finish();
            $this->newLine();
        } else {
            $this->info("✅ Todas las empresas tienen UUID.");
        }

        $this->info("🚀 Reparación completada. Prueba ahora tus enlaces.");
    }
}
