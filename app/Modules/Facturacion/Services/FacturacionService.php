<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi as CfdiModel;
use Illuminate\Support\Facades\Storage;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Certificado\Certificado;

class FacturacionService
{
    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    /**
     * Procesa y timbra un CFDI.
     */
    public function timbrar(CfdiModel $cfdiBorrador): CfdiModel
    {
        // 1. Obtener datos fiscales del tenant.
        $datoFiscal = $this->credentialService->getDatosFiscales();
        $pac = $this->credentialService->getPacActivo();
        $cfdiBorrador->load('cliente', 'conceptos', 'relaciones');

        // 2. Cargar los certificados desde el storage.
        $cerContent = $this->credentialService->getCertificadoPemContent();
        $keyContent = $this->credentialService->getLlavePrivadaPemContent();
        $password = $datoFiscal->password_csd; // El modelo lo desencripta

        $certificado = new Certificado($cerContent);

        // 3. Crear el comprobante CFDI 4.0 usando el Creator
        $comprobanteAtributos = [
            'xmlns:cfdi' => 'http://www.sat.gob.mx/cfd/4',
            'xmlns:xsi' => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/cfd/4 http://www.sat.gob.mx/sitio_internet/cfd/4/cfd_v40.xsd',
            'Version' => '4.0',
            'Serie' => $cfdiBorrador->serie,
            'Folio' => $cfdiBorrador->folio,
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'FormaPago' => $cfdiBorrador->forma_pago,
            'MetodoPago' => $cfdiBorrador->metodo_pago,
            'Moneda' => $cfdiBorrador->moneda,
            'SubTotal' => number_format($cfdiBorrador->subtotal, 2, '.', ''),
            'Total' => number_format($cfdiBorrador->total, 2, '.', ''),
            'TipoDeComprobante' => $cfdiBorrador->tipo_comprobante,
            'Exportacion' => '01', // 01 - No aplica
            'LugarExpedicion' => $datoFiscal->cp_fiscal,
        ];

        $creator = new CfdiCreator40($comprobanteAtributos, $certificado);
        $comprobante = $creator->comprobante();

        // 3.1. Añadir Emisor y Receptor
        $comprobante->getEmisor()
            ->setRfc($datoFiscal->rfc) // Corregido: El RFC es requerido
            ->setNombre($datoFiscal->razon_social)
            ->setRegimenFiscal($datoFiscal->regimen_fiscal); // Usar la clave del régimen

        $comprobante->getReceptor()
            ->setRfc($cfdiBorrador->cliente->rfc)
            ->setNombre($cfdiBorrador->cliente->nombre_completo)
            ->setUsoCfdi($cfdiBorrador->uso_cfdi)
            ->setDomicilioFiscalReceptor($cfdiBorrador->cliente->direccion_fiscal)
            ->setRegimenFiscalReceptor($cfdiBorrador->cliente->regimen_fiscal_receptor); // CRÍTICO: Usar el del cliente

        // 3.2. Añadir Conceptos
        foreach ($cfdiBorrador->conceptos as $conceptoModel) {
            $conceptoModel->load('impuestos'); // Cargar la relación de impuestos

            $conceptoNode = $creator->addConcepto([
                'ClaveProdServ' => $conceptoModel->clave_prod_serv,
                'Cantidad' => number_format($conceptoModel->cantidad, 6, '.', ''),
                'ClaveUnidad' => $conceptoModel->clave_unidad,
                'Descripcion' => $conceptoModel->descripcion,
                'ValorUnitario' => number_format($conceptoModel->valor_unitario, 6, '.', ''),
                'Importe' => number_format($conceptoModel->valor_unitario * $conceptoModel->cantidad, 2, '.', ''),
                'ObjetoImp' => $conceptoModel->impuestos->isEmpty() ? '01' : '02', // 01: No, 02: Sí
            ]);

            // --- INICIO: Lógica de Impuestos Dinámica ---
            if (!$conceptoModel->impuestos->isEmpty()) {
                $impuestos = $conceptoNode->getImpuestos();
                foreach ($conceptoModel->impuestos as $impuestoModel) {
                    // Aquí se añadirían traslados y/o retenciones dinámicamente
                    $impuestos->addTraslado($impuestoModel->toArray());
                }
            }
            // --- FIN: Lógica de Impuestos Dinámica ---
        }

        // 3.3. Añadir Información Global (si aplica)
        if ($cfdiBorrador->es_factura_global) {
            $comprobante->addInformacionGlobal([
                'Periodicidad' => $cfdiBorrador->periodicidad,
                'Meses' => $cfdiBorrador->meses,
                'Año' => $cfdiBorrador->anio,
            ]);
        }

        // 3.4. Añadir CFDI Relacionados (si existen)
        if ($cfdiBorrador->relaciones->isNotEmpty()) {
            $uuidsRelacionados = $cfdiBorrador->relaciones->pluck('cfdi_relacionado_uuid')->all();
            // Asumimos un solo tipo de relación por CFDI para simplificar
            $tipoRelacion = $cfdiBorrador->relaciones->first()->tipo_relacion;

            $creator->addCfdiRelacionados([
                'TipoRelacion' => $tipoRelacion,
                'CfdiRelacionado' => $uuidsRelacionados,
            ]);
        }

        // 3.5. Calcular y añadir sumas de impuestos
        $creator->addSumasConceptos();

        // 4. Sellar el comprobante
        $creator->sign($keyContent, $password);

        // 5. VALIDACIÓN PREVIA (CRÍTICO)
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = [];
            foreach ($asserts->errors() as $error) {
                $errors[] = "{$error->getCode()}: {$error->getMessage()}";
            }
            throw new \Exception("Error de validación del CFDI: " . implode(' | ', $errors));
        }

        // 5. CONEXIÓN Y TIMBRADO CON PAC (LÓGICA REAL)
        $apiUrl = $datoFiscal->en_pruebas ? $pac->url_pruebas : $pac->url_produccion;
        $xmlParaEnviar = $creator->asXml();

        // try {
        //     $clienteHttp = new \GuzzleHttp\Client();
        //     $respuestaPac = $clienteHttp->post($apiUrl, [
        //         'auth' => [$pac->usuario, $pac->password],
        //         'body' => $xmlParaEnviar
        //     ]);
        //     $xmlTimbrado = $respuestaPac->getBody()->getContents();
        //     // Aquí se extraería el UUID del XML de respuesta.
        // } catch (\GuzzleHttp\Exception\ClientException $e) {
        //     $respuestaError = $e->getResponse()->getBody()->getContents();
        //     // Aquí se analizaría la respuesta para obtener el código de error del PAC
        //     throw new Exception("Error del PAC: " . $respuestaError);
        // }

        // --- SIMULACIÓN ---
        $uuidSimulado = \Illuminate\Support\Str::uuid();
        
        // Para una simulación más realista, agregamos el nodo de Timbre Fiscal Digital
        $creator->addSumasConceptos(null, 0); // Asegura que el nodo de impuestos exista si es necesario
        $tfd = new \CfdiUtils\Nodes\Node('tfd:TimbreFiscalDigital', [
            'xmlns:tfd' => 'http://www.sat.gob.mx/TimbreFiscalDigital',
            'xsi:schemaLocation' => 'http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd',
            'Version' => '1.1',
            'UUID' => $uuidSimulado,
            'FechaTimbrado' => now()->format('Y-m-d\TH:i:s'),
            'SelloCFD' => substr($comprobante['Sello'], 0, 8) . '...', // Simulación del sello
            'NoCertificadoSAT' => '00001000000504465028', // Certificado de prueba del SAT
        ]);
        $creator->comprobante()->addComplemento($tfd);
        $xmlTimbrado = $creator->asXml();

        // 6. Guardar el XML y actualizar el registro en la BD
        $pathXml = "tenants/{$cfdiBorrador->tenant_id}/facturas/{$cfdiBorrador->serie}-{$cfdiBorrador->folio}.xml";
        Storage::put($pathXml, $xmlTimbrado);

        $cfdiBorrador->update([
            'status' => 'timbrado',
            'uuid_fiscal' => $uuidSimulado,
            'path_xml' => $pathXml,
        ]);

        return $cfdiBorrador;
    }

    /**
     * Simula la cancelación de un CFDI.
     */
    public function cancelar(CfdiModel $cfdi, string $motivo): bool
    {
        // 1. Obtener datos fiscales y configuración del PAC.
        $datoFiscal = $this->credentialService->getDatosFiscales();
        $pac = $this->credentialService->getPacActivo();

        // 2. Cargar certificados (necesarios para firmar la solicitud de cancelación).
        $cerContent = $this->credentialService->getCertificadoPemContent();
        $keyContent = $this->credentialService->getLlavePrivadaPemContent();
        $password = $datoFiscal->password_csd;

        // 3. CONEXIÓN Y CANCELACIÓN CON PAC (LÓGICA REAL)
        $apiUrl = $datoFiscal->en_pruebas ? $pac->url_pruebas : $pac->url_produccion;
        // Aquí se construiría la petición de cancelación (generalmente un XML o JSON firmado)
        // y se enviaría al endpoint de cancelación del PAC.

        // --- SIMULACIÓN ---
        $esCancelacionExitosa = true;

        if ($esCancelacionExitosa) {
            $cfdi->update([
                'status' => 'cancelado',
                'motivo_cancelacion' => $motivo,
                'fecha_cancelacion' => now(),
            ]);
            return true;
        }

        return false;
    }
}
