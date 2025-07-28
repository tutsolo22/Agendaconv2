<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait TenantScoped
{
    protected static function bootTenantScoped(): void
    {
        // Aplica el scope global para aislar los datos por tenant.
        static::addGlobalScope(new TenantScope);

        // Asigna automáticamente el tenant_id al crear un nuevo registro.
        static::creating(function ($model) {
            // Aplicamos la misma lógica segura y desacoplada.
            // Si hay un usuario logueado y este tiene un tenant_id, se lo asignamos al nuevo modelo.
            // Esto no afecta al Super-Admin (tenant_id es null) y funciona para todos los usuarios de tenants.
            if (Auth::check() && $tenantId = Auth::user()->tenant_id) {
                $model->tenant_id = $model->tenant_id ?? $tenantId;
            }
        });
    }
}