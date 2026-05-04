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
            Schema::table($table, function (Blueprint $table) {
                $table->enum('firebase_sync_status', ['synced', 'pending', 'error', 'modified'])->default('pending');
                $table->unsignedBigInteger('firebase_sync_version')->default(1);
                $table->timestamp('firebase_synced_at')->nullable();
                $table->text('firebase_error_log')->nullable();
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
