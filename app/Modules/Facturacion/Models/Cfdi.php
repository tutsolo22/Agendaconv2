<?php

namespace App\Modules\Facturacion\Models;

use App\Models\Cliente;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cfdi extends Model
{
    use HasFactory, TenantScoped, HasUuids;

    protected $table = 'facturacion_cfdis';

    protected $fillable = [
        'cliente_id',
        'serie_folio_id',
        'serie',
        'folio',
        'tipo_comprobante',
        'forma_pago',
        'metodo_pago',
        'uso_cfdi',
        'moneda',
        'saldo_pendiente',
        'subtotal',
        'impuestos',
        'total',
        'uuid_fiscal',
        'status',
        'es_factura_global',
        'periodicidad',
        'meses',
        'anio',
        'motivo_cancelacion',
        'fecha_cancelacion',
        'path_xml',
        'path_pdf',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function conceptos(): HasMany
    {
        return $this->hasMany(CfdiConcepto::class);
    }

    public function relaciones(): HasMany
    {
        return $this->hasMany(CfdiRelacion::class);
    }

    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    public function ventasGlobales(): HasMany
    {
        return $this->hasMany(VentaPublico::class, 'cfdi_global_id');
    }
}