<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Si la aplicación se está ejecutando en la consola (ej: artisan), no aplicamos el scope.
        if (app()->runningInConsole()) {
            return;
        }

        // Esta es la lógica definitiva que rompe el ciclo.
        // No preguntamos por el ROL, sino por la existencia de un tenant_id en el usuario autenticado.
        // Un Super-Admin tiene tenant_id=NULL, por lo que el scope no se le aplicará.
        // Un usuario de tenant SIEMPRE tiene un tenant_id, por lo que el scope se le aplicará.
        // Esto es seguro porque Auth::id() y Auth::user()->tenant_id no dependen de otras consultas.
        if (Auth::check() && $tenantId = Auth::user()->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', $tenantId);
        }
    }
}