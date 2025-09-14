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
        Schema::create('nominas_empleados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Informacion Personal
            $table->string('nombre');
            $table->string('apellido_paterno');
            $table->string('apellido_materno');
            $table->string('rfc', 13)->unique();
            $table->string('curp', 18)->unique();
            $table->string('nss')->comment('Número de Seguridad Social');

            // Contacto
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();

            // Domicilio
            $table->string('calle');
            $table->string('numero_exterior');
            $table->string('numero_interior')->nullable();
            $table->string('colonia');
            $table->string('localidad')->nullable();
            $table->string('municipio');
            $table->string('estado');
            $table->string('pais', 3)->default('MEX');
            $table->string('codigo_postal', 5);

            // Datos Laborales
            $table->string('puesto');
            $table->date('fecha_inicio_rel_laboral');
            $table->string('sat_tipo_contrato_id', 2);
            $table->string('sat_tipo_regimen_id', 2);
            $table->string('sat_tipo_jornada_id', 2);
            $table->string('sat_riesgo_puesto_id', 2);
            $table->decimal('salario_base_cotizacion_apor', 10, 2)->comment('Salario Base de Cotización, Aportaciones');
            $table->decimal('salario_diario_integrado', 10, 2);

            // Datos de Pago
            $table->string('sat_banco_id', 3)->nullable()->comment('Banco para la transferencia');
            $table->string('cuenta_bancaria', 20)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nominas_empleados');
    }
};