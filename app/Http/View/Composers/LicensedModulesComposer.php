<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Licencia;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LicensedModulesComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $licensedModules = collect();

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            if ($user->tenant_id) {
                $licencias = Licencia::where('tenant_id', $user->tenant_id)
                                     ->where('is_active', true)
                                     ->whereDate('fecha_expiracion', '>', Carbon::now())
                                     ->with('modulo')
                                     ->get();

                $licensedModules = $licencias->map(fn ($licencia) => $licencia->modulo)->filter()->sortBy('nombre');
            }
        }

        $view->with('licensedModules', $licensedModules);
    }
}