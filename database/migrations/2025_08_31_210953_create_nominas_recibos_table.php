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
        Schema::create('nominas_recibos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('nominas_empleado_id')->constrained('nominas_empleados')->onDelete('cascade');

            // Datos del CFDI
            $table->uuid('uuid')->nullable()->unique();
            $table->string('version', 5)->default('1.2');
            $table->string('serie', 25)->nullable();
            $table->string('folio', 40)->nullable();
            $table->enum('status', ['borrador', 'timbrado', 'cancelado'])->default('borrador');

            // Datos de la Nómina
            $table->string('tipo_nomina', 1)->comment('O - Ordinaria, E - Extraordinaria');
            $table->date('fecha_pago');
            $table->date('fecha_inicial_pago');
            $table->date('fecha_final_pago');
            $table->decimal('num_dias_pagados', 10, 3);
            $table->string('sat_periodicidad_pago_id', 2);

            // Totales
            $table->decimal('total_percepciones', 18, 4)->default(0);
            $table->decimal('total_deducciones', 18, 4)->default(0);
            $table->decimal('total_otros_pagos', 18, 4)->default(0);
            $table->decimal('total', 18, 4)->storedAs('total_percepciones - total_deducciones + total_otros_pagos');

            // Almacenamiento de archivos
            $table->longText('xml')->nullable();
            $table->string('path_pdf')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas_recibos');
    }
};