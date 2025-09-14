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
        Schema::create('cp_mercancias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carta_porte_id')->constrained('cp_cartaporte')->onDelete('cascade');
            $table->decimal('peso_bruto_total', 10, 3);
            $table->string('unidad_peso', 3);
            $table->decimal('peso_neto_total', 10, 3);
            $table->integer('num_total_mercancias');
            $table->decimal('cargo_por_tasacion', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_mercancias');
    }
};
