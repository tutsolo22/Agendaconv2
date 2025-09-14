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
        Schema::create('nominas_incapacidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nominas_recibo_id')->constrained('nominas_recibos')->onDelete('cascade');

            $table->string('sat_tipo_incapacidad_id', 2);
            $table->integer('dias_incapacidad');
            $table->decimal('descuento', 18, 4);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas_incapacidades');
    }
};