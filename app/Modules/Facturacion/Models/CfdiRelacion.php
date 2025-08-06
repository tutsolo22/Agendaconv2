<?php

namespace App\Modules\Facturacion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CfdiRelacion extends Model
{
    use HasFactory;

    protected $table = 'facturacion_cfdi_relaciones';

    protected $fillable = ['cfdi_id', 'tipo_relacion', 'cfdi_relacionado_uuid'];

    public function cfdi(): BelongsTo
    {
        return $this->belongsTo(Cfdi::class);
    }
}