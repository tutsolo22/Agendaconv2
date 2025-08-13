<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class ClaveUnidad extends Model
{
    protected $table = 'sat_cfdi_40_claves_unidades';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

