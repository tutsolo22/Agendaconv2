<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
            
    // Comprobamos si existe algún usuario. Si no, este será el primero.
    $isFirstUser = ! User::exists();
 

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Si es el primer usuario, también le asignamos el rol de Spatie.
        // Esto es crucial para que el middleware 'role:Super-Admin' funcione.
        if ($isFirstUser) {
            $superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);
            $user->assignRole($superAdminRole);
        }

        event(new Registered($user));

        Auth::login($user);

        // Si el usuario recién creado es un Super-Admin, lo redirigimos a su panel
        if ($user->hasRole('Super-Admin')) {
            return redirect()->route('admin.dashboard');
        }
 
        return redirect(route('dashboard', absolute: false));
    }
}
