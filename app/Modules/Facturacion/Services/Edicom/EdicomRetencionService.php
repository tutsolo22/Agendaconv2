<?php

namespace App\Modules\Facturacion\Services\Edicom;

use App\Modules\Facturacion\Models\Retencion\Retencion;
use App\Modules\Facturacion\Services\Contracts\RetencionTimbradoServiceInterface;
use App\Modules\Facturacion\Services\SatCredentialService;
use CfdiUtils\CadenaOrigen\DOMBuilder;
use CfdiUtils\CadenaOrigen\CfdiDefaultLocations;
use CfdiUtils\Utils\PrivateKey;
use DOMDocument;
use Exception;
use SoapClient;
use SoapFault;

class EdicomRetencionService implements RetencionTimbradoServiceInterface
{
    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    public function timbrarRetencion(Retencion $retencion): object
    {
        $credentials = $this->credentialService->getCredentials();
        $xmlString = $this->buildXml($retencion, $credentials);

        try {
            $soapClient = new SoapClient($credentials->pac_url, ['trace' => 1]);
            
            $params = [
                'user' => $credentials->pac_user,
                'password' => $credentials->pac_password,
                'file' => base64_encode($xmlString),
            ];

            $response = $soapClient->getTimbreCfdiRetenciones($params);
            
            $timbreXml = base64_decode($response->getTimbreCfdiRetencionesReturn);
            
            $xmlTimbrado = $this->addTimbreToXml($xmlString, $timbreXml);

            $xmlDom = new DOMDocument();
            $xmlDom->loadXML($xmlTimbrado);
            $uuid = $xmlDom->getElementsByTagNameNS('http://www.sat.gob.mx/TimbreFiscalDigital', 'TimbreFiscalDigital')->item(0)->getAttribute('UUID');

            return (object)[
                'success' => true,
                'message' => 'Retención timbrada exitosamente.',
                'uuid' => $uuid,
                'xml' => $xmlTimbrado,
            ];

        } catch (SoapFault $e) {
            // Captura de errores específicos de SOAP
            return (object)[
                'success' => false,
                'message' => 'Error de Timbrado (SOAP): ' . $e->getMessage(),
                'xml' => $xmlString, // Devuelve el XML sin timbrar para depuración
            ];
        } catch (Exception $e) {
            // Captura de otros errores
            return (object)[
                'success' => false,
                'message' => 'Error General: ' . $e->getMessage(),
                'xml' => $xmlString,
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
        $receptorNode->setAttribute('NacionalidadR', 'Nacional');
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
        $totalesNode->setAttribute('montoTotGrav', '0.00');
        $totalesNode->setAttribute('montoTotExent', number_format($retencion->monto_total_operacion, 2, '.', ''));
        $totalesNode->setAttribute('montoTotRet', number_format($retencion->monto_total_retenido, 2, '.', ''));
        $root->appendChild($totalesNode);

        // Impuestos Retenidos
        $impuestosRetenidosNode = $xml->createElement('retenciones:ImpRetenidos');
        foreach ($retencion->impuestos as $impuesto) {
            $impRetenidoNode = $xml->createElement('retenciones:ImpRetenido');
            $impRetenidoNode->setAttribute('BaseRet', number_format($impuesto->base_ret, 2, '.', ''));
            $impRetenidoNode->setAttribute('Impuesto', $impuesto->impuesto);
            $impRetenidoNode->setAttribute('montoRet', number_format($impuesto->monto_ret, 2, '.', ''));
            $impRetenidoNode->setAttribute('TipoPagoRet', $impuesto->tipo_pago_ret);
            $impuestosRetenidosNode->appendChild($impRetenidoNode);
        }
        $totalesNode->appendChild($impuestosRetenidosNode);

        // Sello
        $xsltLocation = CfdiDefaultLocations::retenciones20();
        $builder = new DOMBuilder();
        $cadenaOrigen = $builder->build($xml->saveXML(), $xsltLocation);

        $privateKey = PrivateKey::openFile($credentials->path_key_pem, $credentials->password_csd);
        $sello = base64_encode($privateKey->sign($cadenaOrigen, OPENSSL_ALGO_SHA256));

        $root->setAttribute('Sello', $sello);
        $root->setAttribute('NoCertificado', $credentials->no_certificado);
        $root->setAttribute('Certificado', $credentials->certificado_b64);

        return $xml->saveXML();
    }

    private function addTimbreToXml(string $xmlString, string $timbreXml): string
    {
        $xml = new DOMDocument();
        $xml->loadXML($xmlString);
        $root = $xml->documentElement;

        $timbre = new DOMDocument();
        $timbre->loadXML($timbreXml);
        
        $tfdNode = $timbre->getElementsByTagNameNS('http://www.sat.gob.mx/TimbreFiscalDigital', 'TimbreFiscalDigital')->item(0);

        if ($tfdNode) {
            $complemento = $xml->createElement('retenciones:Complemento');
            $importedTfd = $xml->importNode($tfdNode, true);
            $complemento->appendChild($importedTfd);
            $root->appendChild($complemento);
        }

        return $xml->saveXML();
    }
}
