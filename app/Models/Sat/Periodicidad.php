<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Periodicidad
 *
 * Representa una entrada del catálogo de Periodicidades del SAT.
 * La tabla es poblada por el paquete phpcfdi/sat-catalogos-populate.
 *
 * @package App\Models\Sat
 */
class Periodicidad extends Model
{
    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sat_periodicidades';

    public $timestamps = false;
}

