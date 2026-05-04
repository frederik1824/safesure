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

        // Desactivar checks de integridad en Postgres
        DB::statement('SET session_replication_role = "replica";');

        foreach ($tables as $table) {
            $file = "{$exportPath}/{$table}.json";
            if (!File::exists($file)) {
                $this->warn("Saltando {$table}: archivo no encontrado.");
                continue;
            }

            if (!Schema::hasTable($table)) {
                $this->warn("Saltando {$table}: la tabla no existe en el servidor.");
                continue;
            }

            $this->info("Importando {$table}...");
            
            // Limpiar tabla
            DB::table($table)->truncate();

            $data = json_decode(File::get($file), true);
            
            // Insertar en bloques para no saturar la memoria
            $chunks = array_chunk($data, 500);
            foreach ($chunks as $chunk) {
                DB::table($table)->insert($chunk);
            }

            $this->comment("¡Tabla {$table} importada con éxito!");
        }

        // Reactivar checks
        DB::statement('SET session_replication_role = "origin";');

        $this->info("\n✅ ¡Trasplante completo! Todos los datos locales están ahora en el VPS.");
    }
}
