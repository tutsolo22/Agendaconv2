<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_retenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('serie_folio_id')->constrained('facturacion_series_folios');
            $table->string('serie');
            $table->unsignedBigInteger('folio');
            $table->dateTime('fecha_exp');
            $table->string('cve_retenc'); // Clave de retención del catálogo del SAT
            $table->string('desc_retenc')->nullable(); // Descripción para clave "Otra retención"
            $table->decimal('monto_total_operacion', 18, 4);
            $table->decimal('monto_total_retenido', 18, 4);
            $table->enum('status', ['borrador', 'timbrado', 'cancelado'])->default('borrador');
            $table->uuid('uuid_fiscal')->nullable()->unique();
            $table->string('path_xml')->nullable();
            $table->dateTime('cancelacion_fecha')->nullable();
            $table->text('cancelacion_acuse')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_retenciones');
    }
};

