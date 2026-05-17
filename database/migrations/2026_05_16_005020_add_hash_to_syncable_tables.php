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
            $table->string('last_sync_hash', 64)->nullable()->after('firebase_sync_version');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->string('last_sync_hash', 64)->nullable()->after('firebase_sync_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn('last_sync_hash');
        });

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('last_sync_hash');
        });
    }
};
