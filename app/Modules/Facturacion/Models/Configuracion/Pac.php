<?php

namespace App\Modules\Facturacion\Models\Configuracion;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pac extends Model
{
    use HasFactory;

    protected $table = 'facturacion_pacs';

    protected $fillable = [
        'nombre',
        'rfc',
        'url_produccion',
        'url_pruebas',
        'usuario',
        'password',
        'is_active',
    ];

    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => encrypt($value),
            get: fn ($value) => decrypt($value)
        );
    }
}
