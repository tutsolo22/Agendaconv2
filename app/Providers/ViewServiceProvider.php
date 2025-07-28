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
        // Asocia el composer con la vista de navegación del tenant.
        // Cada vez que se renderice 'layouts.navigation', el composer se ejecutará.
        View::composer('layouts.navigation', LicensedModulesComposer::class);
    }
}