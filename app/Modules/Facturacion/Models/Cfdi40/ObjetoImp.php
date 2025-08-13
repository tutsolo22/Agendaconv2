<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class ObjetoImp extends Model
{
    protected $table = 'sat_cfdi_40_objetos_impuestos';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

