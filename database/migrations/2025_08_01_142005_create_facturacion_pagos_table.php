<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cfdi_id')->comment('El CFDI tipo P al que pertenece este pago')->constrained('facturacion_cfdis')->onDelete('cascade');
            $table->dateTime('fecha_pago');
            $table->string('forma_de_pago_p', 2);
            $table->string('moneda_p', 3);
            $table->decimal('monto', 12, 2);
            $table->decimal('tipo_cambio_p', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_pagos');
    }
};
