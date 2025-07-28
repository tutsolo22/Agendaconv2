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
        Schema::table('licencias', function (Blueprint $table) {
            // Añadimos un UUID como código único para la licencia.
            // Es nullable porque una licencia puede ser asignada directamente por el Super-Admin.
            $table->uuid('codigo_licencia')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licencias', function (Blueprint $table) {
            $table->dropColumn('codigo_licencia');
        });
    }
};
