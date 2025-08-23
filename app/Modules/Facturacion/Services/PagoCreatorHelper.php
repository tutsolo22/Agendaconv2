<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator40;
use Exception;

class PagoCreatorHelper
{
    /**
     * Crea, sella y valida el XML de un complemento de pago.
     *
     * @param Pago $pago
     * @param SatCredentialService $credentialService
     * @return string El contenido del XML sellado.
     * @throws Exception
     */
    public static function crearXml(Pago $pago, SatCredentialService $credentialService): string
    {
        $datoFiscal = $credentialService->getDatosFiscales();
        $pago->load('cliente', 'documentos');
        $cliente = $pago->cliente;

        $cerContent = $credentialService->getCertificadoPemContent();
        $certificado = new Certificado($cerContent);

        $comprobanteAtributos = [
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd',
            'Version' => '4.0',
            'Serie' => $pago->serie,
            'Folio' => $pago->folio,
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'SubTotal' => '0',
            'Moneda' => 'XXX',
            'Total' => '0',
            'TipoDeComprobante' => 'P',
            'Exportacion' => '01',
            'LugarExpedicion' => $datoFiscal->cp_fiscal,
        ];

        $creator = new CfdiCreator40($comprobanteAtributos, $certificado);
        $comprobante = $creator->comprobante();

        $comprobante->getEmisor()
            ->setRfc($datoFiscal->rfc)
            ->setNombre($datoFiscal->razon_social)
            ->setRegimenFiscal($datoFiscal->regimen_fiscal);

        $comprobante->getReceptor()
            ->setRfc($cliente->rfc)
            ->setNombre($cliente->nombre_completo)
            ->setDomicilioFiscalReceptor($cliente->direccion_fiscal)
            ->setRegimenFiscalReceptor($cliente->regimen_fiscal_receptor)
            ->setUsoCfdi('CP01');

        $comprobante->addConcepto([
            'ClaveProdServ' => '84111506',
            'Cantidad' => '1',
            'ClaveUnidad' => 'ACT',
            'Descripcion' => 'Pago',
            'ValorUnitario' => '0',
            'Importe' => '0',
            'ObjetoImp' => '01',
        ]);

        $pagos = $comprobante->addComplemento(new \CfdiUtils\Elements\Pagos20\Pagos());
        $pagos->getTotales()->setMontoTotalPagos(number_format($pago->monto, 2, '.', ''));

        $pagoNode = $pagos->addPago([
            'FechaPago' => \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d\TH:i:s'),
            'FormaDePagoP' => $pago->forma_pago,
            'MonedaP' => $pago->moneda,
            'Monto' => number_format($pago->monto, 2, '.', ''),
        ]);

        foreach ($pago->documentos as $docto) {
            $pagoNode->addDoctoRelacionado([
                'IdDocumento' => $docto->id_documento,
                'Serie' => $docto->serie,
                'Folio' => $docto->folio,
                'MonedaDR' => $docto->moneda_dr,
                'NumParcialidad' => $docto->num_parcialidad,
                'ImpSaldoAnt' => number_format($docto->imp_saldo_ant, 2, '.', ''),
                'ImpPagado' => number_format($docto->imp_pagado, 2, '.', ''),
                'ImpSaldoInsoluto' => number_format($docto->imp_saldo_insoluto, 2, '.', ''),
                'ObjetoImpDR' => '01',
            ]);
        }

        $keyContent = $credentialService->getLlavePrivadaPemContent();
        $creator->sign($keyContent, $datoFiscal->password_csd);

        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = array_map(fn($err) => "{$err->getCode()}: {$err->getMessage()}", iterator_to_array($asserts->errors()));
            throw new Exception("Error de validación del Complemento de Pago: " . implode(' | ', $errors));
        }

        return $creator->asXml();
    }
}