<?php

namespace App\Modules\Facturacion\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaPublico extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'facturacion_ventas_publico';

    protected $fillable = ['folio_venta', 'fecha', 'total', 'cfdi_global_id'];

    public function cfdiGlobal(): BelongsTo
    {
        return $this->belongsTo(Cfdi::class, 'cfdi_global_id');
    }
}
