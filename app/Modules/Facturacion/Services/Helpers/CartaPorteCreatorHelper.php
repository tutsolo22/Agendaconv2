<?php

namespace App\Modules\Facturacion\Services\Helpers;

use CfdiUtils\Certificado\Certificado;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Elements\Cfdi40;
use CfdiUtils\Elements\CartaPorte31;
use App\Modules\Facturacion\Services\SatCredentialService;
use Exception;

class CartaPorteCreatorHelper
{
    public static function crearXml(array $data, SatCredentialService $credentialService): string
    {
        $datosFiscales = $credentialService->getDatosFiscales();
        $credential = $credentialService->getCredential();
        $certificado = $credential->certificate();

        // 1. Crear el CfdiCreator40
        $creator = new CfdiCreator40([
            'Serie' => $data['serie'] ?? 'CP', // Opcional, se puede definir una serie para Cartas Porte
            'Folio' => $data['folio'] ?? null, // El folio puede ser manejado por otro sistema
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'SubTotal' => '0',
            'Moneda' => 'XXX', // Moneda para operaciones sin valor monetario
            'Total' => '0',
            'TipoDeComprobante' => 'T', // T = Traslado
            'Exportacion' => '01', // No aplica
            'LugarExpedicion' => $datosFiscales->cp_fiscal,
        ], $certificado);

        $comprobante = $creator->comprobante();

        // 2. Emisor y Receptor (para Traslado, suelen ser el mismo)
        $comprobante->addEmisor([
            'Rfc' => $datosFiscales->rfc,
            'Nombre' => $datosFiscales->razon_social,
            'RegimenFiscal' => $datosFiscales->regimen_fiscal_clave,
        ]);

        $comprobante->addReceptor([
            'Rfc' => $datosFiscales->rfc, // El mismo RFC del emisor
            'Nombre' => $datosFiscales->razon_social, // El mismo nombre
            'DomicilioFiscalReceptor' => $datosFiscales->cp_fiscal,
            'RegimenFiscalReceptor' => $datosFiscales->regimen_fiscal_clave,
            'UsoCFDI' => 'S01', // Sin efectos fiscales
        ]);

        // 3. Conceptos (se requiere al menos uno)
        $comprobante->addConcepto([
            'ClaveProdServ' => '50171500', // Transporte de carga por carretera
            'Cantidad' => '1',
            'ClaveUnidad' => 'H87', // Pieza
            'Descripcion' => 'Servicio de traslado de mercancías',
            'ValorUnitario' => '0',
            'Importe' => '0',
            'ObjetoImp' => '01', // No objeto de impuesto
        ]);

        // 4. Construir y agregar el complemento Carta Porte
        $cartaPorte = self::buildCartaPorteComplement($data);
        $comprobante->addComplemento($cartaPorte);

        // 5. Sellar y validar
        $creator->addSello(
            $credential->privateKey()->pem(),
            $credential->privateKey()->passPhrase()
        );
        $creator->moveSatDefinitionsToComprobante();

        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = array_map(fn($err) => "{$err->getCode()}: {$err->getMessage()}", iterator_to_array($asserts->errors()));
            throw new Exception("Error de validación del CFDI: " . implode(' | ', $errors));
        }

        return $creator->asXml();
    }

    private static function buildCartaPorteComplement(array $data): CartaPorte31\CartaPorte
    {
        $cartaPorte = new CartaPorte31\CartaPorte([
            'Version' => '3.1',
            'TranspInternac' => $data['transp_internac'],
            'IdCCP' => 'CCC' . str_replace(' ', '', microtime()), // Generar un ID único para la Carta Porte
        ]);

        // Ubicaciones
        $ubicaciones = $cartaPorte->getUbicaciones();
        $origen = $data['origen'];
        $ubicaciones->addUbicacion([
            'TipoUbicacion' => 'Origen',
            'RFCRemitenteDestinatario' => $origen['rfc'],
            'NombreRemitenteDestinatario' => $origen['nombre'],
            'FechaHoraSalidaLlegada' => $origen['fecha_hora_salida'],
        ])->addDomicilio([
            'Calle' => $origen['calle'],
            'NumeroExterior' => $origen['numero_exterior'],
            'Colonia' => $origen['colonia'],
            'Localidad' => $origen['localidad'],
            'Municipio' => $origen['municipio'],
            'Estado' => $origen['estado'],
            'Pais' => 'MEX',
            'CodigoPostal' => $origen['codigo_postal'],
        ]);

        $destino = $data['destino'];
        $ubicaciones->addUbicacion([
            'TipoUbicacion' => 'Destino',
            'RFCRemitenteDestinatario' => $destino['rfc'],
            'NombreRemitenteDestinatario' => $destino['nombre'],
            'FechaHoraSalidaLlegada' => $destino['fecha_hora_llegada'],
            'DistanciaRecorrida' => $data['distancia_recorrida'] ?? null, // Este campo es importante
        ])->addDomicilio([
            'Calle' => $destino['calle'],
            'NumeroExterior' => $destino['numero_exterior'],
            'Colonia' => $destino['colonia'],
            'Localidad' => $destino['localidad'],
            'Municipio' => $destino['municipio'],
            'Estado' => $destino['estado'],
            'Pais' => 'MEX',
            'CodigoPostal' => $destino['codigo_postal'],
        ]);

        // Mercancías
        $mercancias = $cartaPorte->getMercancias([
            'PesoBrutoTotal' => array_sum(array_column($data['mercancias'], 'peso_kg')),
            'UnidadPeso' => 'KGM',
            'NumTotalMercancias' => count($data['mercancias']),
        ]);

        foreach ($data['mercancias'] as $mercanciaData) {
            $mercancias->addMercancia([
                'BienesTransp' => $mercanciaData['bienes_transp'],
                'Descripcion' => $mercanciaData['descripcion'],
                'Cantidad' => $mercanciaData['cantidad'],
                'ClaveUnidad' => $mercanciaData['clave_unidad'],
                'PesoEnKg' => $mercanciaData['peso_kg'],
            ]);
        }

        // Autotransporte
        $autotransporte = $mercancias->getAutotransporte();
        $autotransporteData = $data['autotransporte'];
        $autotransporte->addIdentificacionVehicular([
            'ConfigVehicular' => $autotransporteData['config_vehicular'],
            'PlacaVM' => $autotransporteData['placa_vm'],
            'AnioModeloVM' => $autotransporteData['anio_modelo_vm'],
        ]);
        $autotransporte->addSeguros([
            'AseguraRespCivil' => $autotransporteData['nombre_aseg'],
            'PolizaRespCivil' => $autotransporteData['num_poliza_seguro'],
        ]);

        // Figura Transporte
        $figuraTransporte = $cartaPorte->getFiguraTransporte();
        $figuraData = $data['figura_transporte'];
        $figuraTransporte->addTiposFigura([
            'TipoFigura' => $figuraData['tipo_figura'],
            'RFCFigura' => $figuraData['rfc_figura'],
            'NombreFigura' => $figuraData['nombre_figura'],
        ]);

        return $cartaPorte;
    }
}