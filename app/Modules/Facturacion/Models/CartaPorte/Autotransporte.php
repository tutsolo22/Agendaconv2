<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autotransporte extends Model
{
    use HasFactory;

    protected $table = 'cp_autotransporte';

    protected $fillable = [
        'carta_porte_id',
        'perm_sct',
        'num_permiso_sct',
        'nombre_aseg',
        'num_poliza_seguro',
        'config_vehicular',
        'placa_vm',
        'anio_modelo_vm',
        'subtipo_rem',
        'placa',
    ];

    public function cartaPorte()
    {
        return $this->belongsTo(CartaPorte::class);
    }
}
