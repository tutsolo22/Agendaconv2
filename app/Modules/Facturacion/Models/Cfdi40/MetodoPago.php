<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'sat_cfdi_40_metodos_pago';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

