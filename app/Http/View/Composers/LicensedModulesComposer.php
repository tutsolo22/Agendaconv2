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
                    ->with('modulo')
                    ->get();

                $licensedModules = collect();
                foreach ($licencias as $licencia) {
                    if ($licencia->modulo) {
                        $module = $licencia->modulo;
                        // Si el submenu es una cadena, lo decodificamos manualmente.
                        // Esto es un workaround porque el casteo no se está aplicando automáticamente.
                        if (is_string($module->getRawOriginal('submenu'))) {
                            $module->setAttribute('submenu', json_decode($module->getRawOriginal('submenu'), true));
                        }
                        $licensedModules->push($module);
                    }
                }
                $licensedModules = $licensedModules->unique('id');
            }
        }

        // Pasamos todas las variables a la vista
                        // Pasamos todas las variables a la vista
        $view->with(compact('licensedModules', 'isSuperAdmin', 'isTenantAdmin', 'user'));
    }
}
   