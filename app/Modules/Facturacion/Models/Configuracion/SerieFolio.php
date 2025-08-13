<?php

namespace App\Modules\Facturacion\Models\Configuracion;

use App\Models\Sucursal;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieFolio extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'facturacion_series_folios';

    protected $fillable = [
        'tenant_id',
        'serie',
        'folio_actual',
        'is_active',
        'sucursal_id',
        'tipo_comprobante'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function getTipoComprobanteTextoAttribute(): string
    {
        return match ($this->tipo_comprobante) {
            'I' => 'Ingreso (Factura)',
            'E' => 'Egreso (Nota de Crédito)',
            'P' => 'Pago',
            default => 'Desconocido',
        };
    }
}