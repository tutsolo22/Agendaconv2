<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Services\FacturacionService;
use App\Modules\Facturacion\Services\FacturacionServiceTimbradoCfdi;
use App\Modules\Facturacion\Services\PagoService;
use App\Modules\Facturacion\Services\SatCredentialService;
use App\Modules\Facturacion\Services\SatCatalogService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FacturacionServiceProvider extends ServiceProvider
{
    /**
     * El directorio base del módulo.
     *
     * @var string
     */
    protected string $modulePath = __DIR__ . '/..';

    /**
     * Register services.
     */
    public function register(): void
    {
        // Registramos SatCatalogService como un singleton para que se cree una sola vez
        // por cada petición, optimizando la carga de catálogos.
        $this->app->singleton(SatCatalogService::class, function ($app) {
            return new SatCatalogService();
        });

        // Registramos el resto de los servicios del módulo como singletons.
        // Laravel resolverá sus dependencias automáticamente (ej: PagoService necesita SatCredentialService).
        $this->app->singleton(SatCredentialService::class);
        $this->app->singleton(PagoService::class);

        // Registramos ambos servicios de facturación. El controlador principal usará 'FacturacionService'.
        $this->app->singleton(FacturacionService::class);
        $this->app->singleton(FacturacionServiceTimbradoCfdi::class);
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
     * Registra todas las rutas del módulo (web y API interna).
     */
    protected function registerRoutes(): void
    {
        // Grupo para las rutas WEB del módulo (interfaz de usuario)
        Route::middleware(['web', 'auth', 'verified', 'tenant.license'])
            ->prefix('tenant/facturacion')
            ->name('tenant.facturacion.')
            ->group($this->modulePath . '/Routes/web.php');

        // Grupo para las rutas API del módulo (consumidas por JavaScript)
        // Se registran por separado para tener un prefijo de nombre y URL distinto,
        // lo que es más claro y menos propenso a errores.
        Route::middleware(['web', 'auth'])
            ->prefix('api/tenant') // URL: /api/tenant/facturacion/catalogos
            ->name('tenant.api.')   // Nombre final: tenant.api.facturacion.catalogos
            ->group($this->modulePath . '/Routes/api.php');
    }

    /**
     * Register the module's views.
     */
    protected function registerViews(): void
    {
        // Usar el helper resource_path() es más robusto y claro que calcular rutas relativas con '..'.
        // Esto asegura que Laravel siempre encuentre el directorio de vistas del módulo.
        $viewPath = resource_path('views/tenant/modules/facturacion');
        $this->loadViewsFrom($viewPath, 'facturacion');
    }
}