<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait TenantScoped
{
    /**
     * The "booted" method of the model.
     */
    protected static function bootTenantScoped(): void
    {
        static::addGlobalScope(new TenantScope);

        // Asignar automáticamente el tenant_id al crear un nuevo recurso
        // si el usuario autenticado es un usuario de tenant.
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->tenant_id && !Auth::user()->is_super_admin) {
                // Solo asigna si no se ha establecido explícitamente.
                $model->tenant_id = $model->tenant_id ?? Auth::user()->tenant_id;
            }
        });
    }
}