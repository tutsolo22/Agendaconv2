<?php

namespace App\Modules\Facturacion\Services\FormasDigitales;

use App\Modules\Facturacion\Models\Retencion\Retencion;
use App\Modules\Facturacion\Services\Contracts\RetencionTimbradoServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use CfdiUtils\CadenaOrigen\DOMBuilder;
use CfdiUtils\CadenaOrigen\CfdiDefaultLocations;
use CfdiUtils\Utils\PrivateKey;
use DOMDocument;
use Exception;
use SoapClient;

class FormasDigitalesRetencionService implements RetencionTimbradoServiceInterface
{
    protected SatCredentialService $credentialService;
    protected string $endpoint = 'https://dev.facturacfdi.mx:8081/retenciones/wsreciberetencionservice?wsdl';

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    public function timbrarRetencion(Retencion $retencion): object
    {
        try {
            $credentials = $this->credentialService->getCredentials();
            $xmlString = $this->buildXml($retencion, $credentials);

            $soapClient = new SoapClient($this->endpoint, ['trace' => 1]);
            $params = [
                'usuario' => $credentials->pac_user,
                'password' => $credentials->pac_password,
                'xmlRetencion' => $xmlString,
            ];

            $response = $soapClient->__soapCall('RecibeRetencion2_0', ['parameters' => $params]);

            if (isset($response->RecibeRetencion2_0Result->xmlRetencion_)) {
                $xmlTimbrado = $response->RecibeRetencion2_0Result->xmlRetencion_;

                // Extraer UUID del XML timbrado
                $dom = new DOMDocument();
                $dom->loadXML($xmlTimbrado);
                $uuid = $dom->getElementsByTagName('TimbreFiscalDigital')->item(0)->getAttribute('UUID');

                return (object)[
                    'success' => true,
                    'message' => 'Retención timbrada exitosamente.',
                    'uuid' => $uuid,
                    'xml' => $xmlTimbrado,
                ];
            } else {
                // Manejar errores del PAC
                $errorMessage = 'Error desconocido al timbrar la retención.';
                if (isset($response->RecibeRetencion2_0Result->mensajeError)) {
                    $errorMessage = $response->RecibeRetencion2_0Result->mensajeError;
                }
                return (object)[
                    'success' => false,
                    'message' => $errorMessage,
                    'uuid' => null,
                    'xml' => null,
                ];
            }

        } catch (Exception $e) {
            // Capturar cualquier excepción durante la llamada SOAP
            return (object)[
                'success' => false,
                'message' => 'Error al comunicarse con el PAC: ' . $e->getMessage(),
                'uuid' => null,
                'xml' => null,
            ];
        }
    }

    private function buildXml(Retencion $retencion, object $credentials): string
    {
        $retencion->load('cliente', 'impuestos');
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $root = $xml->createElement('retenciones:Retenciones');
        $xml->appendChild($root);

        // Atributos del nodo raíz
        $root->setAttribute('xmlns:retenciones', 'http://www.sat.gob.mx/esquemas/retencionpago/2');
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', 'http://www.sat.gob.mx/esquemas/retencionpago/2 http://www.sat.gob.mx/esquemas/retencionpago/2/retencionpagov2.xsd');
        $root->setAttribute('Version', '2.0');
        $root->setAttribute('FolioInt', $retencion->folio);
        $root->setAttribute('FechaExp', date('Y-m-d\TH:i:s', strtotime($retencion->fecha_exp)));
        $root->setAttribute('LugarExpRet', $credentials->codigo_postal);
        $root->setAttribute('CveRetenc', $retencion->cve_retenc);
        if ($retencion->desc_retenc) {
            $root->setAttribute('DescRetenc', $retencion->desc_retenc);
        }

        // Emisor
        $emisorNode = $xml->createElement('retenciones:Emisor');
        $emisorNode->setAttribute('RfcE', $credentials->rfc);
        $emisorNode->setAttribute('NomDenRazSocE', $credentials->razon_social);
        $emisorNode->setAttribute('RegimenFiscalE', $credentials->regimen_fiscal);
        $root->appendChild($emisorNode);

        // Receptor
        $receptorNode = $xml->createElement('retenciones:Receptor');
        $receptorNode->setAttribute('NacionalidadR', 'Nacional'); // Asumimos Nacional
        $root->appendChild($receptorNode);
        $receptorNacionalNode = $xml->createElement('retenciones:Nacional');
        $receptorNacionalNode->setAttribute('RfcR', $retencion->cliente->rfc);
        $receptorNacionalNode->setAttribute('NomDenRazSocR', $retencion->cliente->razon_social);
        $receptorNacionalNode->setAttribute('DomicilioFiscalR', $retencion->cliente->codigo_postal);
        $receptorNode->appendChild($receptorNacionalNode);

        // Periodo
        $periodoNode = $xml->createElement('retenciones:Periodo');
        $periodoNode->setAttribute('MesIni', date('m', strtotime($retencion->fecha_exp)));
        $periodoNode->setAttribute('MesFin', date('m', strtotime($retencion->fecha_exp)));
        $periodoNode->setAttribute('Ejerc', date('Y', strtotime($retencion->fecha_exp)));
        $root->appendChild($periodoNode);

        // Totales
        $totalesNode = $xml->createElement('retenciones:Totales');
        $totalesNode->setAttribute('montoTotOperacion', number_format($retencion->monto_total_operacion, 2, '.', ''));
        $totalesNode->setAttribute('montoTotGrav', '0.00'); // Asumiendo que no hay gravados para este tipo de retención
        $totalesNode->setAttribute('montoTotExent', number_format($retencion->monto_total_operacion, 2, '.', '')); // Asumiendo que todo es exento
        $totalesNode->setAttribute('montoTotRet', number_format($retencion->monto_total_retenido, 2, '.', ''));
        $root->appendChild($totalesNode);

        // Impuestos Retenidos
        $impuestosRetenidosNode = $xml->createElement('retenciones:ImpRetenidos');
        foreach ($retencion->impuestos as $impuesto) {
            $impRetenidoNode = $xml->createElement('retenciones:ImpRetenido');
            $impRetenidoNode->setAttribute('BaseRet', number_format($impuesto->base_ret, 2, '.', ''));
            $impRetenidoNode->setAttribute('Impuesto', $impuesto->impuesto); // 01=ISR, 02=IVA, 03=IEPS
            $impRetenidoNode->setAttribute('montoRet', number_format($impuesto->monto_ret, 2, '.', ''));
            $impRetenidoNode->setAttribute('TipoPagoRet', $impuesto->tipo_pago_ret); // "Pago provisional" o "Pago definitivo"
            $impuestosRetenidosNode->appendChild($impRetenidoNode);
        }
        $totalesNode->appendChild($impuestosRetenidosNode);

        // Sello
        $xsltLocation = CfdiDefaultLocations::retencion20();
        $builder = new DOMBuilder();
        $cadenaOrigen = $builder->build($xml->saveXML(), $xsltLocation);

        $privateKey = PrivateKey::openFile($credentials->path_key_pem, $credentials->password_csd);
        $sello = base64_encode($privateKey->sign($cadenaOrigen, OPENSSL_ALGO_SHA256));

        $root->setAttribute('Sello', $sello);
        $root->setAttribute('NoCertificado', $credentials->no_certificado);
        $root->setAttribute('Certificado', $credentials->certificado_b64);

        return $xml->saveXML();
    }

    private function simularTimbre(string $xmlString, string $uuid, string $selloPac): string
    {
        $xml = new DOMDocument();
        $xml->loadXML($xmlString);
        $root = $xml->documentElement;

        $complemento = $xml->createElement('retenciones:Complemento');
        $tfd = $xml->createElement('tfd:TimbreFiscalDigital');
        $tfd->setAttribute('xmlns:tfd', 'http://www.sat.gob.mx/TimbreFiscalDigital');
        $tfd->setAttribute('xsi:schemaLocation', 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd');
        $tfd->setAttribute('Version', '1.1');
        $tfd->setAttribute('UUID', $uuid);
        $tfd->setAttribute('FechaTimbrado', date('Y-m-d\TH:i:s'));
        $tfd->setAttribute('RfcProvCertif', 'FDI000000000'); // PAC de prueba para Formas Digitales
        $tfd->setAttribute('SelloCFD', $root->getAttribute('Sello'));
        $tfd->setAttribute('NoCertificadoSAT', '00001000000500000000'); // Certificado de prueba del SAT
        $tfd->setAttribute('SelloSAT', $selloPac);

        $complemento->appendChild($tfd);
        $root->appendChild($complemento);

        return $xml->saveXML();
    }
}
