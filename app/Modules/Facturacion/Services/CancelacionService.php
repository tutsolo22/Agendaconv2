<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Services\Contracts\CancelacionServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class CancelacionService
{
    protected CancelacionServiceInterface $cancelacionProvider;

    public function __construct(CancelacionServiceInterface $cancelacionProvider)
    {
        $this->cancelacionProvider = $cancelacionProvider;
    }

    public function cancelarCfdi(Cfdi $cfdi, string $motivo, ?string $folioSustitucion = null): object
    {
        if ($cfdi->status !== 'timbrado') {
            throw new Exception("Solo se pueden cancelar CFDI que han sido timbrados. Estado actual: {$cfdi->status}");
        }

        $resultado = $this->cancelacionProvider->cancelar($cfdi->uuid_fiscal, $motivo, $folioSustitucion);

        if ($resultado->success) {
            DB::transaction(function () use ($cfdi, $motivo, $resultado) {
                $cfdi->update([
                    'status' => 'cancelado',
                    'cancelacion_motivo' => $motivo,
                    'cancelacion_fecha' => now(),
                    'cancelacion_acuse' => $resultado->acuse,
                ]);
            });
        }

        return $resultado;
    }
}