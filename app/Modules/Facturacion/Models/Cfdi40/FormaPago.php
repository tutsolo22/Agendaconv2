<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class FormaPago extends Model
{
    protected $table = 'sat_cfdi_40_formas_pago';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

