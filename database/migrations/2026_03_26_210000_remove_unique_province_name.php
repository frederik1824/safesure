<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provincias', function (Blueprint $table) {
            // Eliminar la restricción de nombre único para permitir datos sucios de Firebase
            $table->dropUnique(['nombre']);
        });
    }

    public function down(): void
    {
        Schema::table('provincias', function (Blueprint $table) {
            $table->unique('nombre');
        });
    }
};
