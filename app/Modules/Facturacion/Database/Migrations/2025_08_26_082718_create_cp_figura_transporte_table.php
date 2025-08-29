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
        Schema::create('cp_figura_transporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carta_porte_id')->constrained('cp_cartaporte')->onDelete('cascade');
            $table->string('tipo_figura', 2);
            $table->string('rfc_figura', 13);
            $table->string('num_licencia')->nullable();
            $table->string('nombre_figura')->nullable();
            $table->string('num_reg_id_trib_figura')->nullable();
            $table->string('residencia_fiscal_figura', 3)->nullable();
            $table->string('calle')->nullable();
            $table->string('numero_exterior')->nullable();
            $table->string('numero_interior')->nullable();
            $table->string('colonia')->nullable();
            $table->string('localidad')->nullable();
            $table->string('referencia')->nullable();
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
            $table->string('pais', 3)->nullable();
            $table->string('codigo_postal', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_figura_transporte');
    }
};
