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
        Schema::table('mensajeros', function (Blueprint $table) {
            if (!Schema::hasColumn('mensajeros', 'activo')) {
                $table->boolean('activo')->default(true)->after('vehiculo_placa');
            }
            if (!Schema::hasColumn('mensajeros', 'color')) {
                $table->string('color')->default('#3b82f6')->after('activo');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mensajeros', function (Blueprint $table) {
            $table->dropColumn(['activo', 'color']);
        });
    }
};
