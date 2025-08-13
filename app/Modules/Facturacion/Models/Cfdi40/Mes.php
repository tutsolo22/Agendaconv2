<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class Mes extends Model
{
    protected $table = 'sat_cfdi_40_meses';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

