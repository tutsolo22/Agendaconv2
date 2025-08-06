<?php

namespace App\Models\Sat;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RegimenFiscal
 *
 * Representa una entrada del catálogo de Regímenes Fiscales del SAT.
 * La tabla es poblada por el paquete phpcfdi/sat-catalogos-populate.
 *
 * @package App\Models\Sat
 */
class RegimenFiscal extends Model
{
    /**
     * El nombre de la tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'sat_regimenes_fiscales';

    public $timestamps = false;
}