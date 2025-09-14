<?php

namespace App\Modules\Facturacion\Models\Complemento\Nomina;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle extends Model
{
    use HasFactory;

    protected $table = 'nominas_recibo_detalles';

    protected $fillable = [
        'nominas_recibo_id',
        'tipo',
        'clave',
        'concepto',
        'importe_gravado',
        'importe_exento',
        'sat_tipo_clave',
    ];

    public function recibo()
    {
        return $this->belongsTo(Recibo::class, 'nominas_recibo_id');
    }
}
