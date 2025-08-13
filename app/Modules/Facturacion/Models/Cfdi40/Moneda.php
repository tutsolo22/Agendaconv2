<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class Moneda extends Model
{
    protected $table = 'sat_cfdi_40_monedas';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

