<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DataImportCommand extends Command
{
    protected $signature = 'db:import-from-local';
    protected $description = 'Importa los archivos JSON generados localmente al VPS';

    public function handle()
    {
        if (!$this->confirm('⚠️ Esto borrará los datos actuales del servidor para reemplazarlos por los locales. ¿Continuar?')) {
            return;
        }

        $tables = [
            'estados',
            'cortes',
            'proveedores',
            'proveedors',
            'mensajeros',
            'empresas',
            'lotes',
            'afiliados',
            'provincias',
            'municipios',
            'responsables',
        ];

        $exportPath = base_path('storage/app/exports');

        if (!File::exists($exportPath)) {
            $this->error("No se encontró la carpeta: storage/app/exports");
            return;
        }

        // 1. LIMPIEZA (Orden inverso para evitar errores de llaves foráneas)
        $reverseTables = array_reverse($tables);
        foreach ($reverseTables as $table) {
            if (Schema::hasTable($table)) {
                $this->comment("Limpiando tabla: {$table}...");
                DB::table($table)->delete();
            }
        }

        // 2. CARGA (Orden directo para respetar dependencias)
        foreach ($tables as $table) {
            $file = "{$exportPath}/{$table}.json";
            if (!File::exists($file) || !Schema::hasTable($table)) {
                continue;
            }

            $this->info("Importando datos en: {$table}...");
            $data = json_decode(File::get($file), true);
            
            if (empty($data)) continue;

            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
            }
            $this->comment("¡{$table} cargada! (" . count($data) . " registros)");
        }

        $this->info("\n✅ ¡Trasplante completo! Todos los datos locales están ahora en el VPS.");
    }
}
