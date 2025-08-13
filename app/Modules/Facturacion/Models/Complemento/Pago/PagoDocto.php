<?php

namespace App\Modules\Facturacion\Models\Complemento\Pago;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDocto extends Model
{
    use HasFactory;

    protected $table = 'facturacion_pago_doctos';

    protected $fillable = [
        'pago_id', 'id_documento', 'serie', 'folio', 'moneda_dr',
        'tipo_cambio_dr', 'num_parcialidad', 'imp_saldo_ant',
        'imp_pagado', 'imp_saldo_insoluto', 'objeto_imp_dr'
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }
}
