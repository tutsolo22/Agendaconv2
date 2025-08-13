<?php

namespace App\Modules\Facturacion\Models\Cfdi40;

use Illuminate\Database\Eloquent\Model;

class ProductoServicio extends Model
{
    protected $table = 'sat_cfdi_40_productos_servicios';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'id';
    public $timestamps = false;
}