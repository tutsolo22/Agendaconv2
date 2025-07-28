<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantLicense
{
    /**
     * Handle an incoming request.
     *
     * Este middleware verifica la licencia del tenant. He añadido logs para que puedas
     * ver exactamente qué está pasando en tu archivo `storage/logs/laravel.log`.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        // Nos aseguramos de que el usuario esté logueado y tenga un tenant
        if (!$user || !$tenant = $user->tenant) {
            Log::warning('[CheckTenantLicense] Middleware ejecutado para un usuario sin tenant o no autenticado.');
            // Si no hay tenant, es un error de configuración grave.
            abort(500, 'Error de configuración: El usuario no está asociado a un tenant.');
        }

        // Verificamos si el tenant tiene CUALQUIER licencia activa y no expirada.
        $hasActiveLicense = $tenant->licencias()->where('is_active', true)->where('fecha_fin', '>', now())->exists();

        if (!$hasActiveLicense) {
            Log::warning("[CheckTenantLicense] El Tenant ID {$tenant->id} no tiene una licencia asignada.");
            abort(403, 'No tienes una licencia asignada. Por favor, contacta a soporte.');
        }

        return $next($request);
    }
}