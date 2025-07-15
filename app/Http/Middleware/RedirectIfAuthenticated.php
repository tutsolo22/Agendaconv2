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
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                // Si es Super-Admin, redirigir al dashboard de administración.
                if ($user->is_super_admin) {
                    return redirect()->route('admin.dashboard');
                }

                // Para otros usuarios, redirigir al dashboard normal.
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}