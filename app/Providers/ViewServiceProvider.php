<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\LicensedModulesComposer;

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
        // Using class based composers...
        // This composer will pass the licensed modules to the tenant admin navigation bar.
        View::composer(
            'components.layouts.admin-navigation',
            LicensedModulesComposer::class
        );
    }
}