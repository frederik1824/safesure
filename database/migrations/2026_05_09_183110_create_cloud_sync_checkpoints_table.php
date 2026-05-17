<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloud_sync_checkpoints', function (Blueprint $table) {
            $table->id();
            $table->string('process_name'); // 'afiliados', 'empresas', 'catalogo'
            $table->string('sync_type')->default('incremental'); // 'full', 'incremental', 'retry'
            $table->string('status')->default('idle'); // 'running', 'completed', 'failed', 'paused_quota'
            
            $table->dateTime('last_successful_sync_at')->nullable();
            $table->string('last_document_id')->nullable(); // Para paginación cursor-based
            $table->dateTime('last_firebase_updated_at')->nullable(); // Referencia de tiempo en Firebase
            
            $table->integer('records_processed')->default(0);
            $table->integer('records_synced')->default(0);
            $table->integer('records_failed')->default(0);
            $table->integer('estimated_reads')->default(0);
            
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            
            $table->timestamps();
            
            $table->index(['process_name', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cloud_sync_checkpoints');
    }
};
