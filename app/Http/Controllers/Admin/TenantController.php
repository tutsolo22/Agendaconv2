<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class TenantController extends Controller
{
    public function index(): View
    {
        // Cargamos los tenants y su primer usuario (el admin) para mostrar en la tabla.
        $tenants = Tenant::with('users')->latest()->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        // Usamos una transacción para asegurar la integridad de los datos.
        // Si falla la creación del usuario, se revierte la creación del tenant.
        DB::transaction(function () use ($request) {
            // 1. Crear el Tenant
            $tenant = Tenant::create([
                'name' => $request->input('name'),
                // El ID se puede generar automáticamente o basarse en el nombre.
                // Por simplicidad, lo dejamos autoincremental por ahora.
            ]);

            // 2. Crear el usuario Administrador para este Tenant
            $adminUser = $tenant->users()->create([
                'name' => $request->input('admin_name'),
                'email' => $request->input('admin_email'),
                'password' => Hash::make($request->input('admin_password')),
            ]);

            // 3. Asignar el rol de 'Tenant-Admin'
            $tenantAdminRole = Role::where('name', 'Tenant-Admin')->first();
            if ($tenantAdminRole) {
                $adminUser->assignRole($tenantAdminRole);
            }
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant y administrador creados exitosamente.');
    }

    public function edit(Tenant $tenant): View
    {
        // Cargamos el primer usuario (admin) para pre-rellenar el formulario de edición.
        $admin = $tenant->users()->first();
        return view('admin.tenants.edit', compact('tenant', 'admin'));
    }

    public function update(StoreTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        DB::transaction(function () use ($request, $tenant) {
            // Actualizar datos del Tenant
            $tenant->update([
                'name' => $request->input('name'),
            ]);

            // Actualizar datos del Administrador del Tenant
            $admin = $tenant->users()->first();
            if ($admin) {
                $adminData = [
                    'name' => $request->input('admin_name'),
                    'email' => $request->input('admin_email'),
                ];
                // Solo actualizar la contraseña si se proporciona una nueva
                if ($request->filled('admin_password')) {
                    $adminData['password'] = Hash::make($request->input('admin_password'));
                }
                $admin->update($adminData);
            }
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant actualizado exitosamente.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        // Usamos una transacción para garantizar que toda la eliminación sea atómica.
        // Si algo falla, se revierte toda la operación.
        DB::transaction(function () use ($tenant) {
            // Eliminar recursos relacionados que dependen del tenant.
            // La arquitectura menciona licencias, por lo que es un buen candidato.
            // Asegúrate de que la relación `licencias()` exista en el modelo Tenant.
            $tenant->licencias()->delete();
            // Eliminar usuarios asociados al tenant.
            $tenant->users()->delete();
            // Finalmente, eliminar el tenant.
            $tenant->delete();
        });

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant eliminado exitosamente.');
    }
}