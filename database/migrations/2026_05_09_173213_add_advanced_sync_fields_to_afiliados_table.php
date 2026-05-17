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
            if (!Schema::hasColumn('afiliados', 'updated_from')) {
                $table->string('updated_from')->nullable()->comment('local, firebase')->after('updated_at');
            }
            if (!Schema::hasColumn('afiliados', 'last_updated_by')) {
                $table->unsignedBigInteger('last_updated_by')->nullable()->after('updated_from');
            }
            if (!Schema::hasColumn('afiliados', 'conflict_status')) {
                $table->boolean('conflict_status')->default(false)->after('last_updated_by');
            }
            if (!Schema::hasColumn('afiliados', 'firebase_error_log')) {
                $table->text('firebase_error_log')->nullable()->after('conflict_status');
            }
            if (!Schema::hasColumn('afiliados', 'remote_version')) {
                $table->integer('remote_version')->default(0)->after('firebase_sync_version');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('afiliados', function (Blueprint $table) {
            $table->dropColumn(['updated_from', 'last_updated_by', 'conflict_status', 'firebase_error_log', 'remote_version']);
        });
    }
};
