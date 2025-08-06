<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MetodoPago
 *
 * Representa una entrada del catálogo de Métodos de Pago del SAT.
 * La tabla es poblada por el paquete phpcfdi/sat-catalogos-populate.
 *
 * @package App\Models\Sat
 */
class MetodoPago extends Model
{
    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sat_metodos_pago';

    public $timestamps = false;
}

