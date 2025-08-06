<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_pago_doctos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('facturacion_pagos')->onDelete('cascade');
            $table->uuid('id_documento')->comment('UUID de la factura original que se paga');
            $table->string('serie', 25)->nullable();
            $table->string('folio', 40)->nullable();
            $table->string('moneda_dr', 3);
            $table->decimal('tipo_cambio_dr', 10, 6)->nullable();
            $table->integer('num_parcialidad');
            $table->decimal('imp_saldo_ant', 12, 2);
            $table->decimal('imp_pagado', 12, 2);
            $table->decimal('imp_saldo_insoluto', 12, 2);
            $table->string('objeto_imp_dr', 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_pago_doctos');
    }
};
