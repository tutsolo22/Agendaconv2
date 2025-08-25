<?php

namespace App\Modules\Facturacion\Models\Retencion;

use App\Models\Cliente;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retencion extends Model
{
    use TenantScoped;

    protected $table = 'facturacion_retenciones';

    protected $guarded = ['id'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function serieFolio(): BelongsTo
    {
        return $this->belongsTo(SerieFolio::class);
    }

    public function impuestos(): HasMany
    {
        return $this->hasMany(RetencionImpuesto::class);
    }
}