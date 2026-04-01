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
        Schema::table('empresas', function (Blueprint $table) {
            if (Schema::hasColumn('empresas', 'responsable_id')) {
                $table->dropForeign(['responsable_id']);
                $table->dropColumn('responsable_id');
            }
            if (!Schema::hasColumn('empresas', 'promotor_id')) {
                $table->foreignId('promotor_id')->nullable()->after('comision_valor')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropForeign(['promotor_id']);
            $table->dropColumn('promotor_id');
            $table->foreignId('responsable_id')->nullable()->constrained('responsables')->nullOnDelete();
        });
    }
};
