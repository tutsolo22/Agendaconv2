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
        Schema::create('cp_mercancia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mercancias_id')->constrained('cp_mercancias')->onDelete('cascade');
            $table->string('bienes_transp', 10);
            $table->string('descripcion');
            $table->decimal('cantidad', 10, 2);
            $table->string('clave_unidad', 5);
            $table->string('unidad')->nullable();
            $table->string('dimensiones')->nullable();
            $table->string('material_peligroso', 2)->nullable();
            $table->string('cve_material_peligroso', 4)->nullable();
            $table->string('embalaje', 5)->nullable();
            $table->string('descrip_embalaje')->nullable();
            $table->decimal('peso_en_kg', 10, 3);
            $table->decimal('valor_mercancia', 10, 2)->nullable();
            $table->string('moneda', 3)->nullable();
            $table->string('fraccion_arancelaria')->nullable();
            $table->string('uuid_comercio_ext')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_mercancia');
    }
};
