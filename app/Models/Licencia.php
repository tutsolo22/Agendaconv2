<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Licencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'modulo_id',
        'fecha_inicio',
        'fecha_fin',
        'limite_usuarios',
        'is_active',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(LicenciaHistorial::class);
    }
}