<?php

namespace App\Modules\Facturacion\Services\FormasDigitales;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Services\Contracts\PagoTimbradoServiceInterface;
use App\Modules\Facturacion\Services\PagoCreatorHelper;
use App\Modules\Facturacion\Services\SatCredentialService;
use CfdiUtils\Cfdi as CfdiReader;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FormasDigitalesPagoTimbradoService implements PagoTimbradoServiceInterface
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

        // **CORRECCIÓN:** Un Complemento de Pago es un CFDI normal (Tipo P) y debe usar el endpoint de timbrado general, no el de retenciones.
        $this->apiUrl = $datosFiscales->en_pruebas
            ? 'https://dev33.facturacfdi.mx/WSTimbradoCFDIService?wsdl'
            : 'https://v33.facturacfdi.mx/WSTimbradoCFDIService?wsdl';

        $this->apiUser = $pacConfig->credentials['user'] ?? '';
        $this->apiPassword = $pacConfig->credentials['password'] ?? '';
    }

    public function timbrarPago(Pago $pago): object
    {
        $xmlSellado = PagoCreatorHelper::crearXml($pago, $this->credentialService);
        $soapRequest = $this->buildSoapRequest($xmlSellado);

        $response = Http::withHeaders(['Content-Type' => 'text/xml; charset=utf-8'])
            ->withBody($soapRequest, 'text/xml')
            ->post($this->apiUrl);

        if ($response->failed()) {
            Log::channel('facturacion')->error('Error de conexión en timbrado de pago con Formas Digitales', ['status' => $response->status(), 'body' => $response->body()]);
            throw new Exception('Error de conexión con el PAC para timbrado de pago: ' . $response->reason());
        }

        return $this->parseSoapResponse($response->body());
    }

    private function buildSoapRequest(string $xmlSellado): string
    {
        $comprobanteCdata = '<![CDATA[' . $xmlSellado . ']]>';
        // **CORRECCIÓN:** Se usa el método TimbrarCFDI, que es el correcto para un comprobante de pago.
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
        $xmlString = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$3", $responseBody);
        $xml = new \SimpleXMLElement($xmlString);

        // **CORRECCIÓN:** Se parsea la respuesta de TimbrarCFDIResponse y se busca el nodo acuseCFDI.
        $acuse = $xml->Body->TimbrarCFDIResponse->acuseCFDI ?? null;

        if (!$acuse || (isset($acuse->codigo) && (string)$acuse->codigo !== '0')) {
            $codigo = $acuse->codigo ?? 'N/A';
            $mensaje = $acuse->mensaje ?? 'El PAC no devolvió un acuse válido. Respuesta: ' . $responseBody;
            return (object) ['success' => false, 'message' => "Error de Formas Digitales: [{$codigo}] {$mensaje}"];
        }

        $xmlTimbrado = (string)($acuse->xmlTimbrado ?? '');
        if (empty($xmlTimbrado)) {
            return (object) ['success' => false, 'message' => 'El PAC no devolvió un XML timbrado. Respuesta: ' . $responseBody];
        }

        $cfdiReader = CfdiReader::newFromString($xmlTimbrado);
        // La búsqueda del TFD es correcta para un cfdi:Comprobante
        $tfd = $cfdiReader->getNode()->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');

        if (!$tfd) {
            return (object) ['success' => false, 'message' => 'El XML timbrado por el PAC no contiene un Timbre Fiscal Digital válido.'];
        }

        // **CORRECCIÓN:** Se completa el mensaje de éxito.
        return (object) [
            'success' => true,
            'uuid' => $tfd['UUID'],
            'xml' => $xmlTimbrado,
            'message' => 'Complemento de Pago timbrado con Formas Digitales.'
        ];
    }
}