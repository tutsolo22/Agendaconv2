<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SWTimbradoService implements TimbradoServiceInterface
{
    protected string $apiUrl;
    protected ?string $apiToken;
    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
        $pacConfig = $this->credentialService->getPacActivo();
        $datosFiscales = $this->credentialService->getDatosFiscales();

        $this->apiUrl = $datosFiscales->en_pruebas ? $pacConfig->url_pruebas : $pacConfig->url_produccion;
        $this->apiToken = $pacConfig->credentials['token'] ?? null;
    }

    public function timbrar(array $cfdiData): object
    {
        // 1. Construir el XML a partir del array $cfdiData.
        $xmlString = CfdiCreatorHelper::crearXml($cfdiData, $this->credentialService);

        // 2. Enviar la petición a la API de SW Sapiens.
        $response = Http::withToken($this->apiToken)
            ->withBody($xmlString, 'application/xml')
            ->post($this->apiUrl . '/v4/cfdi33/stamp/v4'); // Endpoint para CFDI 4.0

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de SW Sapiens', $response->json());
            return (object) [
                'success' => false,
                'message' => 'El servicio de SW Sapiens devolvió un error: ' . ($response->json()['message'] ?? $response->reason()),
            ];
        }

        // 3. Devolver una respuesta estandarizada
        $responseData = $response->json();
        return (object) [
            'success' => true,
            'uuid' => $responseData['data']['uuid'] ?? null,
            'xml' => $responseData['data']['cfdi'] ?? null, // El PAC devuelve el XML con el TFD
            'message' => $responseData['message'] ?? 'Timbrado con SW Sapiens Exitoso'
        ];
    }
}