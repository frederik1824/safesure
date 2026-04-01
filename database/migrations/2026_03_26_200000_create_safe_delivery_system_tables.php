<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Mensajeros (Personal de SAFE)
        Schema::create('mensajeros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nombre');
            $table->string('cedula')->nullable();
            $table->string('telefono')->nullable();
            $table->string('vehiculo_placa')->nullable();
            $table->enum('estado', ['Activo', 'Inactivo', 'Vacaciones'])->default('Activo');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Rutas de Entrega
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mensajero_id');
            $table->string('nombre_ruta'); // Ej: Ruta Norte, Ruta Sabatina
            $table->date('fecha_programada');
            $table->enum('estado', ['Abierta', 'En Proceso', 'Cerrada'])->default('Abierta');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->foreign('mensajero_id')->references('id')->on('mensajeros')->onDelete('cascade');
        });

        // 3. Detalle de Ruta (Pivot Afiliados)
        Schema::create('ruta_afiliado', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ruta_id');
            $table->unsignedBigInteger('afiliado_id');
            $table->integer('orden_entrega')->default(0);
            $table->boolean('entregado')->default(false);
            $table->timestamp('fecha_entrega_real')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->text('observacion_entrega')->nullable();
            $table->timestamps();

            $table->foreign('ruta_id')->references('id')->on('rutas')->onDelete('cascade');
            $table->foreign('afiliado_id')->references('id')->on('afiliados')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ruta_afiliado');
        Schema::dropIfExists('rutas');
        Schema::dropIfExists('mensajeros');
    }
};
