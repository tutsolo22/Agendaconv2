<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercancias extends Model
{
    use HasFactory;

    protected $table = 'cp_mercancias';

    protected $fillable = [
        'carta_porte_id',
        'peso_bruto_total',
        'unidad_peso',
        'peso_neto_total',
        'num_total_mercancias',
        'cargo_por_tasacion',
    ];

    public function cartaPorte()
    {
        return $this->belongsTo(CartaPorte::class);
    }

    public function mercancia()
    {
        return $this->hasMany(Mercancia::class);
    }
}
