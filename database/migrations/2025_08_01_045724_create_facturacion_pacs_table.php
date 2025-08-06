<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_pacs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('rfc', 13);
            $table->string('url_produccion');
            $table->string('url_pruebas');
            $table->string('usuario');
            $table->text('password'); // Encriptado
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_pacs');
    }
};
