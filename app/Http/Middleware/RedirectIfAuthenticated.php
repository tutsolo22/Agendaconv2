<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            // Esta es la forma segura de verificar y obtener el usuario en un solo paso.
            // Evita el problema de que check() sea true pero user() devuelva null.
            if ($user = Auth::guard($guard)->user()) {
                if ($user->hasRole('Super-Admin')) {
                    return redirect()->route('admin.dashboard');
                }

                if ($user->tenant_id) {
                    return redirect()->route('tenant.dashboard');
                }

                return redirect()->route('dashboard'); // Fallback
            }
        }

        return $next($request);
    }
}