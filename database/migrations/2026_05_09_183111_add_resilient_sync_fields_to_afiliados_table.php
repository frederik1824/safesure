<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            // Campos de control de sincronización avanzada
            if (!Schema::hasColumn('afiliados', 'sync_hash')) {
                $table->string('sync_hash', 64)->nullable()->index(); // Para detección de cambios sin leer todo
            }
            if (!Schema::hasColumn('afiliados', 'firebase_updated_at')) {
                $table->dateTime('firebase_updated_at')->nullable()->index();
            }
            if (!Schema::hasColumn('afiliados', 'last_sync_attempt_at')) {
                $table->dateTime('last_sync_attempt_at')->nullable();
            }
            if (!Schema::hasColumn('afiliados', 'sync_error_message')) {
                $table->text('sync_error_message')->nullable();
            }
            if (!Schema::hasColumn('afiliados', 'sync_attempts')) {
                $table->integer('sync_attempts')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['sync_hash', 'firebase_updated_at', 'last_sync_attempt_at', 'sync_error_message', 'sync_attempts']);
        });
    }
};
