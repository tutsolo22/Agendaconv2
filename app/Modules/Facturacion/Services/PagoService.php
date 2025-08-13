<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Configuracion\DatoFiscal;
use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator40;
use Exception;
use Illuminate\Support\Facades\Storage;

class PagoService
{
    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    public function timbrar(Pago $pago): void
    {
        if ($pago->status !== 'borrador') {
            throw new Exception("Solo se pueden timbrar complementos en estado de borrador.");
        }

        $creator = $this->crearComprobantePago($pago);

        // Simulación de la comunicación con el PAC
        // $pac = $this->credentialService->getPacActivo();
        // $pacClient = new PacClient($pac->user, $pac->password, $pac->production_url);
        // $respuestaPac = $pacClient->timbrar($xmlSelladoString);

        // if (!$respuestaPac->esExitosa()) {
        //     throw new Exception("Error del PAC: " . $respuestaPac->getMensaje());
        // }

        // Simulación de una respuesta exitosa del PAC
        $uuid = (string) \Illuminate\Support\Str::uuid();

        // Para una simulación más realista, agregamos el nodo de Timbre Fiscal Digital
        $tfd = new \CfdiUtils\Nodes\Node('tfd:TimbreFiscalDigital', [
            'xmlns:tfd' => 'http://www.sat.gob.mx/TimbreFiscalDigital',
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd',
            'Version' => '1.1',
            'UUID' => $uuid,
            'FechaTimbrado' => now()->format('Y-m-d\TH:i:s'),
            'SelloCFD' => substr($creator->comprobante()['Sello'], 0, 8) . '...', // Simulación del sello
            'NoCertificadoSAT' => '00001000000504465028', // Certificado de prueba del SAT
        ]);
        // El complemento de pago ya existe, solo le agregamos el TFD
        $creator->comprobante()->getComplemento()->add($tfd);

        $xmlTimbrado = $creator->asXml();

        $pago->update([
            'status' => 'timbrado',
            'uuid_fiscal' => $uuid,
            'path_xml' => $this->guardarArchivo($pago, $xmlTimbrado, 'xml'),
        ]);
    }

    private function crearComprobantePago(Pago $pago): CfdiCreator40
    {
        $datoFiscal = $this->credentialService->getDatosFiscales();
        $pago->load('cliente', 'documentos');
        $cliente = $pago->cliente;

        $cerContent = $this->credentialService->getCertificadoPemContent();
        $certificado = new Certificado($cerContent);

        $comprobanteAtributos = [
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfdv40.xsd http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd',
            'Version' => '4.0',
            'Serie' => $pago->serie,
            'Folio' => $pago->folio,
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'SubTotal' => '0',
            'Moneda' => 'XXX', // Valor requerido para tipo 'P'
            'Total' => '0', // Valor requerido para tipo 'P'
            'TipoDeComprobante' => 'P',
            'Exportacion' => '01',
            'LugarExpedicion' => $datoFiscal->cp_fiscal,
        ];

        $creator = new CfdiCreator40($comprobanteAtributos, $certificado);
        $comprobante = $creator->comprobante();

        // Emisor
        $comprobante->getEmisor()
            ->setRfc($datoFiscal->rfc)
            ->setNombre($datoFiscal->razon_social)
            ->setRegimenFiscal($datoFiscal->regimen_fiscal);

        // Receptor
        $comprobante->getReceptor()
            ->setRfc($cliente->rfc)
            ->setNombre($cliente->nombre_completo)
            ->setDomicilioFiscalReceptor($cliente->direccion_fiscal)
            ->setRegimenFiscalReceptor($cliente->regimen_fiscal_receptor)
            ->setUsoCfdi('CP01'); // Valor requerido para tipo 'P'

        // Conceptos (un solo concepto requerido para pagos)
        $comprobante->addConcepto([
            'ClaveProdServ' => '84111506', // Facturación
            'Cantidad' => '1',
            'ClaveUnidad' => 'ACT', // Actividad
            'Descripcion' => 'Pago',
            'ValorUnitario' => '0',
            'Importe' => '0',
            'ObjetoImp' => '01', // No es objeto de impuesto
        ]);

        // Complemento de Pago
        $pagos = $comprobante->addComplemento(new \CfdiUtils\Elements\Pagos20\Pagos());

        // Totales
        $pagos->getTotales()
            // Aquí iría la lógica para sumar impuestos si los hubiera
            ->setMontoTotalPagos(number_format($pago->monto, 2, '.', ''));

        // Nodo de Pago
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
                'ObjetoImpDR' => '01', // Asumiendo que no hay impuestos en los documentos relacionados
            ]);
        }

        // Sellar el comprobante
        $keyContent = $this->credentialService->getLlavePrivadaPemContent();
        $password = $datoFiscal->password_csd;
        $creator->sign($keyContent, $password);

        // Validar
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = [];
            foreach ($asserts->errors() as $error) {
                $errors[] = "{$error->getCode()}: {$error->getMessage()}";
            }
            throw new \Exception("Error de validación del Complemento de Pago: " . implode(' | ', $errors));
        }

        return $creator;
    }

    public function cancelar(Pago $pago, string $motivo): void
    {
        if ($pago->status !== 'timbrado') {
            throw new Exception("Solo se pueden cancelar complementos timbrados.");
        }

        // Simulación de la comunicación con el PAC para cancelación
        // $pac = Pac::where('is_active', true)->firstOrFail();
        // $pacClient = new PacClient($pac->user, $pac->password, $pac->production_url);
        // $respuestaCancelacion = $pacClient->cancelar($pago->uuid_fiscal, $motivo);

        // if (!$respuestaCancelacion->esExitosa()) {
        //     throw new Exception("Error del PAC al cancelar: " . $respuestaCancelacion->getMensaje());
        // }

        $pago->update([
            'status' => 'cancelado',
            'cancelacion_motivo' => $motivo,
            'cancelacion_fecha' => now(),
        ]);
    }

    private function guardarArchivo(Pago $pago, string $content, string $extension): string
    {
        $filename = "{$pago->serie}-{$pago->folio}.{$extension}";
        $path = "tenants/{$pago->tenant_id}/pagos/{$filename}";
        Storage::put($path, $content);
        return $path;
    }
}