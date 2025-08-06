<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->boolean('es_factura_global')->default(false)->after('status');
            $table->string('periodicidad', 2)->nullable()->after('es_factura_global');
            $table->string('meses', 2)->nullable()->after('periodicidad');
            $table->integer('anio')->nullable()->after('meses');
        });
    }

    public function down(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->dropColumn(['es_factura_global', 'periodicidad', 'meses', 'anio']);
        });
    }
};
