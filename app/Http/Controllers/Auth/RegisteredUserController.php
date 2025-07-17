<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
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
            //Si es el primer usuario, asignamos el rol de Super-Admin.
            'is_super_admin' => $isFirstUser,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Si el usuario recién creado es un Super-Admin, lo redirigimos a su panel
    if ($user->is_super_admin) {
        return redirect()->route('admin.dashboard');
    }
 
    return redirect(route('dashboard', absolute: false));
    }
}
