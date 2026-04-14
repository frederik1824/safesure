<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Afiliado;
use App\Models\Empresa;
use Illuminate\Support\Facades\DB;

class NormalizeCedulas extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:normalize-cedulas {--rnc : También normalizar los RNC de las empresas}';

    /**
     * The console command description.
     */
    protected $description = 'Elimina guiones y caracteres no numéricos de las cédulas (y opcionalmente RNC) para estandarización.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🚀 Iniciando formateo de cédulas (000-0000000-0)...");

        // 1. Normalizar Afiliados
        $count = Afiliado::count();
        $this->info("Procesando {$count} afiliados...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        Afiliado::chunkById(500, function ($afiliados) use ($bar) {
            foreach ($afiliados as $afiliado) {
                if ($afiliado->cedula) {
                    $original = $afiliado->cedula;
                    // Limpiar primero
                    $clean = preg_replace('/[^0-9]/', '', $original);
                    
                    // Aplicar máscara si tiene 11 dígitos
                    if (strlen($clean) === 11) {
                        $formatted = substr($clean, 0, 3) . '-' . substr($clean, 3, 7) . '-' . substr($clean, 10, 1);
                        
                        if ($original !== $formatted) {
                            $afiliado->cedula = $formatted;
                            $afiliado->saveQuietly();
                        }
                    }
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("✅ Cédulas de afiliados formateadas correctamente.");

        // 2. Normalizar Empresas (Opcional - RNC usualmente es 9 o 11 dígitos)
        if ($this->option('rnc')) {
            $this->info("Se omitirá el formateo de RNC con guiones para mantener compatibilidad con las URLs de búsqueda, a menos que se especifique lo contrario.");
        }

        $this->info("🎉 Proceso de normalización completado con éxito.");
        return 0;
    }
}
