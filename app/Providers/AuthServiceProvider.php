<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
// Importamos la clase base correcta para el AuthServiceProvider
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Corregimos el nombre de la clase y su herencia
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Colocamos la definición del Gate dentro del método boot()
        Gate::define('manage-tenant-user', function (User $admin, User $userToManage) {
            // Un admin puede gestionar a un usuario si el tenant_id de ambos es el mismo.
            return $admin->tenant_id === $userToManage->tenant_id;
        });
    }
}
