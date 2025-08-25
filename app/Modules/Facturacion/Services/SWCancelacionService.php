<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Services\Contracts\CancelacionServiceInterface;

class SWCancelacionService implements CancelacionServiceInterface {
    public function __construct(SatCredentialService $credentialService) {}
    public function cancelar(string $uuid, string $motivo, ?string $folioSustitucion = null): object {
        return (object)['success' => false, 'message' => 'La cancelación para SW Sapiens aún no está implementada.'];
    }
}

