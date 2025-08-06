<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'nombre_completo',
        'rfc',
        'email',
        'telefono',
        'direccion_fiscal',
        'tipo',
        // tenant_id es asignado automáticamente por el trait TenantScoped
    ];

    public function documentos(): HasMany
    {
        return $this->hasMany(ClienteDocumento::class);
    }
}