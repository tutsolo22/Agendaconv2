<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_cfdi_relaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('cfdi_id')->constrained('facturacion_cfdis')->onDelete('cascade');
            $table->string('tipo_relacion', 2); // e.g., '01' for Nota de crédito
            $table->uuid('cfdi_relacionado_uuid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_cfdi_relaciones');
    }
};
