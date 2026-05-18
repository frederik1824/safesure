<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Envolvemos TODO en un try-catch absoluto para garantizar que la migración
        // NUNCA interrumpa la inicialización del contenedor, incluso en entornos con permisos restrictivos.
        try {
            if (DB::getDriverName() === 'pgsql') {
                $results = DB::select("
                    SELECT 
                        S.relname AS seq_name,
                        T.relname AS table_name,
                        C.attname AS col_name
                    FROM pg_class AS S
                    JOIN pg_depend AS D ON D.objid = S.oid AND D.classid = 'pg_class'::regclass AND D.refclassid = 'pg_class'::regclass
                    JOIN pg_class AS T ON T.oid = D.refobjid
                    JOIN pg_attribute AS C ON C.attrelid = D.refobjid AND C.attnum = D.refobjsubid
                    WHERE S.relkind = 'S'
                ");

                foreach ($results as $r) {
                    try {
                        $max = DB::table($r->table_name)->max($r->col_name);
                        if ($max) {
                            DB::statement("SELECT setval('{$r->seq_name}', {$max})");
                        }
                    } catch (\Throwable $e) {
                        Log::warning("No se pudo recalibrar la secuencia {$r->seq_name}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("Fallo general al recalibrar secuencias de PostgreSQL: " . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay operación de reversión para los contadores de secuencias
    }
};
