<?php

namespace App\Modules\Facturacion\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SerieFolio extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'facturacion_series_folios';

    protected $fillable = [
        'sucursal_id',
        'serie',
        'folio_actual',
        'is_active',
    ];

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Sucursal::class);
    }
}