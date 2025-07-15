<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Modulo;

class RolesAndModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Roles
        Role::create(['name' => 'Super-Admin']);
        Role::create(['name' => 'Tenant-Admin']);
        Role::create(['name' => 'Usuario-Tenant']);

        // Crear Módulos
        Modulo::create([
            'nombre' => 'Restaurante',
            'descripcion' => 'Módulo para la gestión completa de restaurantes, bares y cafeterías.'
        ]);

        Modulo::create([
            'nombre' => 'Facturacion',
            'descripcion' => 'Módulo para la emisión de facturas CFDI v4.0.'
        ]);

        Modulo::create([
            'nombre' => 'Citas',
            'descripcion' => 'Módulo para la gestión de citas y agendas (consultorios, estéticas, etc.).'
        ]);
    }
}