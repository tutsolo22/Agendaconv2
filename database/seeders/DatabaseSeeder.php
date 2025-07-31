<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Llamar al seeder que crea los roles y módulos
        $this->call(RolesAndModulesSeeder::class);

        // Crear el usuario Super-Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@agendacon.com'],
            [
                'name' => 'Super-Admin',
                'password' => Hash::make('password'), // Cambiar en producción
                'tenant_id' => null, // Los Super-Admins no pertenecen a un tenant
            ]
        );

        // Asignar el rol de Super-Admin
        $superAdminRole = Role::where('name', 'Super-Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Llamar al seeder de datos de prueba si no estamos en producción
        if (app()->environment() !== 'production') {
            $this->call(TestDataSeeder::class);
        }
    }
}
