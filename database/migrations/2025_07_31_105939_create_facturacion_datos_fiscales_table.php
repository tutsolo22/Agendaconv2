<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturacion_datos_fiscales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->unique()->constrained('tenants')->onDelete('cascade');
            $table->string('razon_social');
            $table->string('rfc', 13);
            $table->string('regimen_fiscal_clave', 3); // Ej: 601, 612
            $table->string('cp_fiscal', 5);
            $table->text('path_cer_pem'); // Contenido del .cer convertido a .pem
            $table->text('path_key_pem'); // Contenido del .key convertido a .pem
            $table->string('password_csd'); // Contraseña del CSD (se encriptará en el modelo)
            $table->dateTime('valido_hasta');
            $table->foreignId('pac_id')->nullable()->constrained('facturacion_pacs')->onDelete('set null');
            $table->boolean('en_pruebas')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturacion_datos_fiscales');
    }
};
