<?php

namespace App\Modules\Facturacion\Services\Edicom;

use App\Modules\Facturacion\Services\Contracts\CancelacionServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class EdicomCancelacionService implements CancelacionServiceInterface
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

        // Asumimos que la URL de cancelación es la misma que la de timbrado, pero con /cancel al final.
        // Esto puede variar según la documentación específica de EDICOM.
        $baseUrl = $datosFiscales->en_pruebas ? $pacConfig->url_pruebas : $pacConfig->url_produccion;
        $this->apiUrl = rtrim($baseUrl, '/') . '/cancel';

        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function cancelar(string $uuid, string $motivo, ?string $folioSustitucion = null): object
    {
        $datosFiscales = $this->credentialService->getDatosFiscales();

        $payload = [
            'rfc' => $datosFiscales->rfc,
            'uuid' => $uuid,
            'motivo' => $motivo,
        ];

        if ($motivo === '01' && $folioSustitucion) {
            $payload['folioSustitucion'] = $folioSustitucion;
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'X-CFDI-USER' => $this->apiUser,
            'X-CFDI-PASSWORD' => $this->apiPassword,
        ])->post($this->apiUrl, $payload);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión en cancelación con EDICOM', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC (EDICOM) para cancelación: ' . $response->reason());
        }

        $data = $response->json();

        return (object) [
            'success' => $response->successful() && ($data['status'] ?? 'error') === 'success',
            'message' => $data['message'] ?? 'Error desconocido en la respuesta de EDICOM.',
            'acuse' => $data['data']['acuse'] ?? null,
        ];
    }
}
