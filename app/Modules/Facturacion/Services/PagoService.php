<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Services\Contracts\PagoCancelacionServiceInterface;
use App\Modules\Facturacion\Services\Contracts\PagoTimbradoServiceInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PagoService
{
    protected PagoTimbradoServiceInterface $pagoProvider;
    protected ?PagoCancelacionServiceInterface $pagoCancelacionProvider;

    public function __construct(PagoTimbradoServiceInterface $pagoProvider, ?PagoCancelacionServiceInterface $pagoCancelacionProvider = null)
    {
        $this->pagoProvider = $pagoProvider;
        $this->pagoCancelacionProvider = $pagoCancelacionProvider;
    }

    public function timbrar(Pago $pago): object
    {
        if ($pago->status !== 'borrador') {
            throw new Exception("Solo se pueden timbrar complementos en estado de borrador.");
        }

        $resultado = $this->pagoProvider->timbrarPago($pago);

        if (!$resultado->success) {
            // El servicio del PAC ya debería lanzar una excepción o devolver un mensaje claro.
            // Devolvemos el objeto de resultado para que el controlador pueda mostrar el mensaje.
            return $resultado;
        }

        // Actualizar el modelo de Pago con los datos del timbrado
        $pago->update([
            'status' => 'timbrado',
            'uuid_fiscal' => $resultado->uuid,
            'path_xml' => $this->guardarArchivo($pago, $resultado->xml, 'xml'),
        ]);

        return $resultado;
    }

    public function cancelar(Pago $pago): object
    {
        if ($pago->status !== 'timbrado') {
            throw new Exception("Solo se pueden cancelar complementos de pago timbrados.");
        }

        if (!$this->pagoCancelacionProvider) {
            throw new Exception("No se ha configurado un proveedor de cancelación de pagos.");
        }

        $resultado = $this->pagoCancelacionProvider->cancelarPago($pago);

        if ($resultado->success) {
            DB::transaction(function () use ($pago, $resultado) {
                $pago->update([
                    'status' => 'cancelado',
                    'cancelacion_fecha' => now(),
                    'cancelacion_acuse' => $resultado->acuse,
                ]);
            });
        }

        return $resultado;
    }

    private function guardarArchivo(Pago $pago, string $content, string $extension): string
    {
        $filename = "{$pago->serie}-{$pago->folio}.{$extension}";
        // Usamos el helper global de tenancy para obtener el ID del tenant actual de forma segura.
        $path = "tenants/" . \tenant('id') . "/pagos/{$filename}";
        Storage::put($path, $content);
        return $path;
    }
}