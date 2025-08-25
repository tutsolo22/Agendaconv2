<?php

namespace App\Modules\Facturacion\Services\Contracts;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;

interface PagoTimbradoServiceInterface
{
    /**
     * Timbra un complemento de pago utilizando el servicio del PAC.
     *
     * @param Pago $pago El modelo del pago a timbrar.
     * @return object Un objeto estandarizado con el resultado de la operación.
     *                Ej: (object) ['success' => bool, 'uuid' => ?string, 'xml' => ?string, 'message' => string]
     */
    public function timbrarPago(Pago $pago): object;
}