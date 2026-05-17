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
        Schema::create('traspasos', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_document_id')->unique()->nullable();
            $table->string('nombre_afiliado');
            $table->string('cedula_afiliado')->index();
            $table->string('agente')->index();
            $table->string('estado')->default('EFECTIVO')->index();
            $table->integer('cantidad_dependientes')->default(0);
            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_efectivo')->nullable();
            $table->string('periodo')->nullable()->index();
            $table->string('status_unipago')->nullable()->index();
            $table->string('sync_status')->default('pending')->index();
            $table->timestamp('firebase_updated_at')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->string('source_system')->default('CMD');
            $table->timestamp('local_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traspasos');
    }
};
