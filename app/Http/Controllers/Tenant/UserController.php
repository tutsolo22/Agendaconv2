<?php

namespace App\Http\Controllers\Tenant;

use Illuminate\Routing\Controller;
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
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        // Proteger todas las rutas de este controlador con los permisos correspondientes
        $this->middleware('can:tenant.users.index')->only('index'); // @phpstan-ignore-line
        $this->middleware('can:tenant.users.create')->only(['create', 'store']); // @phpstan-ignore-line
        $this->middleware('can:tenant.users.edit')->only(['edit', 'update']); // @phpstan-ignore-line
        $this->middleware('can:tenant.users.destroy')->only('destroy');
    }

    /**
     * Función auxiliar para obtener los detalles de la licencia del tenant.
     */
    private function getLicenseDetails(): array
    {
        /** @var \App\Models\User $adminUser */
        $adminUser = Auth::user();

        if (!$adminUser) {
            return ['limit' => 0, 'count' => 0, 'canAddUsers' => false];
        }

        $userLimit = Licencia::where('tenant_id', $adminUser->tenant_id)
            ->where('is_active', true)
            ->whereDate('fecha_fin', '>', Carbon::now())
            ->sum('limite_usuarios'); // Usamos sum() para ser consistentes con la regla de validación

        $userLimit = $userLimit ?? 0;
        $currentUserCount = User::where('tenant_id', $adminUser->tenant_id)->count();

        return [
            'limit' => (int) $userLimit,
            'count' => $currentUserCount,
            'canAddUsers' => $currentUserCount < $userLimit,
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Ahora que el TenantScope no se aplica a los usuarios, debemos filtrar manualmente.
        $users = User::where('tenant_id', Auth::user()->tenant_id)
            ->latest()
            ->paginate(10);
        $licenseDetails = $this->getLicenseDetails();

        return view('tenant.users.index', compact('users', 'licenseDetails'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $licenseDetails = $this->getLicenseDetails();

        if (!$licenseDetails['canAddUsers']) {
            return redirect()->route('tenant.users.index')->with('error', 'Ha alcanzado el límite de usuarios de su licencia.');
        }

        // Obtener roles que no sean Super-Admin para el dropdown
        $roles = Role::where('name', '!=', 'Super-Admin')->pluck('name', 'name');

        return view('tenant.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Aplicamos la regla de validación personalizada aquí.
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, new UserLimitPerTenant],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Asignamos manualmente el tenant_id ya que el trait fue removido del modelo User.
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => Auth::user()->tenant_id,
        ]);

        // Asignar el rol seleccionado
        $user->assignRole($request->role);

        return redirect()->route('tenant.users.index')->with('success', 'Usuario creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // Usamos el Gate para una autorización explícita.
        $this->authorize('manage-tenant-user', $user);

        $roles = Role::where('name', '!=', 'Super-Admin')->pluck('name', 'name');

        return view('tenant.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('manage-tenant-user', $user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class.',email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Prevenir que el admin se quite a sí mismo el rol de 'Tenant-Admin'
        if ($user->id === Auth::id() && $request->role !== 'Tenant-Admin' && $user->hasRole('Tenant-Admin')) {
            return back()->with('error', 'No puedes cambiar tu propio rol de Administrador.');
        }

        $user->update($request->only('name', 'email'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }
        
        $user->syncRoles($request->role);

        return redirect()->route('tenant.users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('manage-tenant-user', $user);

        // Prevenir que un usuario se elimine a sí mismo.
        if ($user->id === Auth::id()) {
            return redirect()->route('tenant.users.index')->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()->route('tenant.users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}