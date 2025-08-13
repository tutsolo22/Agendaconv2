<?php

namespace App\Modules\Facturacion\Models\Complemento\Pago;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'facturacion_pagos';

    protected $fillable = [
        'cfdi_id', 'fecha_pago', 'forma_de_pago_p', 'moneda_p', 'monto', 'tipo_cambio_p'
    ];

    protected $casts = ['fecha_pago' => 'datetime'];

    public function cfdi(): BelongsTo
    {
        return $this->belongsTo(Cfdi::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(PagoDocto::class);
    }
}
