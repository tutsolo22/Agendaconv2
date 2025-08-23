<?php

namespace App\Modules\Facturacion\Services\Contracts;

interface CancelacionServiceInterface
{
    /**
     * Cancela un CFDI utilizando el servicio del PAC.
     *
     * @param string $uuid El UUID del CFDI a cancelar.
     * @param string $motivo El motivo de la cancelación (catálogo del SAT).
     * @param string|null $folioSustitucion El UUID del CFDI que sustituye al cancelado (si aplica).
     * @return object Un objeto estandarizado con el resultado de la operación.
     *                Ej: (object) ['success' => bool, 'message' => string, 'acuse' => ?string]
     */
    public function cancelar(string $uuid, string $motivo, ?string $folioSustitucion = null): object;
}