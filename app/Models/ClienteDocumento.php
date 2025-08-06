<?php

namespace App\Models;

use App\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDocumento extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'cliente_documentos';

    protected $fillable = [
        'cliente_id',
        'modulo_id',
        'subido_por_user_id',
        'nombre_original',
        'ruta_archivo',
        'mime_type',
        'descripcion',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }

    public function subidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subido_por_user_id');
    }
}