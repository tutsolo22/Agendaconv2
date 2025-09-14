<?php

namespace App\Providers\Modules\Facturacion;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FacturacionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $moduleName = 'Facturacion';
        $modulePath = app_path('Modules/' . $moduleName);

        // Cargar vistas
        $this->loadViewsFrom($modulePath . '/Views', 'facturacion');

        // Cargar rutas web
        Route::middleware(['web', 'tenant'])
             ->group($modulePath . '/Routes/web.php');

        // Cargar rutas API
        Route::middleware(['web', 'tenant']) // Usando 'web' temporalmente
             ->group($modulePath . '/Routes/api.php');
    }
}