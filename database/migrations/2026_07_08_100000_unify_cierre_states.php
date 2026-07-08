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
        // 1. Unify estado_id in afiliados
        DB::table('afiliados')->where('estado_id', 20)->update(['estado_id' => 9]);

        // 2. Unify in historial_estados
        DB::table('historial_estados')->where('estado_anterior_id', 20)->update(['estado_anterior_id' => 9]);
        DB::table('historial_estados')->where('estado_nuevo_id', 20)->update(['estado_nuevo_id' => 9]);

        // 3. Remove the redundant state from estados table
        DB::table('estados')->where('id', 20)->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback is possible/needed since the change is a one-way unification of duplicates
    }
};
