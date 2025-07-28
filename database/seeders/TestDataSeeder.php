<?php

namespace Database\Seeders;

use App\Models\Licencia;
use App\Models\Modulo;
use App\Models\Sucursal;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles y módulos creados por el seeder anterior
        $tenantAdminRole = Role::where('name', 'Tenant-Admin')->first();
        $tenantUserRole = Role::where('name', 'Tenant-User')->first();
        $modulos = Modulo::all();

        // --- Tenant 1: Clínica Dental Sonrisas ---
        $tenant1 = Tenant::create([
            'name' => 'Clínica Dental Sonrisas',
            'email' => 'contacto@sonrisas.com',
            'phone' => '5512345678',
            'is_active' => true,
        ]);

        // Crear Tenant-Admin para Clínica Dental Sonrisas
        $admin1 = User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'admin@sonrisas.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant1->id,
        ]);
        $admin1->assignRole($tenantAdminRole);

        // Crear un usuario regular para Clínica Dental Sonrisas
        $user1 = User::create([
            'name' => 'Recepcionista Ana',
            'email' => 'recepcion@sonrisas.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant1->id,
        ]);
        $user1->assignRole($tenantUserRole);

        // Crear licencias para Clínica Dental Sonrisas
        $moduloCitas = $modulos->where('nombre', 'Citas Medicas')->first();
        if ($moduloCitas) {
            Licencia::create([
                'tenant_id' => $tenant1->id,
                'modulo_id' => $moduloCitas->id,
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => Carbon::now()->addYear(),
                'limite_usuarios' => 5,
                'is_active' => true,
            ]);
        }

        // Crear una sucursal para Clínica Dental Sonrisas
        Sucursal::create([
            'tenant_id' => $tenant1->id,
            'nombre' => 'Sucursal Centro',
            'direccion' => 'Av. Principal 123, Centro',
            'telefono' => '5511223344',
        ]);

        // --- Tenant 2: Restaurante El Buen Sabor ---
        $tenant2 = Tenant::create([
            'name' => 'Restaurante El Buen Sabor',
            'email' => 'info@buensabor.com',
            'phone' => '8187654321',
            'is_active' => true,
        ]);

        // Crear Tenant-Admin para Restaurante El Buen Sabor
        $admin2 = User::create([
            'name' => 'Chef María García',
            'email' => 'admin@buensabor.com',
            'password' => Hash::make('password'),
            'tenant_id' => $tenant2->id,
        ]);
        $admin2->assignRole($tenantAdminRole);

        // Crear licencias para Restaurante El Buen Sabor
        $moduloRestaurante = $modulos->where('nombre', 'Restaurante')->first();
        if ($moduloRestaurante) {
            Licencia::create([
                'tenant_id' => $tenant2->id,
                'modulo_id' => $moduloRestaurante->id,
                'fecha_inicio' => Carbon::now(),
                'fecha_fin' => Carbon::now()->addMonths(6),
                'limite_usuarios' => 1,
                'is_active' => true,
            ]);
        }
    }
}