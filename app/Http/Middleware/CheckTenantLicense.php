<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Licencia;
use App\Models\Modulo;
use Carbon\Carbon;

class CheckTenantLicense
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $moduleSlug  El 'slug' o identificador único del módulo a verificar.
     */
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Si es Super Admin, tiene acceso a todo.
        if ($user->is_super_admin || $user->hasRole('Super-Admin')) {
            return $next($request);
        }

        // Si el usuario no pertenece a un tenant, no puede acceder a módulos de tenant.
        if (!$user->tenant_id) {
            abort(403, 'Acceso denegado. No estás asociado a un tenant.');
        }

        // Buscamos el módulo por su slug/identificador.
        $modulo = Modulo::where('nombre', $moduleSlug)->first(); // Asumimos que el nombre es único y se usa como slug.
        if (!$modulo) {
            abort(404, 'Módulo no encontrado.');
        }

        // Verificamos la licencia del tenant para este módulo.
        $licencia = Licencia::where('tenant_id', $user->tenant_id)
                            ->where('modulo_id', $modulo->id)
                            ->first();

        // Validamos las condiciones de la licencia.
        if (!$licencia || !$licencia->is_active || Carbon::now()->gt($licencia->fecha_expiracion)) {
            abort(403, 'Acceso denegado. Tu licencia para este módulo no es válida o ha expirado.');
        }

        // Si todo está en orden, permite el acceso.
        return $next($request);
    }
}