<?php

namespace App\Modules\Facturacion\Services\FormasDigitales;

use App\Modules\Facturacion\Services\Contracts\CancelacionServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FormasDigitalesCancelacionService implements CancelacionServiceInterface
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

        // URL específica para el servicio de cancelación 4.0
        $this->apiUrl = $datosFiscales->en_pruebas
            ? 'https://dev-cancelacion.facturacfdi.mx/WSCancelacion40Service?wsdl'
            : 'https://cancelacion.facturacfdi.mx/WSCancelacion40Service?wsdl';

        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function cancelar(string $uuid, string $motivo, ?string $folioSustitucion = null): object
    {
        $datosFiscales = $this->credentialService->getDatosFiscales();
        $credential = $this->credentialService->getCredential();

        // 1. Obtener CSD y codificarlos en Base64
        $publicKey = base64_encode($credential->certificate()->pem());
        $privateKey = base64_encode($credential->privateKey()->pem());

        // 2. Construir la petición SOAP
        $soapRequest = $this->buildSoapRequest($datosFiscales->rfc, $uuid, $motivo, $folioSustitucion, $publicKey, $privateKey, $credential->privateKey()->passPhrase());

        // 3. Enviar la petición
        $response = Http::withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
            ->withBody($soapRequest, 'text/xml')
            ->post($this->apiUrl);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión en cancelación con Formas Digitales', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC para cancelación: ' . $response->reason());
        }

        // 4. Procesar la respuesta
        return $this->parseSoapResponse($response->body());
    }

    private function buildSoapRequest(string $rfcEmisor, string $uuid, string $motivo, ?string $folioSustitucion, string $publicKey, string $privateKey, string $csdPassword): string
    {
        // El folio de sustitución solo se incluye si el motivo es "01"
        $folioSustitucionXml = $motivo === '01' ? "<folioSustitucion>{$folioSustitucion}</folioSustitucion>" : "<folioSustitucion/>";

        return <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wser="http://wservicios/">
   <soapenv:Header/>
   <soapenv:Body>
      <wser:Cancelacion40_1>
         <accesos>
            <password>{$this->apiPassword}</password>
            <usuario>{$this->apiUser}</usuario>
         </accesos>
         <rfcEmisor>{$rfcEmisor}</rfcEmisor>
         <fecha>{$this->credentialService->getDatosFiscales()->fecha_emision->format('Y-m-d\TH:i:s')}</fecha>
         <folios>
            <uuid>{$uuid}</uuid>
            <motivo>{$motivo}</motivo>
            {$folioSustitucionXml}
         </folios>
         <publicKey>{$publicKey}</publicKey>
         <privateKey>{$privateKey}</privateKey>
         <password>{$csdPassword}</password>
      </wser:Cancelacion40_1>
   </soapenv:Body>
</soapenv:Envelope>
XML;
    }

    private function parseSoapResponse(string $responseBody): object
    {
        $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $responseBody);
        $xml = new \SimpleXMLElement($xmlString);

        $returnNode = $xml->Body->Cancelacion40_1Response->response;
        $folioNode = $returnNode->folios;

        $codigoEstatus = (string)($folioNode->estatusUUID ?? $returnNode->codigo_estatus ?? '999');
        $mensaje = (string)($folioNode->mensaje ?? 'Error desconocido en la respuesta del PAC.');

        // 201: Solicitud de cancelación aceptada.
        // 202: Folio Fiscal previamente cancelado.
        if ($codigoEstatus === '201' || $codigoEstatus === '202') {
            return (object) [
                'success' => true,
                'message' => "[$codigoEstatus] $mensaje",
                'acuse' => (string)($returnNode->acuse ?? ''),
            ];
        }

        return (object) [
            'success' => false,
            'message' => "Error de Formas Digitales: [$codigoEstatus] $mensaje",
            'acuse' => null,
        ];
    }
}