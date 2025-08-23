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
        'driver', // Campo nuevo para identificar el servicio (ej: 'edicom', 'sw_sapiens')
        'rfc',
        'url_produccion',
        'url_pruebas',
        'credentials', // Campo nuevo para almacenar las credenciales en formato JSON
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'credentials' => 'encrypted:array', // Encripta el JSON completo para máxima seguridad
    ];
}
