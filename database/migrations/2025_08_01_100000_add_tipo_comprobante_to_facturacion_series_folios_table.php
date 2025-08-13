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
        Schema::table('facturacion_series_folios', function (Blueprint $table) {
            $table->char('tipo_comprobante', 1)->after('serie')->default('I')->comment('I: Ingreso, E: Egreso, P: Pago');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturacion_series_folios', function (Blueprint $table) {
            $table->dropColumn('tipo_comprobante');
        });
    }
};