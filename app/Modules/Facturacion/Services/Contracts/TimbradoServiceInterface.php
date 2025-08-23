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
     * @param array $cfdiData Los datos para construir el CFDI.
     * @return object El resultado del timbrado (éxito, XML, UUID, etc.).
     */
    public function timbrar(array $cfdiData): object;
}