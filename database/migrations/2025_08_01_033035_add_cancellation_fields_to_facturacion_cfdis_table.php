<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->string('motivo_cancelacion')->nullable()->after('status');
            $table->dateTime('fecha_cancelacion')->nullable()->after('motivo_cancelacion');
        });
    }

    public function down(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->dropColumn(['motivo_cancelacion', 'fecha_cancelacion']);
        });
    }
};
