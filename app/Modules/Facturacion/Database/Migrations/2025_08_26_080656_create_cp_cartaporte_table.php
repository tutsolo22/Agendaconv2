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
        Schema::create('cp_cartaporte', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('facturacion_cfdi_id')->constrained('facturacion_cfdis')->onDelete('cascade');
            $table->string('version', 5);
            $table->string('transp_internac', 2);
            $table->string('entrada_salida_merc', 2)->nullable();
            $table->string('pais_origen_destino', 3)->nullable();
            $table->string('via_entrada_salida', 2)->nullable();
            $table->decimal('total_dist_rec', 10, 2)->nullable();
            $table->string('id_ccp', 50);
            $table->string('regimen_aduanero', 3)->nullable();
            $table->string('tipo_materia', 2)->nullable();
            $table->string('descripcion_materia')->nullable();
            $table->string('nombre_figura')->nullable();
            $table->string('rfc_figura', 13)->nullable();
            $table->string('num_reg_id_trib_figura')->nullable();
            $table->string('residencia_fiscal_figura', 3)->nullable();
            $table->boolean('logistica_inversa_recoleccion_devolucion')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_cartaporte');
    }
};
