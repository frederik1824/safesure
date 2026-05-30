<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Estado;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Estado::firstOrCreate(
            ['nombre' => 'Impreso'],
            ['es_final' => false]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Estado::where('nombre', 'Impreso')->delete();
    }
};
