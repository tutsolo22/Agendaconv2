<?php

namespace App\Providers;

use App\Http\View\Composers\LicensedModulesComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Este View Composer es crucial. Se encarga de inyectar las variables
        // dinámicas ($user, $isSuperAdmin, $isTenantAdmin, $licensedModules)
        // en nuestro layout unificado cada vez que se renderiza.
        View::composer('components.layouts.app', LicensedModulesComposer::class);
    }
}