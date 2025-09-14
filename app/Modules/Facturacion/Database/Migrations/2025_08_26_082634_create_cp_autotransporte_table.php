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
        Schema::create('cp_autotransporte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carta_porte_id')->constrained('cp_cartaporte')->onDelete('cascade');
            $table->string('perm_sct', 5);
            $table->string('num_permiso_sct');
            $table->string('nombre_aseg');
            $table->string('num_poliza_seguro');
            $table->string('config_vehicular', 10);
            $table->string('placa_vm', 10);
            $table->integer('anio_modelo_vm');
            $table->string('subtipo_rem', 10)->nullable();
            $table->string('placa', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_autotransporte');
    }
};
