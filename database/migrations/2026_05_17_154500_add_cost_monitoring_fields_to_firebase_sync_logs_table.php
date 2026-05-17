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
            $table->integer('firebase_reads')->default(0)->after('records_failed');
            $table->integer('firebase_writes')->default(0)->after('firebase_reads');
            $table->decimal('estimated_cost', 10, 6)->default(0.000000)->after('firebase_writes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('firebase_sync_logs', function (Blueprint $table) {
            $table->dropColumn(['firebase_reads', 'firebase_writes', 'estimated_cost']);
        });
    }
};
