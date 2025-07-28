<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class SuperAdminUserController extends Controller
{
    /**
     * Muestra una lista de todos los usuarios Super-Admin.
     */
    public function index(): View
    {
        $superAdmins = User::role('Super-Admin')->latest()->paginate(10);
        return view('admin.super-admins.index', compact('superAdmins'));
    }

    /**
     * Muestra el formulario para crear un nuevo Super-Admin.
     */
    public function create(): View
    {
        return view('admin.super-admins.create');
    }

    /**
     * Almacena un nuevo Super-Admin en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Asignamos el rol de Super-Admin.
            // Usamos firstOrFail para asegurar que el rol exista.
            $superAdminRole = Role::where('name', 'Super-Admin')->firstOrFail();
            $user->assignRole($superAdminRole);
        });

        return redirect()->route('admin.super-admins.index')
            ->with('success', 'Nuevo Super-Admin creado exitosamente.');
    }

    /**
     * Muestra el formulario para editar un Super-Admin existente.
     */
    public function edit(User $superAdmin): View
    {
        return view('admin.super-admins.edit', compact('superAdmin'));
    }

    /**
     * Actualiza un Super-Admin en la base de datos.
     */
    public function update(Request $request, User $superAdmin): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $superAdmin->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Solo actualizamos la contraseña si se ha proporcionado una nueva.
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $superAdmin->update($data);

        return redirect()->route('admin.super-admins.index')
            ->with('success', 'Administrador actualizado exitosamente.');
    }

    /**
     * Elimina un Super-Admin de la base de datos.
     */
    public function destroy(User $superAdmin): RedirectResponse
    {
        // **Regla de seguridad 1: Un administrador no puede eliminarse a sí mismo.**
        if (auth()->id() === $superAdmin->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta de administrador.');
        }

        // **Regla de seguridad 2: No se puede eliminar el último Super-Admin.**
        if (User::role('Super-Admin')->count() <= 1) {
            return back()->with('error', 'No se puede eliminar el único Super-Admin que queda.');
        }

        $superAdmin->delete();

        return redirect()->route('admin.super-admins.index')
            ->with('success', 'Administrador eliminado exitosamente.');
    }
}