<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Mes
 *
 * Representa una entrada del catálogo de Meses del SAT.
 * La tabla es poblada por el paquete phpcfdi/sat-catalogos-populate.
 *
 * @package App\Models\Sat
 */
class Mes extends Model
{
    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sat_meses';

    public $timestamps = false;
}

