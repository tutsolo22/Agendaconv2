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
        $superAdmin = User::create([
            'name' => 'Super-Admin',
            'email' => 'superadmin@agendacon.com',
            'password' => Hash::make('password'), // Cambiar en producción
            'is_super_admin' => true,
            'tenant_id' => null, // Los Super-Admins no pertenecen a un tenant
        ]);

        // Asignar el rol de Super-Admin
        // Usamos 'first' porque sabemos que el seeder anterior ya lo creó.
        $superAdminRole = Role::where('name', 'Super-Admin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
