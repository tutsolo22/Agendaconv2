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
            [
                'nombre' => 'Citas Medicas',
                'slug' => 'citas-medicas',
                'route_name' => 'tenant.citas.index', // Nombre de ruta de ejemplo
                'submenu' => json_encode([
                    ['nombre' => 'Agenda', 'route_name' => 'tenant.citas.agenda'],
                    ['nombre' => 'Pacientes', 'route_name' => 'tenant.citas.pacientes'],
                ]),
                'descripcion' => 'Agendamiento y gestión de citas para consultorios.',
                'icono' => 'fa-solid fa-calendar-check',
                'is_active' => true
            ],
            [
                'nombre' => 'Restaurante',
                'slug' => 'restaurante',
                'route_name' => 'tenant.restaurante.index', // Nombre de ruta de ejemplo
                'submenu' => null,
                'descripcion' => 'Gestión de mesas, menús y pedidos.',
                'icono' => 'fa-solid fa-utensils',
                'is_active' => true
            ],
            [
                'nombre' => 'Facturacion V4',
                'slug' => 'facturacion-v4',
                'route_name' => 'tenant.facturacion.index', // Ruta principal del módulo de facturación
                'submenu' => json_encode([
                    ['nombre' => 'CFDI 4.0', 'route_name' => 'tenant.facturacion.cfdi40'],
                    ['nombre' => 'Pagos 2.0', 'route_name' => 'tenant.facturacion.pagos20'],
                    ['nombre' => 'Retenciones 2.0', 'route_name' => 'tenant.facturacion.retenciones20'],
                ]),
                'descripcion' => 'Emisión de facturas electrónicas CFDI 4.0.',
                'icono' => 'fa-solid fa-file-invoice-dollar',
                'is_active' => true
            ],
        ];

        foreach ($modulos as $moduloData) {
            // Usamos el slug como identificador único para evitar duplicados
            Modulo::firstOrCreate(['slug' => $moduloData['slug']], $moduloData);
        }
    }
}