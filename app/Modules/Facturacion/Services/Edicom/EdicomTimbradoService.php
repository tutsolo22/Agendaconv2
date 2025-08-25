<?php

namespace App\Modules\Facturacion\Services\Edicom;

use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use App\Modules\Facturacion\Services\CfdiCreatorHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EdicomTimbradoService implements TimbradoServiceInterface
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

        $this->apiUrl = $datosFiscales->en_pruebas ? $pacConfig->url_pruebas : $pacConfig->url_produccion;
        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    /**
     * Timbra un CFDI utilizando el servicio de EDICOM.
     *
     * @param array $cfdiData Los datos para construir el CFDI.
     * @return object El resultado estandarizado del timbrado.
     */
    public function timbrar(array $cfdiData): object
    {
        // 1. Construir el XML a partir del array $cfdiData.
        $xmlString = CfdiCreatorHelper::crearXml($cfdiData, $this->credentialService);

        // 2. Enviar la petición a la API de EDICOM.
        // La documentación de EDICOM especificará el formato exacto (JSON, form-data, etc.).
        // Aquí simulamos un envío JSON.
        $response = Http::withBasicAuth($this->apiUser, $this->apiPassword)
            ->post($this->apiUrl, [
                'xml_base64' => base64_encode($xmlString),
            ]);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de EDICOM', $response->json());
            return (object) [
                'success' => false,
                'message' => 'El servicio de EDICOM devolvió un error: ' . $response->reason(),
            ];
        }

        // 3. Procesar la respuesta y devolver un objeto estandarizado.
        $responseData = $response->json();
        return (object) [
            'success' => true,
            'uuid' => $responseData['uuid'] ?? null,
            'xml' => base64_decode($responseData['xml_timbrado_base64'] ?? ''), // EDICOM devuelve el XML en base64
            'message' => $responseData['message'] ?? 'Timbrado con EDICOM Exitoso',
        ];
    }
}