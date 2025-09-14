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
        Schema::create('cp_ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carta_porte_id')->constrained('cp_cartaporte')->onDelete('cascade');
            $table->string('tipo_ubicacion', 20);
            $table->string('id_ubicacion', 8)->nullable();
            $table->string('rfc_remitente_destinatario', 13);
            $table->string('nombre_remitente_destinatario')->nullable();
            $table->string('num_reg_id_trib')->nullable();
            $table->string('residencia_fiscal', 3)->nullable();
            $table->dateTime('fecha_hora_salida_llegada');
            $table->decimal('distancia_recorrida', 10, 2)->nullable();
            $table->string('calle');
            $table->string('numero_exterior')->nullable();
            $table->string('numero_interior')->nullable();
            $table->string('colonia');
            $table->string('localidad')->nullable();
            $table->string('referencia')->nullable();
            $table->string('municipio');
            $table->string('estado');
            $table->string('pais', 3);
            $table->string('codigo_postal', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_ubicaciones');
    }
};
