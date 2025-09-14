<?php

namespace App\Modules\Facturacion\Models\Complemento\Nomina;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incapacidad extends Model
{
    use HasFactory;

    protected $table = 'nominas_incapacidades';

    protected $fillable = [
        'nominas_recibo_id',
        'sat_tipo_incapacidad_id',
        'dias_incapacidad',
        'descuento',
    ];

    public function recibo()
    {
        return $this->belongsTo(Recibo::class, 'nominas_recibo_id');
    }
}
