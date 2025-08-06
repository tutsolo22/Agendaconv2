<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nombre_completo');
            $table->string('rfc', 13)->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion_fiscal')->nullable();
            $table->enum('tipo', ['persona', 'empresa'])->default('persona');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
