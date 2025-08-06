<?php

namespace App\Modules\Facturacion\Providers;

use App\Modules\Facturacion\Http\Controllers\PagoController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FacturacionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
    }

    /**
     * Register the module's routes.
     */
    protected function registerRoutes(): void
    {
        Route::middleware(['web', 'auth', 'verified', 'tenant.license', 'role:Tenant-Admin'])
            ->prefix('tenant')
            ->as('tenant.')
            ->group(base_path('app/Modules/Facturacion/Routes/web.php'));
    }

    /**
     * Register the module's views.
     */
    protected function registerViews(): void
    {
        $viewPath = resource_path('views/tenant/modules/facturacion');
        $this->loadViewsFrom($viewPath, 'facturacion');
    }
}