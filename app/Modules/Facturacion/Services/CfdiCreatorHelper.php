<?php

namespace App\Modules\Facturacion\Services;

use CfdiUtils\CfdiCreator40;
use CfdiUtils\Elements\Cfdi40\Comprobante;
use Exception;

class CfdiCreatorHelper
{
    /**
     * Crea, sella y valida un CFDI 4.0 a partir de un array de datos.
     *
     * @param array $data Los datos del CFDI.
     * @param SatCredentialService $credentialService El servicio que provee las credenciales.
     * @return string El contenido del XML sellado.
     * @throws Exception
     */
    public static function crearXml(array $data, SatCredentialService $credentialService): string
    {
        $datosFiscales = $credentialService->getDatosFiscales();
        $credential = $credentialService->getCredential();
        $certificado = $credential->certificate();

        // 1. Crear el objeto CfdiCreator40
        $creator = new CfdiCreator40([
            'Serie' => $data['serie'],
            'Folio' => $data['folio'],
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'FormaPago' => $data['forma_pago'],
            'MetodoPago' => $data['metodo_pago'],
            'Moneda' => 'MXN', // Asumimos MXN, se puede hacer dinámico
            'TipoDeComprobante' => $data['tipo_comprobante'],
            'Exportacion' => '01', // No aplica
            'LugarExpedicion' => $datosFiscales->cp_fiscal,
        ], $certificado);

        $comprobante = $creator->comprobante();

        // 2. Agregar Emisor
        $comprobante->addEmisor([
            'Rfc' => $datosFiscales->rfc,
            'Nombre' => $datosFiscales->razon_social,
            'RegimenFiscal' => $datosFiscales->regimen_fiscal_clave,
        ]);

        // 3. Agregar Receptor
        $comprobante->addReceptor([
            'Rfc' => $data['receptor']['rfc'],
            'Nombre' => $data['receptor']['nombre'],
            'DomicilioFiscalReceptor' => $data['receptor']['domicilio_fiscal_receptor'],
            'RegimenFiscalReceptor' => $data['receptor']['regimen_fiscal_receptor'],
            'UsoCFDI' => $data['receptor']['uso_cfdi'],
        ]);

        // 4. Agregar Conceptos
        foreach ($data['conceptos'] as $conceptoData) {
            $comprobante->addConcepto([
                'ClaveProdServ' => $conceptoData['clave_prod_serv'],
                'Cantidad' => $conceptoData['cantidad'],
                'ClaveUnidad' => $conceptoData['clave_unidad'],
                'Descripcion' => $conceptoData['descripcion'],
                'ValorUnitario' => $conceptoData['valor_unitario'],
                'Importe' => $conceptoData['cantidad'] * $conceptoData['valor_unitario'],
                'ObjetoImp' => $conceptoData['objeto_imp'],
            ]);
            // Aquí se añadiría la lógica para los impuestos de cada concepto si aplica
        }

        // 5. Calcular sumas y totales
        $creator->addSumasConceptos(null, 2);

        // 6. Sellar el comprobante
        $creator->addSello(
            $credential->privateKey()->pem(),
            $credential->privateKey()->passPhrase()
        );

        // 7. Mover definiciones de namespaces al nodo raíz (práctica recomendada por el SAT)
        $creator->moveSatDefinitionsToComprobante();

        // 8. Validar el CFDI creado
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = [];
            foreach ($asserts->errors() as $error) {
                $errors[] = "{$error->getCode()}: {$error->getMessage()}";
            }
            throw new Exception("Error de validación al crear el CFDI: " . implode(' | ', $errors));
        }

        // 9. Devolver el XML como string
        return $creator->asXml();
    }
}