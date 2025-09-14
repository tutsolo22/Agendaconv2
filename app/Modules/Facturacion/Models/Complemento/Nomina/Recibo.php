<?php

namespace App\Modules\Facturacion\Models\Complemento\Nomina;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    use HasFactory;

    protected $table = 'nominas_recibos';

    protected $fillable = [
        'tenant_id',
        'nominas_empleado_id',
        'uuid',
        'version',
        'serie',
        'folio',
        'status',
        'tipo_nomina',
        'fecha_pago',
        'fecha_inicial_pago',
        'fecha_final_pago',
        'num_dias_pagados',
        'sat_periodicidad_pago_id',
        'total_percepciones',
        'total_deducciones',
        'total_otros_pagos',
        'xml',
        'path_pdf',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'nominas_empleado_id');
    }

    public function detalles()
    {
        return $this->hasMany(Detalle::class, 'nominas_recibo_id');
    }

    public function incapacidades()
    {
        return $this->hasMany(Incapacidad::class, 'nominas_recibo_id');
    }
}
