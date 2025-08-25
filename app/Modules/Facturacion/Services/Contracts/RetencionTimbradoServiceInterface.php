<?php

namespace App\Modules\Facturacion\Services\Contracts;

use App\Modules\Facturacion\Models\Retencion\Retencion;

interface RetencionTimbradoServiceInterface
{
    /**
     * Timbra una retención y devuelve un objeto con el resultado.
     *
     * @return object Con propiedades 'success' (bool), 'message' (string), 'uuid' (string) y 'xml' (string).
     */
    public function timbrarRetencion(Retencion $retencion): object;
}