<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // <<--- INICIO DE LA LÓGICA DE REDIRECCIÓN ---<<
        $user = $request->user();

        if ($user->hasRole('Super-Admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole('Tenant-Admin')) {
            return redirect()->intended(route('tenant.dashboard'));
        }

        // Si hay otros roles, puedes añadir más condiciones aquí.
        // Por ejemplo, un 'Tenant-User' podría ir al mismo dashboard del tenant.
        if ($user->tenant_id) {
            return redirect()->intended(route('tenant.dashboard'));
        }

        // Fallback por si un usuario no tiene un rol esperado.
        return redirect()->intended(route('dashboard'));
        // <<--- FIN DE LA LÓGICA DE REDIRECCIÓN ---<<
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}