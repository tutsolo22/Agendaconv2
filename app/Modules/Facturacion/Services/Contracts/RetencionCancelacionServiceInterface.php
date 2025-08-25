<?php

namespace App\Modules\Facturacion\Services\Contracts;

use App\Modules\Facturacion\Models\Retencion\Retencion;

interface RetencionCancelacionServiceInterface
{
    /**
     * Cancela una retención y devuelve un objeto con el resultado.
     *
     * @param Retencion $retencion
     * @param string $motivo
     * @return object Con propiedades 'success' (bool) y 'message' (string).
     */
    public function cancelarRetencion(Retencion $retencion, string $motivo): object;
}