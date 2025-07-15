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
        Schema::create('licencia_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('licencia_id')->constrained('licencias')->onDelete('cascade');
            $table->string('accion'); // Ej: 'creada', 'renovada', 'cancelada'
            $table->text('detalles')->nullable();
            $table->foreignId('realizado_por')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencia_historial');
    }
};