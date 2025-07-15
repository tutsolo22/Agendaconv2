<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'is_active',
    ];

    public function licencias(): HasMany
    {
        return $this->hasMany(Licencia::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'modulo_tenant');
    }
}