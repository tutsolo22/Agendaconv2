<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercancia extends Model
{
    use HasFactory;

    protected $table = 'cp_mercancia';

    protected $fillable = [
        'mercancias_id',
        'bienes_transp',
        'descripcion',
        'cantidad',
        'clave_unidad',
        'unidad',
        'dimensiones',
        'material_peligroso',
        'cve_material_peligroso',
        'embalaje',
        'descrip_embalaje',
        'peso_en_kg',
        'valor_mercancia',
        'moneda',
        'fraccion_arancelaria',
        'uuid_comercio_ext',
    ];

    public function mercancias()
    {
        return $this->belongsTo(Mercancias::class);
    }
}
