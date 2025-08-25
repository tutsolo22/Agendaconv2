<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi;
use Barryvdh\DomPDF\Facade\Pdf;
use CfdiUtils\Cfdi as CfdiReader;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;

class PdfService
{
    public function generate(Cfdi $cfdi): string
    {
        if ($cfdi->status !== 'timbrado' || !$cfdi->xml) {
            throw new \Exception('El PDF solo está disponible para CFDI timbrados.');
        }

        // 1. Leer el XML para extraer los datos
        $cfdiReader = CfdiReader::newFromString($cfdi->xml);
        $comprobante = $cfdiReader->getComprobante();
        $emisor = $comprobante->getEmisor();
        $receptor = $comprobante->getReceptor();
        $tfd = $cfdiReader->getTimbreFiscalDigital();

        if (!$tfd) {
            throw new \Exception('El XML no contiene un Timbre Fiscal Digital válido.');
        }

        // 2. Generar la URL para el código QR
        $qrUrl = RequestParameters::createFromCfdi($cfdiReader)->expression();

        // 3. Preparar el array de datos para la vista
        $data = [
            'uuid' => $tfd['UUID'],
            'fecha' => $comprobante['Fecha'],
            'serie' => $comprobante['Serie'],
            'folio' => $comprobante['Folio'],
            'subtotal' => $comprobante['SubTotal'],
            'total' => $comprobante['Total'],
            'forma_pago' => $comprobante['FormaPago'],
            'metodo_pago' => $comprobante['MetodoPago'],
            'sello_cfdi' => $comprobante['Sello'],
            'sello_sat' => $tfd['SelloSAT'],
            'cadena_original_tfd' => '||' . $tfd->getTfdSourceString() . '||',
            'qr_url' => $qrUrl,
            'emisor' => [
                'rfc' => $emisor['Rfc'],
                'nombre' => $emisor['Nombre'],
                'regimen_fiscal' => $emisor['RegimenFiscal'],
            ],
            'receptor' => [
                'rfc' => $receptor['Rfc'],
                'nombre' => $receptor['Nombre'],
                'uso_cfdi' => $receptor['UsoCFDI'],
            ],
            'conceptos' => [],
        ];

        foreach ($comprobante->getConceptos() as $concepto) {
            $data['conceptos'][] = [
                'clave_prod_serv' => $concepto['ClaveProdServ'],
                'cantidad' => $concepto['Cantidad'],
                'clave_unidad' => $concepto['ClaveUnidad'],
                'descripcion' => $concepto['Descripcion'],
                'valor_unitario' => $concepto['ValorUnitario'],
                'importe' => $concepto['Importe'],
            ];
        }

        // 4. Cargar la vista y generar el PDF
        $pdf = Pdf::loadView('facturacion::cfdis.pdf.template', ['cfdi' => $cfdi, 'data' => $data]);

        return $pdf->output();
    }
}
