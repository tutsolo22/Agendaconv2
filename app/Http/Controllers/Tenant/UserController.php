<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Licencia;
use App\Models\User;
use App\Rules\UserLimitPerTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // El TenantScope filtra automáticamente los usuarios por el tenant_id actual.
        $users = User::latest()->paginate(10);

        // Lógica para obtener el límite de usuarios y el conteo actual.
        /** @var \App\Models\User $adminUser */
        $adminUser = Auth::user();
        $maxUsers = Licencia::where('tenant_id', $adminUser->tenant_id)
            ->where('is_active', true)
            ->whereDate('fecha_expiracion', '>', Carbon::now())
            ->max('max_usuarios');

        $maxUsers = $maxUsers ?? 0;
        $userCount = $users->total();

        return view('tenant.users.index', compact('users', 'userCount', 'maxUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Verificamos si se ha alcanzado el límite antes de mostrar el formulario.
        /** @var \App\Models\User $adminUser */
        $adminUser = Auth::user();
        $maxUsers = Licencia::where('tenant_id', $adminUser->tenant_id)
            ->where('is_active', true)
            ->whereDate('fecha_expiracion', '>', Carbon::now())
            ->max('max_usuarios');

        $maxUsers = $maxUsers ?? 0;
        $userCount = User::where('tenant_id', $adminUser->tenant_id)->count();

        $limitReached = $userCount >= $maxUsers;

        return view('tenant.users.create', compact('limitReached', 'maxUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Aplicamos la regla de validación personalizada aquí.
            'name' => ['required', 'string', 'max:255', new UserLimitPerTenant],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // El trait TenantScoped agregará automáticamente el tenant_id.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Opcionalmente, asignar un rol por defecto a los nuevos usuarios.
        // Asegúrate de que el rol 'Tenant-User' exista en tu seeder de roles.
        $user->assignRole('Tenant-User');

        return redirect()->route('tenant.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // El Route-Model Binding junto con el TenantScope aseguran que solo podamos
        // editar usuarios de nuestro propio tenant.
        return view('tenant.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('tenant.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // Prevenir que un usuario se elimine a sí mismo.
        if ($user->id === Auth::id()) {
            return redirect()->route('tenant.users.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('tenant.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}