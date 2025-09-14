<?php

namespace App\Modules\Facturacion\Models\CartaPorte;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartaPorte extends Model
{
    use HasFactory;

    protected $table = 'cp_cartaporte';

    protected $fillable = [
        'facturacion_cfdi_id',
        'version',
        'transp_internac',
        'entrada_salida_merc',
        'pais_origen_destino',
        'via_entrada_salida',
        'total_dist_rec',
        'id_ccp',
        'regimen_aduanero',
        'tipo_materia',
        'descripcion_materia',
        'nombre_figura',
        'rfc_figura',
        'num_reg_id_trib_figura',
        'residencia_fiscal_figura',
        'logistica_inversa_recoleccion_devolucion',
        'status',
    ];

    public function cfdi()
    {
        return $this->belongsTo(\App\Modules\Facturacion\Models\Cfdi::class, 'facturacion_cfdi_id');
    }

    public function ubicaciones()
    {
        return $this->hasMany(Ubicacion::class);
    }

    public function mercancias()
    {
        return $this->hasOne(Mercancias::class);
    }

    public function autotransporte()
    {
        return $this->hasOne(Autotransporte::class);
    }

    public function figuraTransporte()
    {
        return $this->hasOne(FiguraTransporte::class);
    }
}