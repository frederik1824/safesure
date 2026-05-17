<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            if (!Schema::hasColumn('afiliados', 'corte_nombre')) {
                $table->string('corte_nombre')->nullable()->after('corte_id');
            }
            if (!Schema::hasColumn('afiliados', 'estado_nombre_remote')) {
                $table->string('estado_nombre_remote')->nullable()->after('estado_id');
            }
            if (!Schema::hasColumn('afiliados', 'responsable_nombre_remote')) {
                $table->string('responsable_nombre_remote')->nullable()->after('responsable_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['corte_nombre', 'estado_nombre_remote', 'responsable_nombre_remote']);
        });
    }
};
