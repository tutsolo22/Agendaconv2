<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sucursal extends Model
{
    use HasFactory, TenantScoped;

    /**
     * The table associated with the model.
     *
     * By default, Laravel will look for the English plural 'sucursals'.
     * We need to specify the correct Spanish plural table name 'sucursales'.
     *
     * @var string
     */
    protected $table = 'sucursales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'is_active',
        'tenant_id',
    ];
}