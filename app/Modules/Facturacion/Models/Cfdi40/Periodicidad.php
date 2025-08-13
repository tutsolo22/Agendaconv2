<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class Periodicidad extends Model
{
    protected $table = 'sat_cfdi_40_periodicidades';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
