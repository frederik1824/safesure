<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert missing states safely to ensure quick actions and dashboard metrics work properly
        $estados = [
            ['id' => 10, 'nombre' => 'Acuse recibido', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'nombre' => 'Formulario recibido', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'nombre' => 'Incidencia', 'es_final' => false, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($estados as $est) {
            DB::table('estados')->updateOrInsert(['id' => $est['id']], $est);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('estados')->whereIn('id', [10, 11, 12])->delete();
    }
};
