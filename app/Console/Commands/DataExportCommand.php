<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DataExportCommand extends Command
{
    protected $signature = 'db:export-for-vps';
    protected $description = 'Exporta las tablas principales a JSON para subir al VPS';

    public function handle()
    {
        $tables = [
            'estados',
            'cortes',
            'proveedores',
            'proveedors', // Intento alternativo
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
            File::makeDirectory($exportPath, 0755, true);
        }

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("Saltando tabla: {$table} (No existe en la base de datos)");
                continue;
            }

            $this->info("Exportando tabla: {$table}...");
            $data = DB::table($table)->get();
            File::put("{$exportPath}/{$table}.json", $data->toJson());
            $this->comment("¡Tabla {$table} exportada! (" . count($data) . " registros)");
        }

        $this->info("\n✅ Exportación completada en: storage/app/exports/");
        $this->warn("Ahora comprime esa carpeta y súbela al VPS.");
    }
}
