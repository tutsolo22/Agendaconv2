<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_cfdis', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Usamos UUID para el ID principal
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('serie_folio_id')->constrained('facturacion_series_folios')->onDelete('cascade');
            $table->string('serie', 10);
            $table->unsignedBigInteger('folio');
            $table->char('tipo_comprobante', 1); // I: Ingreso, E: Egreso, T: Traslado, P: Pago
            $table->string('forma_pago', 2);
            $table->string('metodo_pago', 3);
            $table->string('uso_cfdi', 4);
            $table->char('moneda', 3)->default('MXN');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('impuestos', 12, 2);
            $table->decimal('total', 12, 2);
            $table->uuid('uuid_fiscal')->nullable(); // El UUID que devuelve el PAC
            $table->enum('status', ['borrador', 'timbrado', 'cancelado', 'error'])->default('borrador');
            $table->string('path_xml')->nullable();
            $table->string('path_pdf')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_cfdis');
    }
};
