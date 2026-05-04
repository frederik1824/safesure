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
        $tables = ['afiliados', 'empresas'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) use ($table) {
                if (!Schema::hasColumn($table, 'firebase_sync_status')) {
                    $table->enum('firebase_sync_status', ['synced', 'pending', 'error', 'modified'])->default('pending');
                }
                if (!Schema::hasColumn($table, 'firebase_sync_version')) {
                    $table->unsignedBigInteger('firebase_sync_version')->default(1);
                }
                if (!Schema::hasColumn($table, 'firebase_synced_at')) {
                    $table->timestamp('firebase_synced_at')->nullable();
                }
                if (!Schema::hasColumn($table, 'firebase_error_log')) {
                    $table->text('firebase_error_log')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        $tables = ['afiliados', 'empresas'];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['firebase_sync_status', 'firebase_sync_version', 'firebase_synced_at', 'firebase_error_log']);
            });
        }
    }
};
