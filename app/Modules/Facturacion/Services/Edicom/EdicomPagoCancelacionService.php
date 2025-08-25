<?php

namespace App\Modules\Facturacion\Services\Edicom;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Services\Contracts\PagoCancelacionServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EdicomPagoCancelacionService implements PagoCancelacionServiceInterface
{
    protected string $apiUrl;
    protected string $apiUser;
    protected string $apiPassword;
    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
        $pacConfig = $this->credentialService->getPacActivo();
        $datosFiscales = $this->credentialService->getDatosFiscales();

        // **¡IMPORTANTE!** Esta URL es una suposición. Debes verificar la URL correcta
        // para la cancelación de Retenciones/Pagos en la documentación de EDICOM.
        // Podría ser algo como '.../payments/cancel' o '.../retenciones/cancel'.
        $baseUrl = $datosFiscales->en_pruebas ? $pacConfig->url_pruebas : $pacConfig->url_produccion;
        $this->apiUrl = rtrim($baseUrl, '/') . '/payments/cancel';

        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function cancelarPago(Pago $pago): object
    {
        // El payload para cancelar un pago suele ser más simple, solo el UUID.
        $payload = [
            'uuid' => $pago->uuid_fiscal,
            'rfc' => $this->credentialService->getDatosFiscales()->rfc,
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'X-CFDI-USER' => $this->apiUser,
            'X-CFDI-PASSWORD' => $this->apiPassword,
        ])->post($this->apiUrl, $payload);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión en cancelación de pago con EDICOM', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC (EDICOM) para cancelación de pago: ' . $response->reason());
        }

        $data = $response->json();

        return (object) [
            'success' => $response->successful() && ($data['status'] ?? 'error') === 'success',
            'message' => $data['message'] ?? 'Error desconocido en la respuesta de EDICOM.',
            'acuse' => $data['data']['acuse'] ?? null,
        ];
    }
}