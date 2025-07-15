<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificamos si el usuario está autenticado y si la columna 'is_super_admin' es verdadera.
        if (!auth()->check() || !auth()->user()->is_super_admin) 
        {
            // Si no es un Super-Admin, abortamos la petición con un error 403 (Prohibido).
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
