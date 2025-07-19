<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Si el usuario está autenticado, tiene un tenant_id y NO es super_admin,
        // entonces aplicamos el filtro.
        // Esto asegura que los Super Admins puedan ver todos los registros.
        if (Auth::check() && Auth::user()->tenant_id && !Auth::user()->is_super_admin) {
            $builder->where($model->getTable() . '.tenant_id', Auth::user()->tenant_id);
        }
    }
}