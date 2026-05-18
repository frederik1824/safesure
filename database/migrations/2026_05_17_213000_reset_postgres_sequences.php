<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Solo ejecutamos si estamos usando PostgreSQL
        if (DB::getDriverName() === 'pgsql') {
            $results = DB::select("
                SELECT 
                    S.relname AS seq_name,
                    T.relname AS table_name,
                    C.attname AS col_name
                FROM pg_class AS S
                JOIN pg_depend AS D ON D.objid = S.oid AND D.classoid = 'pg_class'::regclass AND D.refclassoid = 'pg_class'::regclass
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
                    // Prevenir fallos en tablas que puedan no existir
                }
            }
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
