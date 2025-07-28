<?php

namespace Database\Seeders;

use App\Models\Modulo;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos para asegurar que los cambios se apliquen
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // --- 1. Crear Permisos para Tenants ---
        // Lista de acciones que se pueden restringir.
        $permissions = [
            'tenant.users.index', 'tenant.users.create', 'tenant.users.edit', 'tenant.users.destroy',
            'tenant.sucursales.index', 'tenant.sucursales.create', 'tenant.sucursales.edit', 'tenant.sucursales.destroy',
            'admin.panel.access', // Permiso genérico para el panel de Super-Admin
            // A futuro, se pueden agregar más permisos aquí (ej: 'tenant.reports.view')
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // --- 2. Crear Roles ---
        $superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'Tenant-Admin', 'guard_name' => 'web']);
        $tenantUserRole = Role::firstOrCreate(['name' => 'Tenant-User', 'guard_name' => 'web']);

        // --- 3. Asignar Permisos a Roles ---
        // El Super-Admin tiene acceso a su panel. Usualmente, sus otros poderes se definen
        // con un Gate::before() que le concede todo, pero este permiso es útil para proteger rutas.
        $superAdminRole->givePermissionTo('admin.panel.access');

        // El Tenant-Admin puede gestionar usuarios y sucursales.
        $tenantAdminPermissions = collect($permissions)->filter(fn ($p) => str_starts_with($p, 'tenant.'))->all();
        $tenantAdminRole->syncPermissions($tenantAdminPermissions);

        // --- 4. Crear Módulos del Sistema ---
        $modulos = [
            ['nombre' => 'Restaurante', 'descripcion' => 'Gestión de mesas, menús y pedidos.', 'icono' => 'fa-utensils', 'is_active' => true],
            ['nombre' => 'Facturacion CFDI v4', 'descripcion' => 'Emisión de facturas electrónicas CFDI 4.0.', 'icono' => 'fa-file-invoice-dollar', 'is_active' => true],
            ['nombre' => 'Citas Medicas', 'descripcion' => 'Agendamiento y gestión de citas para consultorios.', 'icono' => 'fa-calendar-check', 'is_active' => true],
            
        ];

        foreach ($modulos as $modulo) {
            
            Modulo::firstOrCreate(['nombre' => $modulo['nombre']], $modulo);
        }
    }
}