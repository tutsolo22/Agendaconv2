<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->decimal('saldo_pendiente', 12, 2)->nullable()->after('total');
            $table->string('status', 20)->default('borrador')->change(); // Más flexible que ENUM
            $table->index(['status', 'metodo_pago']);
        });

        // Inicializar saldo pendiente para facturas PPD existentes
        DB::table('facturacion_cfdis')
            ->where('tipo_comprobante', 'I')
            ->where('metodo_pago', 'PPD')
            ->update(['saldo_pendiente' => DB::raw('total')]);
    }

    public function down(): void
    {
        Schema::table('facturacion_cfdis', function (Blueprint $table) {
            $table->dropColumn('saldo_pendiente');
            $table->dropIndex(['status', 'metodo_pago']);
        });
    }
};
