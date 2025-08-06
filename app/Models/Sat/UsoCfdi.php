<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UsoCfdi
 *
 * Representa una entrada del catálogo de Usos de CFDI del SAT.
 * La tabla es poblada por el paquete phpcfdi/sat-catalogos-populate.
 *
 * @package App\Models\Sat
 */
class UsoCfdi extends Model
{
    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sat_usos_cfdi';

    public $timestamps = false;
}

