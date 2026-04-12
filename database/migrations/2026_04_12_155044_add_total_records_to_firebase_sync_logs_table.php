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
            $table->integer('total_records')->default(0)->after('records_synced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firebase_sync_logs', function (Blueprint $table) {
            $table->dropColumn('total_records');
        });
    }
};
