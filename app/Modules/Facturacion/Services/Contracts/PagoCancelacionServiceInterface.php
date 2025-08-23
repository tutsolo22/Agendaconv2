<?php

namespace App\Modules\Facturacion\Services\Contracts;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;

interface PagoCancelacionServiceInterface
{
    /**
     * Cancela un Complemento de Pago utilizando el servicio del PAC.
     *
     * @param Pago $pago El modelo del pago a cancelar.
     * @return object Un objeto estandarizado con el resultado de la operación.
     *                Ej: (object) ['success' => bool, 'message' => string, 'acuse' => ?string]
     */
    public function cancelarPago(Pago $pago): object;
}