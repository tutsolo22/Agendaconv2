<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LicenciaHistorial extends Model
{
    use HasFactory;

    // Como el nombre de la tabla no sigue la convención de Laravel (plural de LicenciaHistorial),
    // debemos especificarlo manualmente.
    protected $table = 'licencia_historial';

    protected $fillable = [
        'licencia_id',
        'accion',
        'detalles',
        'realizado_por',
    ];

    public function licencia(): BelongsTo
    {
        return $this->belongsTo(Licencia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'realizado_por');
    }
}