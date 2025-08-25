<?php

namespace App\Modules\Salud;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SaludServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Registrar las vistas del módulo desde la ruta correcta y con el namespace 'salud'
        $this->loadViewsFrom(resource_path('views/tenant/modules/salud'), 'salud');

        // Registrar las rutas del módulo
        $this->registerModuleRoutes();
    }

    /**
     * Register the routes for the module.
     *
     * Sigue la convención de la arquitectura para los middlewares y prefijos.
     *
     * @return void
     */
    protected function registerModuleRoutes()
    {
        Route::middleware(['web', 'auth', 'tenant.license'])
            ->prefix('tenant')
            ->as('tenant.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__.'/Routes/web.php');
            });
    }
}
