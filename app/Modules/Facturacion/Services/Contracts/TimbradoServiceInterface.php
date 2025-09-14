<?php

namespace App\Modules\Facturacion\Services\Contracts;

/**
 * Interface TimbradoServiceInterface
 * Define el contrato para cualquier servicio que timbre un CFDI.
 */
interface TimbradoServiceInterface
{
    /**
     * Timbra un CFDI.
     * @param string $xmlSellado El contenido del XML del CFDI ya sellado.
     * @return object El resultado del timbrado (éxito, XML, UUID, etc.).
     */
    public function timbrar(string $xmlSellado): object;
}