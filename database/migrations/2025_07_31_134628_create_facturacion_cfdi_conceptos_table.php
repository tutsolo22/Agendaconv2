<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_cfdi_conceptos', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cfdi_id')->constrained('facturacion_cfdis')->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('clave_prod_serv', 8);
            $table->decimal('cantidad', 12, 2);
            $table->string('clave_unidad', 3);
            $table->string('descripcion');
            $table->decimal('valor_unitario', 12, 2);
            $table->decimal('importe', 12, 2);
            $table->decimal('impuestos', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_cfdi_conceptos');
    }
};
