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
        Schema::table('users', function (Blueprint $table) {
            // Se añade después de la columna 'password' para mantener el orden.
            $table->foreignId('tenant_id')->nullable()->after('password')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->after('tenant_id')->constrained('sucursales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Es importante eliminar las claves foráneas antes que las columnas.
            $table->dropForeign(['tenant_id']);
            $table->dropForeign(['sucursal_id']);
            $table->dropColumn(['tenant_id', 'sucursal_id']);
        });
    }
};

