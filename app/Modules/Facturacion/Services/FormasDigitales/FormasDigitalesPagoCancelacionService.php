<?php

namespace App\Modules\Facturacion\Services\FormasDigitales;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Services\Contracts\PagoCancelacionServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FormasDigitalesPagoCancelacionService implements PagoCancelacionServiceInterface
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

        // URL para cancelación de Retenciones y Pagos según la documentación
        $this->apiUrl = $datosFiscales->en_pruebas
            ? 'https://dev.facturacfdi.mx:8081/retenciones/wscancelaretenciones?wsdl'
            : 'https://www.facturacfdi.mx/retenciones/wscancelaretenciones?wsdl';

        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function cancelarPago(Pago $pago): object
    {
        $datosFiscales = $this->credentialService->getDatosFiscales();
        $credential = $this->credentialService->getCredential();

        $publicKey = base64_encode($credential->certificate()->pem());
        $privateKey = base64_encode($credential->privateKey()->pem());
        $csdPassword = $credential->privateKey()->passPhrase();

        // Según la documentación, los parámetros son: folios, password_certs, password_servicio, privatekey, publickey, rfc, usuario_servicio
        $soapRequest = $this->buildSoapRequest(
            $pago->uuid_fiscal,
            $csdPassword,
            $this->apiPassword,
            $privateKey,
            $publicKey,
            $datosFiscales->rfc,
            $this->apiUser
        );

        $response = Http::withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
            ->withBody($soapRequest, 'text/xml')
            ->post($this->apiUrl);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión en cancelación de pago con Formas Digitales', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC para cancelación de pago: ' . $response->reason());
        }

        return $this->parseSoapResponse($response->body());
    }

    private function buildSoapRequest(string $uuid, string $csdPassword, string $apiPassword, string $privateKey, string $publicKey, string $rfc, string $apiUser): string
    {
        return <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:can="http://cancelacion.forcogsa.com/">
   <soapenv:Header/>
   <soapenv:Body>
      <can:cancela>
         <request_cancelacion>
            <folios>{$uuid}</folios>
            <password_certs>{$csdPassword}</password_certs>
            <password_servicio>{$apiPassword}</password_servicio>
            <privatekey>{$privateKey}</privatekey>
            <publickey>{$publicKey}</publickey>
            <rfc>{$rfc}</rfc>
            <usuario_servicio>{$apiUser}</usuario_servicio>
         </request_cancelacion>
      </can:cancela>
   </soapenv:Body>
</soapenv:Envelope>
XML;
    }

    private function parseSoapResponse(string $responseBody): object
    {
        $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $responseBody);
        $xml = new \SimpleXMLElement($xmlString);

        $returnNode = $xml->Body->cancelaresponse->return;
        $folioNode = $returnNode->folios;

        $codigoEstatus = (string)($folioNode->estatusuuid ?? $returnNode->codigo_estatus ?? '999');
        $mensaje = (string)($folioNode->mensaje ?? 'Error desconocido en la respuesta del PAC.');

        // Según la documentación de ejemplo, 201 es "UUID Cancelado"
        if ($codigoEstatus === '201') {
            return (object) [
                'success' => true,
                'message' => "[$codigoEstatus] $mensaje",
                'acuse' => (string)($returnNode->asXML() ?? ''),
            ];
        }

        return (object) [
            'success' => false,
            'message' => "Error de Formas Digitales: [$codigoEstatus] $mensaje",
            'acuse' => null,
        ];
    }
}
