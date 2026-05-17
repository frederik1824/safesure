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
        Schema::table('firebase_sync_logs', function (Blueprint $table) {
            $table->integer('records_added')->default(0)->after('records_synced');
            $table->integer('records_updated')->default(0)->after('records_added');
            $table->integer('records_skipped')->default(0)->after('records_updated');
            $table->integer('records_failed')->default(0)->after('records_skipped');
            $table->string('process_name')->nullable()->after('type'); // Para identificar si fue Afiliados, Empresas, etc.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firebase_sync_logs', function (Blueprint $table) {
            $table->dropColumn(['records_added', 'records_updated', 'records_skipped', 'records_failed', 'process_name']);
        });
    }
};
