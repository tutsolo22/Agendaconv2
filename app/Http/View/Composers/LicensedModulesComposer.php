<?php

namespace App\Http\View\Composers;

use App\Models\Licencia;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;

class LicensedModulesComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        $user = Auth::user();
        $isSuperAdmin = false;
        $isTenantAdmin = false;
        $licensedModules = collect();

        if ($user) {
            $isSuperAdmin = $user->hasRole('Super-Admin');
            $isTenantAdmin = $user->hasRole('Tenant-Admin');

            if ($isTenantAdmin) {
                $tenantId = $user->tenant_id;
                // Obtener todas las licencias activas y vigentes para el tenant
                $licencias = Licencia::where('tenant_id', $tenantId)
                    ->where('is_active', true)
                    ->where('fecha_fin', '>', now())
                    ->with('modulo') // Carga ansiosa para optimizar
                    ->get();

                // Extraer solo los módulos únicos de las licencias
                $licensedModules = $licencias->map->modulo->filter()->unique('id');
            }
        }

        // Pasamos todas las variables a la vista
        $view->with(compact('licensedModules', 'isSuperAdmin', 'isTenantAdmin', 'user'));
    }
}