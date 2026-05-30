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
            $table->date('fecha_nacimiento')->nullable()->after('sexo');
            $table->string('numero_solicitud', 100)->nullable()->after('fecha_nacimiento');
            $table->integer('cantidad_dependientes')->default(0)->after('numero_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['fecha_nacimiento', 'numero_solicitud', 'cantidad_dependientes']);
        });
    }
};
