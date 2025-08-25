<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Configuracion\Pac;
use App\Modules\Facturacion\Services\Contracts\CancelacionServiceInterface;
use App\Modules\Facturacion\Services\Contracts\RetencionTimbradoServiceInterface;
use App\Modules\Facturacion\Services\Contracts\PagoCancelacionServiceInterface;
use App\Modules\Facturacion\Services\Contracts\PagoTimbradoServiceInterface;
use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Services\Edicom\EdicomTimbradoService;
use App\Modules\Facturacion\Services\Edicom\EdicomCancelacionService;
use App\Modules\Facturacion\Services\Edicom\EdicomPagoCancelacionService;
use App\Modules\Facturacion\Services\FormasDigitales\FormasDigitalesTimbradoService;
use App\Modules\Facturacion\Services\FormasDigitales\FormasDigitalesCancelacionService;
use App\Modules\Facturacion\Services\FormasDigitales\FormasDigitalesRetencionService;
use App\Modules\Facturacion\Services\FormasDigitales\FormasDigitalesPagoCancelacionService;
use App\Modules\Facturacion\Services\FormasDigitales\FormasDigitalesPagoTimbradoService;
use App\Modules\Facturacion\Services\SWTimbradoService;
use App\Modules\Facturacion\Services\SWCancelacionService; // Placeholder
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FacturacionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registramos los servicios principales como singletons para que se resuelvan una sola vez.
        $this->app->singleton(CancelacionService::class);
        $this->app->singleton(FacturacionService::class);
        $this->app->singleton(SatCatalogService::class);
        $this->app->singleton(SatCredentialService::class);
        $this->app->singleton(ComprobanteEmailService::class); // Added
        $this->app->singleton(PdfService::class); // Added
        $this->app->singleton(PagoService::class);
        $this->app->singleton(RetencionService::class);


        // --- BINDING DINÁMICO DEL SERVICIO DE TIMBRADO ---
        // Esta es la parte más importante. Permite que el sistema seleccione el PAC
        // correcto según la configuración del tenant, haciendo el sistema flexible.
        $this->app->bind(TimbradoServiceInterface::class, function ($app) {
            $activeDriver = $this->getActivePacDriver('sw_sapiens');

            // 3. Usamos un switch para instanciar la clase de servicio correcta.
            //    Inyectamos SatCredentialService en cada uno, ya que todos lo necesitan.
            switch (strtolower($activeDriver)) {
                case 'edicom':
                    return new EdicomTimbradoService($app->make(SatCredentialService::class));
                case 'formas_digitales':
                    return new FormasDigitalesTimbradoService($app->make(SatCredentialService::class));
                case 'sw_sapiens':
                default:
                    return new SWTimbradoService($app->make(SatCredentialService::class));
            }
        });

        // --- BINDING DINÁMICO DEL SERVICIO DE CANCELACIÓN ---
        $this->app->bind(CancelacionServiceInterface::class, function ($app) {
            $activeDriver = $this->getActivePacDriver('sw_sapiens');

            switch (strtolower($activeDriver)) {
                case 'edicom':
                    return new EdicomCancelacionService($app->make(SatCredentialService::class));
                case 'formas_digitales':
                    return new FormasDigitalesCancelacionService($app->make(SatCredentialService::class));
                case 'sw_sapiens':
                default:
                    return new SWCancelacionService($app->make(SatCredentialService::class));
            }
        });

        // --- BINDING DINÁMICO DEL SERVICIO DE TIMBRADO DE PAGOS ---
        $this->app->bind(PagoTimbradoServiceInterface::class, function ($app) {
            $activeDriver = $this->getActivePacDriver('formas_digitales');

            switch (strtolower($activeDriver)) {
                // case 'edicom':
                //     return new EdicomPagoTimbradoService($app->make(SatCredentialService::class));
                case 'formas_digitales':
                default:
                    // Por ahora, solo Formas Digitales soporta el timbrado de pagos en nuestra implementación
                    return new FormasDigitalesPagoTimbradoService($app->make(SatCredentialService::class));
            }
        });

        // --- BINDING DINÁMICO DEL SERVICIO DE CANCELACIÓN DE PAGOS ---
        $this->app->bind(PagoCancelacionServiceInterface::class, function ($app) {
            $activeDriver = $this->getActivePacDriver('formas_digitales');

            switch (strtolower($activeDriver)) {
                case 'edicom':
                    return new EdicomPagoCancelacionService($app->make(SatCredentialService::class));
                case 'formas_digitales':
                default:
                    // Por ahora, solo Formas Digitales soporta la cancelación de pagos en nuestra implementación
                    return new FormasDigitalesPagoCancelacionService($app->make(SatCredentialService::class));
            }
        });

        // --- BINDING DINÁMICO DEL SERVICIO DE TIMBRADO DE RETENCIONES ---
        $this->app->bind(RetencionTimbradoServiceInterface::class, function ($app) {
            $activeDriver = $this->getActivePacDriver('formas_digitales');

            switch (strtolower($activeDriver)) {
                // case 'edicom':
                //     return new EdicomRetencionTimbradoService($app->make(SatCredentialService::class));
                case 'formas_digitales':
                default:
                    return new FormasDigitalesRetencionService($app->make(SatCredentialService::class));
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $basePath = __DIR__ . '/..';
        $moduleName = 'Facturacion';

        // Cargar las rutas del módulo
        Route::middleware(['web', 'auth', 'tenant.license'])
            ->prefix(config('tenancy.tenant_route_prefix') . '/facturacion') // Add 'facturacion' to the prefix
            ->as('tenant.facturacion.') // Add 'facturacion.' to the route names
            ->group(function () use ($basePath) { // Remove $moduleName from use
                $this->loadRoutesFrom($basePath . '/Routes/web.php');
            });

        // Cargar las vistas del módulo
        $this->loadViewsFrom(resource_path('views/tenant/modules/facturacion'), 'facturacion');

        // Cargar las rutas de la API del módulo
        Route::middleware('api')
            ->prefix('api')
            ->as('tenant.api.')
            ->group(function () use ($basePath) {
                $this->loadRoutesFrom($basePath . '/Routes/api.php');
            });
    }

    /**
     * Obtiene el driver del PAC activo para el tenant actual.
     *
     * @param string $defaultDriver El driver a usar si no hay PAC activo.
     * @return string
     */
    private function getActivePacDriver(string $defaultDriver): string
    {
        if (! tenant()) {
            return $defaultDriver;
        }

        $activePac = Pac::where('tenant_id', tenant('id'))
                        ->where('is_active', true)
                        ->first();

        return $activePac->driver ?? $defaultDriver;
    }
}