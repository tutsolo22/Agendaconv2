<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
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

        if ($tenantAdminRole && $tenantUserRole) {
            // --- Tenant 1: Clínica Dental Sonrisas ---
            $tenant1 = Tenant::create([
                'name' => 'Clínica Dental Sonrisas',
                'email' => 'contacto@sonrisas.com',
                'phone' => '5512345678',
                'is_active' => true,
            ]);

            // Crear Sucursales para el Tenant 1
            $sucursalCentro = Sucursal::withoutEvents(function () use ($tenant1) {
                return Sucursal::create([
                    'tenant_id' => 1,
                    'nombre' => 'Sucursal Centro',
                    'direccion' => 'Av. Principal 123, Centro',
                    'telefono' => '5511223344',
                ]);
            });

            $sucursalNorte = Sucursal::withoutEvents(function () use ($tenant1) {
                return Sucursal::create([
                    'tenant_id' => 1,
                    'nombre' => 'Sucursal Norte',
                    'direccion' => 'Blvd. del Norte 456',
                    'telefono' => '5555667788',
                ]);
            });

            // Crear Usuarios para el Tenant 1
            $admin1 = User::create([
                'name' => 'Dr. Juan Pérez',
                'email' => 'admin@sonrisas.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant1->id,
            ]);
            $admin1->assignRole($tenantAdminRole);

            User::create([
                'name' => 'Recepcionista Ana',
                'email' => 'recepcion@sonrisas.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant1->id,
                'sucursal_id' => $sucursalCentro->id,
            ])->assignRole($tenantUserRole);

            User::create([
                'name' => 'Asistente Luis',
                'email' => 'asistente@sonrisas.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant1->id,
                'sucursal_id' => $sucursalNorte->id,
            ])->assignRole($tenantUserRole);

            // Crear Licencias para el Tenant 1
            $moduloCitas = $modulos->where('slug', 'citas-medicas')->first();
            if ($moduloCitas) {
                Licencia::create([
                    'tenant_id' => 1,
                    'modulo_id' => $moduloCitas->id,
                    'fecha_inicio' => Carbon::now(),
                    'fecha_fin' => Carbon::now()->addYear(),
                    'limite_usuarios' => 10,
                    'is_active' => true,
                ]);
            }

            // --- Tenant 2: Restaurante El Buen Sabor ---
            $tenant2 = Tenant::create([
                'name' => 'Restaurante El Buen Sabor',
                'email' => 'info@buensabor.com',
                'phone' => '8187654321',
                'is_active' => true,
            ]);

            $admin2 = User::create([
                'name' => 'Chef María García',
                'email' => 'admin@buensabor.com',
                'password' => Hash::make('password'),
                'tenant_id' => $tenant2->id,
            ]);
            $admin2->assignRole($tenantAdminRole);

            $moduloRestaurante = $modulos->where('slug', 'restaurante')->first();
            if ($moduloRestaurante) {
                Licencia::create([
                    'tenant_id' => 2,
                    'modulo_id' => $moduloRestaurante->id,
                    'fecha_inicio' => Carbon::now(),
                    'fecha_fin' => Carbon::now()->addMonths(6),
                    'limite_usuarios' => 3,
                    'is_active' => true,
                ]);
            }

            // Crear un cliente y un CFDI de ejemplo para Carta Porte
            $cliente = Cliente::create([
                'tenant_id' => 1,
                'nombre_completo' => 'Cliente de Prueba para Carta Porte',
                'rfc' => 'XAXX010101000',
                'email' => 'cliente.cp@test.com',
            ]);

            $serieFolio = SerieFolio::create([
                'tenant_id' => 1,
                'serie' => 'T',
                'folio_actual' => 0,
                'tipo_comprobante' => 'T',
            ]);

            Cfdi::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'tenant_id' => 1,
                'cliente_id' => $cliente->id,
                'serie_folio_id' => $serieFolio->id,
                'serie' => 'T',
                'folio' => 1,
                'tipo_comprobante' => 'T',
                'forma_pago' => '99',
                'metodo_pago' => 'PUE',
                'uso_cfdi' => 'S01',
                'subtotal' => 100,
                'impuestos' => 16,
                'total' => 116,
                'status' => 'timbrado',
            ]);
        }
    }
}
