<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiguraTransporte extends Model
{
    use HasFactory;

    protected $table = 'cp_figura_transporte';

    protected $fillable = [
        'carta_porte_id',
        'tipo_figura',
        'rfc_figura',
        'num_licencia',
        'nombre_figura',
        'num_reg_id_trib_figura',
        'residencia_fiscal_figura',
        'calle',
        'numero_exterior',
        'numero_interior',
        'colonia',
        'localidad',
        'referencia',
        'municipio',
        'estado',
        'pais',
        'codigo_postal',
    ];

    public function cartaPorte()
    {
        return $this->belongsTo(CartaPorte::class);
    }
}
