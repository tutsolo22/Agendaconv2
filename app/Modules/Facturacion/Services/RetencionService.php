<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Retencion\Retencion;
use App\Modules\Facturacion\Services\Contracts\RetencionCancelacionServiceInterface;
use App\Modules\Facturacion\Services\Contracts\RetencionTimbradoServiceInterface;
use Exception;
use Illuminate\Support\Facades\Storage;

class RetencionService
{
    protected RetencionTimbradoServiceInterface $retencionProvider;
    protected ?RetencionCancelacionServiceInterface $retencionCancelacionProvider;

    public function __construct(RetencionTimbradoServiceInterface $retencionProvider, ?RetencionCancelacionServiceInterface $retencionCancelacionProvider = null)
    {
        $this->retencionProvider = $retencionProvider;
        $this->retencionCancelacionProvider = $retencionCancelacionProvider;
    }

    public function timbrar(Retencion $retencion): object
    {
        if ($retencion->status !== 'borrador') {
            throw new Exception("Solo se pueden timbrar retenciones en estado de borrador.");
        }

        $resultado = $this->retencionProvider->timbrarRetencion($retencion);

        if (!$resultado->success) {
            return $resultado;
        }

        // Actualizar el modelo de Retencion con los datos del timbrado
        $retencion->update([
            'status' => 'timbrado',
            'uuid_fiscal' => $resultado->uuid,
            'path_xml' => $this->guardarArchivo($retencion, $resultado->xml, 'xml'),
        ]);

        return $resultado;
    }

    private function guardarArchivo(Retencion $retencion, string $content, string $extension): string
    {
        $filename = "{$retencion->serie}-{$retencion->folio}.{$extension}";
        // Usamos el helper global de tenancy para obtener el ID del tenant actual de forma segura.
        $tenantId = \tenant('id');
        $path = "tenants/{$tenantId}/retenciones/{$filename}";
        Storage::put($path, $content);
        return $path;
    }
}