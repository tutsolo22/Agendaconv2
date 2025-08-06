<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_ventas_publico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->string('folio_venta');
            $table->date('fecha');
            $table->decimal('total', 12, 2);
            $table->foreignUuid('cfdi_global_id')->nullable()->constrained('facturacion_cfdis')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_ventas_publico');
    }
};
