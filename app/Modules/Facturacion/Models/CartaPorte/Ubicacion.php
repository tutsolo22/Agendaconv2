<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'cp_ubicaciones';

    protected $fillable = [
        'carta_porte_id',
        'tipo_ubicacion',
        'id_ubicacion',
        'rfc_remitente_destinatario',
        'nombre_remitente_destinatario',
        'num_reg_id_trib',
        'residencia_fiscal',
        'fecha_hora_salida_llegada',
        'distancia_recorrida',
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
