<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_retencion_impuestos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('retencion_id')->constrained('facturacion_retenciones')->onDelete('cascade');
            $table->decimal('base_ret', 18, 4);
            $table->string('impuesto'); // 01=ISR, 02=IVA, 03=IEPS
            $table->decimal('monto_ret', 18, 4);
            $table->string('tipo_pago_ret'); // "Pago provisional", "Pago definitivo"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_retencion_impuestos');
    }
};

