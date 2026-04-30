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
            $table->unsignedBigInteger('sync_version')->default(1)->after('firebase_synced_at');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->unsignedBigInteger('sync_version')->default(1)->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn('sync_version');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('sync_version');
        });
    }
};
