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
        Schema::create('nominas_recibo_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominas_recibo_id')->constrained('nominas_recibos')->onDelete('cascade');

            $table->enum('tipo', ['percepcion', 'deduccion', 'otro_pago']);

            // Clave del catálogo del SAT (ej. 001 para Sueldos, 002 para ISR)
            $table->string('clave', 15);
            $table->string('concepto');
            
            // Importes
            $table->decimal('importe_gravado', 18, 4);
            $table->decimal('importe_exento', 18, 4);

            // Referencia al tipo de catálogo del SAT (opcional, para referencia)
            $table->string('sat_tipo_clave')->nullable()->comment('Ej: sat_nomina_tipos_percepciones');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas_recibo_detalles');
    }
};