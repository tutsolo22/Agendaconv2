<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        // Configura globalmente Laravel para que use las vistas de paginación de Bootstrap 5.
        // Esto asegura que cada vez que se llame al método ->links() en un paginador,
        // se renderice el HTML con las clases correctas de Bootstrap.
        Paginator::useBootstrapFive();
    }
}
