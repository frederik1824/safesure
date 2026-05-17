<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
    public function up(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->index(['estado', 'status_unipago']);
            $table->index(['agente', 'periodo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('traspasos', function (Blueprint $table) {
            $table->dropIndex(['estado', 'status_unipago']);
            $table->dropIndex(['agente', 'periodo']);
        });
    }
};
