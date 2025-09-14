<?php

namespace App\Modules\Facturacion\Services\Helpers;

use App\Modules\Facturacion\Models\Complemento\Nomina\Empleado;
use App\Modules\Facturacion\Services\SatCredentialService;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Elements\Nomina12;
use Exception;

class NominaCreatorHelper
{
    public static function crearXml(array $data, SatCredentialService $credentialService): string
    {
        $datosFiscales = $credentialService->getDatosFiscales();
        $credential = $credentialService->getCredential();
        $certificado = $credential->certificate();
        $empleado = Empleado::find($data['empleado_id']);

        // Calcular totales
        $totalPercepciones = array_sum(array_column($data['percepciones'], 'importe_gravado')) + array_sum(array_column($data['percepciones'], 'importe_exento'));
        $totalDeducciones = array_sum(array_column($data['deducciones'], 'importe'));
        $totalOtrosPagos = array_sum(array_column($data['otrospagos'], 'importe'));

        $subtotal = $totalPercepciones + $totalOtrosPagos;
        $descuento = $totalDeducciones;
        $total = $subtotal - $descuento;

        $creator = new CfdiCreator40([
            'Version' => '4.0',
            'Serie' => $data['serie'] ?? 'N',
            'Folio' => $data['folio'] ?? null,
            'Fecha' => now()->format('Y-m-d\TH:i:s'),
            'FormaPago' => '99', // Por definir
            'SubTotal' => number_format($subtotal, 2, '.', ''),
            'Descuento' => number_format($descuento, 2, '.', ''),
            'Moneda' => 'MXN',
            'Total' => number_format($total, 2, '.', ''),
            'TipoDeComprobante' => 'N',
            'Exportacion' => '01',
            'LugarExpedicion' => $datosFiscales->cp_fiscal,
        ], $certificado);

        $comprobante = $creator->comprobante();

        $comprobante->addEmisor([
            'Rfc' => $datosFiscales->rfc,
            'Nombre' => $datosFiscales->razon_social,
            'RegimenFiscal' => $datosFiscales->regimen_fiscal_clave,
        ]);

        $comprobante->addReceptor([
            'Rfc' => $empleado->rfc,
            'Nombre' => "{$empleado->nombre} {$empleado->apellido_paterno} {$empleado->apellido_materno}",
            'DomicilioFiscalReceptor' => $empleado->codigo_postal,
            'RegimenFiscalReceptor' => '605', // Sueldos y Salarios e Ingresos Asimilados a Salarios
            'UsoCFDI' => 'CN01', // Nómina
        ]);

        $comprobante->addConcepto([
            'ClaveProdServ' => '84111505',
            'Cantidad' => '1',
            'ClaveUnidad' => 'ACT',
            'Descripcion' => 'Pago de nómina',
            'ValorUnitario' => number_format($subtotal, 2, '.', ''),
            'Importe' => number_format($subtotal, 2, '.', ''),
            'Descuento' => number_format($descuento, 2, '.', ''),
            'ObjetoImp' => '01',
        ]);

        // Complemento de Nómina
        $nomina = self::buildNominaComplement($data, $empleado, $totalPercepciones, $totalDeducciones, $totalOtrosPagos);
        $comprobante->addComplemento($nomina);

        // Sellar y validar
        $creator->addSello($credential->privateKey()->pem(), $credential->privateKey()->passPhrase());
        $creator->moveSatDefinitionsToComprobante();

        $asserts = $creator->validate();
        if ($asserts->hasErrors()) {
            $errors = array_map(fn($err) => "{$err->getCode()}: {$err->getMessage()}", iterator_to_array($asserts->errors()));
            throw new Exception("Error de validación del CFDI de Nómina: " . implode(' | ', $errors));
        }

        return $creator->asXml();
    }

    private static function buildNominaComplement(array $data, Empleado $empleado, float $totalPercepciones, float $totalDeducciones, float $totalOtrosPagos): Nomina12\Nomina
    {
        $nomina = new Nomina12\Nomina([
            'Version' => '1.2',
            'TipoNomina' => $data['tipo_nomina'],
            'FechaPago' => $data['fecha_pago'],
            'FechaInicialPago' => $data['fecha_inicial_pago'],
            'FechaFinalPago' => $data['fecha_final_pago'],
            'NumDiasPagados' => $data['num_dias_pagados'],
            'TotalPercepciones' => number_format($totalPercepciones, 2, '.', ''),
            'TotalDeducciones' => number_format($totalDeducciones, 2, '.', ''),
            'TotalOtrosPagos' => number_format($totalOtrosPagos, 2, '.', ''),
        ]);

        // Emisor del Complemento
        // $nomina->getEmisor([...]); // Atributos del emisor si aplican en el complemento

        // Receptor del Complemento
        $nomina->getReceptor([
            'Curp' => $empleado->curp,
            'NumSeguridadSocial' => $empleado->nss,
            'FechaInicioRelLaboral' => $empleado->fecha_inicio_rel_laboral,
            'TipoContrato' => $empleado->sat_tipo_contrato_id,
            'TipoRegimen' => $empleado->sat_tipo_regimen_id,
            'NumEmpleado' => $empleado->id,
            'TipoJornada' => $empleado->sat_tipo_jornada_id,
            'Puesto' => $empleado->puesto,
            'RiesgoPuesto' => $empleado->sat_riesgo_puesto_id,
            'PeriodicidadPago' => $data['periodicidad_pago'],
            'SalarioDiarioIntegrado' => number_format($empleado->salario_diario_integrado, 2, '.', ''),
        ]);

        // Percepciones
        $percepciones = $nomina->getPercepciones([
            'TotalSueldos' => '...', // Suma de percepciones de tipo sueldo
            'TotalGravado' => array_sum(array_column($data['percepciones'], 'importe_gravado')),
            'TotalExento' => array_sum(array_column($data['percepciones'], 'importe_exento')),
        ]);
        foreach ($data['percepciones'] as $p) {
            $percepciones->addPercepcion([
                'TipoPercepcion' => $p['clave'],
                'Clave' => $p['clave'],
                'Concepto' => $p['concepto'],
                'ImporteGravado' => number_format($p['importe_gravado'], 2, '.', ''),
                'ImporteExento' => number_format($p['importe_exento'], 2, '.', ''),
            ]);
        }

        // Deducciones
        $deducciones = $nomina->getDeducciones(['TotalOtrasDeducciones' => '...', 'TotalImpuestosRetenidos' => '...']);
        foreach ($data['deducciones'] as $d) {
            $deducciones->addDeduccion([
                'TipoDeduccion' => $d['clave'],
                'Clave' => $d['clave'],
                'Concepto' => $d['concepto'],
                'Importe' => number_format($d['importe'], 2, '.', ''),
            ]);
        }

        // Otros Pagos
        $otrosPagos = $nomina->getOtrosPagos();
        foreach ($data['otrospagos'] as $op) {
            $otrosPagos->addOtroPago([
                'TipoOtroPago' => $op['clave'],
                'Clave' => $op['clave'],
                'Concepto' => $op['concepto'],
                'Importe' => number_format($op['importe'], 2, '.', ''),
            ]);
        }

        return $nomina;
    }
}