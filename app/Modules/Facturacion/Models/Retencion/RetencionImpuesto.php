<?php

namespace App\Modules\Facturacion\Models\Retencion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RetencionImpuesto extends Model
{
    protected $table = 'facturacion_retencion_impuestos';

    protected $guarded = ['id'];

    public function retencion(): BelongsTo
    {
        return $this->belongsTo(Retencion::class);
    }
}