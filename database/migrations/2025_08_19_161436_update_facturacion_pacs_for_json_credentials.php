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
        Schema::table('facturacion_pacs', function (Blueprint $table) {
            // Añadimos el 'driver' para identificar el servicio a usar.
            $table->string('driver')->after('nombre')->nullable()->comment('Identificador del servicio a utilizar (ej: edicom, sw_sapiens)');
            // Añadimos la columna JSON para credenciales flexibles y encriptadas.
            $table->text('credentials')->after('url_pruebas')->nullable()->comment('Credenciales del PAC en formato JSON encriptado');

            // Eliminamos las columnas antiguas y rígidas.
            $table->dropColumn(['usuario', 'password']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_pacs', function (Blueprint $table) {
            // Revertimos los cambios para no perder datos si se hace un rollback.
            $table->string('usuario')->nullable()->after('url_pruebas');
            $table->string('password')->nullable()->after('usuario');

            $table->dropColumn(['driver', 'credentials']);
        });
    }
};

