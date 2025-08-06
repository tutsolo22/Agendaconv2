<?php

namespace App\Modules\Facturacion\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfdiConcepto extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'facturacion_cfdi_conceptos';

    protected $fillable = [
        'cfdi_id',
        'clave_prod_serv',
        'cantidad',
        'clave_unidad',
        'descripcion',
        'valor_unitario',
        'importe',
        'impuestos',
    ];

    public function cfdi(): BelongsTo
    {
        return $this->belongsTo(Cfdi::class);
    }
}