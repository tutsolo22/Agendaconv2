<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('cascade');
            $table->string('group')->default('general')->comment('Grupo de configuración, ej: general, impresion, apariencia');
            $table->string('key');
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'sucursal_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};