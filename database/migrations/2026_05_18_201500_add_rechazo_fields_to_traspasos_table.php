<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->text('motivo_rechazo')->nullable()->after('status_unipago');
            $table->date('fecha_rechazo')->nullable()->after('motivo_rechazo');
        });

        // Clear existing traspasos data to force a complete re-download under the new schema
        DB::table('traspasos')->truncate();

        // Reset the cloud sync checkpoint for traspasos
        DB::table('cloud_sync_checkpoints')
            ->where('process_name', 'traspasos')
            ->update([
                'last_firebase_updated_at' => null,
                'records_processed' => 0,
                'records_synced' => 0,
                'status' => 'idle',
                'error_message' => null
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropColumn(['motivo_rechazo', 'fecha_rechazo']);
        });
    }
};
