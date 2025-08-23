<?php

namespace App\Modules\Facturacion\Services\FormasDigitales;

use CfdiUtils\Cfdi as CfdiReader;
use App\Modules\Facturacion\Services\Contracts\TimbradoServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use App\Modules\Facturacion\Services\CfdiCreatorHelper;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FormasDigitalesTimbradoService implements TimbradoServiceInterface
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

        // Según la documentación, la URL para timbrado sin token es WSTimbradoCFDIService
        $this->apiUrl = $datosFiscales->en_pruebas ? 'https://dev33.facturacfdi.mx/WSTimbradoCFDIService?wsdl' : 'https://v33.facturacfdi.mx/WSTimbradoCFDIService?wsdl';
        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function timbrar(array $cfdiData): object
    {
        // 1. Crear el XML sellado usando nuestro helper existente.
        $xmlSellado = CfdiCreatorHelper::crearXml($cfdiData, $this->credentialService);

        // 2. Construir el cuerpo de la petición SOAP como un string.
        $soapRequest = $this->buildSoapRequest($xmlSellado);

        // 3. Enviar la petición SOAP al endpoint del PAC.
        $response = Http::withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
            ->withBody($soapRequest, 'text/xml')
            ->post($this->apiUrl);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión con Formas Digitales', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC: ' . $response->reason());
        }

        // 4. Procesar la respuesta SOAP.
        return $this->parseSoapResponse($response->body());
    }

    private function buildSoapRequest(string $xmlSellado): string
    {
        // Escapamos caracteres especiales en el XML para que sea un string válido dentro del CDATA
        $comprobanteCdata = '<![CDATA[' . $xmlSellado . ']]>';

        return <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wser="http://wservicios/">
   <soapenv:Header/>
   <soapenv:Body>
      <wser:TimbrarCFDI>
         <accesos>
            <password>{$this->apiPassword}</password>
            <usuario>{$this->apiUser}</usuario>
         </accesos>
         <comprobante>{$comprobanteCdata}</comprobante>
      </wser:TimbrarCFDI>
   </soapenv:Body>
</soapenv:Envelope>
XML;
    }

    private function parseSoapResponse(string $responseBody): object
    {
        // Usamos SimpleXMLElement para parsear el XML de la respuesta
        // Se eliminan los prefijos de namespace (S:, ns2:) para un acceso más fácil
        $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $responseBody);
        $xml = new \SimpleXMLElement($xmlString);

        $acuse = $xml->Body->TimbrarCFDIResponse->acuseCFDI;

        if (isset($acuse->codigo) && (string)$acuse->codigo !== '0') {
            return (object) [
                'success' => false,
                'message' => "Error de Formas Digitales: [{$acuse->codigo}] {$acuse->mensaje}",
            ];
        }

        $xmlTimbrado = (string)$acuse->xmlTimbrado;
        $cfdiReader = \CfdiUtils\Cfdi::newFromString($xmlTimbrado);
        // Se busca el nodo del Timbre Fiscal Digital. El método getTimbreFiscalDigital no existe en la clase Cfdi.
        // La forma correcta es buscar el nodo dentro del complemento.
        $tfd = $cfdiReader->getNode()->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');

        return (object) [
            'success' => true,
            'uuid' => $tfd ? $tfd['UUID'] : '',
            'xml' => $xmlTimbrado,
            'message' => 'Timbrado con Formas Digitales exitoso.',
        ];
    }
}