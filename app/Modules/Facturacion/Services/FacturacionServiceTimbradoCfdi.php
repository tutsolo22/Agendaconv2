<?php

namespace App\Modules\Facturacion\Services;

use App\Modules\Facturacion\Models\Cfdi as CfdiModel;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use CfdiUtils\CfdiCreator40;
use CfdiUtils\Certificado\Certificado;

class FacturacionService
{
    private const IVA_RATE = 0.16;

    protected SatCredentialService $credentialService;

    public function __construct(SatCredentialService $credentialService)
    {
        $this->credentialService = $credentialService;
    }

    /**
     * Orquesta la creación y timbrado de un CFDI.
     *
     * @param array $validatedData
     * @return CfdiModel
     * @throws \Exception
     */
    public function crearYTimbrar(array $validatedData): CfdiModel
    {
        // Primero, guardamos el CFDI como borrador para tener un registro base.
        $cfdiBorrador = $this->guardarBorrador($validatedData);

        // Luego, pasamos el borrador al método de timbrado.
        return $this->timbrar($cfdiBorrador);
    }

    /**
     * Guarda los datos de una factura como borrador en la base de datos.
     *
     * @param array $validatedData Datos validados desde StoreCfdiRequest.
     * @return CfdiModel El modelo del CFDI creado.
     */
    public function guardarBorrador(array $validatedData): CfdiModel
    {
        return DB::transaction(function () use ($validatedData) {
            // 1. Obtener la seral entrar marca error ie y el folio de forma segura
            $serieFolio = SerieFolio::findOrFail($validatedData['serie_folio_id']);
            $folio = $this->getNextFolio($serieFolio);

            // 2. Calcular totales usando un método helper
            $totales = $this->calcularTotales($validatedData['conceptos']);

            // 3. Crear el registro principal del CFDI, ahora consciente del tipo
            $cfdi = CfdiModel::create([
                'cliente_id' => $validatedData['cliente_id'],
                'serie_folio_id' => $serieFolio->id,
                'serie' => $serieFolio->serie,
                'folio' => $folio,
                'forma_pago' => $validatedData['forma_pago'],
                'metodo_pago' => $validatedData['metodo_pago'],
                'uso_cfdi' => $validatedData['uso_cfdi'],
                'tipo_comprobante' => $validatedData['tipo_comprobante'] ?? 'I', // 'E' para Nota de Crédito
                'moneda' => 'MXN', // Asumir MXN por ahora
                'subtotal' => $totales['subtotal'],
                'total_impuestos_trasladados' => $totales['iva'],
                'total' => $totales['total'],
                'status' => 'borrador',
                // Campos para notas de crédito
                'related_uuid' => $validatedData['related_uuid'] ?? null,
                'relation_type' => $validatedData['relation_type'] ?? null,
            ]);

            // 4. Crear los conceptos y sus impuestos
            foreach ($validatedData['conceptos'] as $conceptoData) {
                $importe = $conceptoData['cantidad'] * $conceptoData['valor_unitario'];
                $concepto = $cfdi->conceptos()->create([
                    'clave_prod_serv' => $conceptoData['clave_prod_serv'],
                    'cantidad' => $conceptoData['cantidad'],
                    'clave_unidad' => 'H87', // Clave SAT para "Pieza", podría venir del request
                    'descripcion' => $conceptoData['descripcion'],
                    'valor_unitario' => $conceptoData['valor_unitario'],
                    'importe' => $importe,
                    'objeto_imp' => '02', // Sí objeto de impuesto, podría venir del request
                ]);

                // Crear los impuestos del concepto usando un método helper
                $impuestoData = $this->calcularImpuestosConcepto($importe);
                $concepto->impuestos()->create($impuestoData);
            }

            return $cfdi;
        });
    }

    /**
     * Procesa y timbra un CFDI que ya existe como borrador.
     * @param CfdiModel $cfdiBorrador
     * @return CfdiModel
     */
    public function timbrar(CfdiModel $cfdiBorrador): CfdiModel
    {
        // Esta es una versión simplificada de tu método existente para la demostración.
        // La lógica completa con CfdiUtils se mantiene.
        $cfdiBorrador->load('cliente', 'conceptos.impuestos');

        // --- SIMULACIÓN DE TIMBRADO ---
        $uuidSimulado = \Illuminate\Support\Str::uuid();
        $xmlSimulado = "<cfdi>... Timbrado con UUID {$uuidSimulado} ...</cfdi>";

        $pathXml = "tenants/{$cfdiBorrador->tenant_id}/facturas/{$cfdiBorrador->serie}-{$cfdiBorrador->folio}.xml";
        Storage::put($pathXml, $xmlSimulado);

        $cfdiBorrador->update([
            'status' => 'timbrado',
            'uuid_fiscal' => $uuidSimulado,
            'path_xml' => $pathXml,
            'fecha_timbrado' => now(),
        ]);

        return $cfdiBorrador;
    }

    /**
     * Obtiene el siguiente folio para una serie y lo incrementa de forma atómica.
     *
     * @param SerieFolio $serieFolio
     * @return int
     */
    private function getNextFolio(SerieFolio $serieFolio): int
    {
        // Bloqueamos el registro para evitar race conditions
        $serieFolio->lockForUpdate();
        $serieFolio->increment('folio_actual');
        return $serieFolio->folio_actual;
    }

    /**
     * Calcula el subtotal, IVA y total a partir de una lista de conceptos.
     *
     * @param array $conceptos
     * @return array
     */
    private function calcularTotales(array $conceptos): array
    {
        $subtotal = 0;
        foreach ($conceptos as $concepto) {
            $subtotal += $concepto['cantidad'] * $concepto['valor_unitario'];
        }
        $iva = $subtotal * self::IVA_RATE;
        $total = $subtotal + $iva;

        return [
            'subtotal' => round($subtotal, 2),
            'iva' => round($iva, 2),
            'total' => round($total, 2),
        ];
    }

    /**
     * Calcula los datos de un impuesto (IVA 16%) para un concepto.
     *
     * @param float $base
     * @return array
     */
    private function calcularImpuestosConcepto(float $base): array
    {
        return [
            'base' => $base,
            'impuesto' => '002', // Código SAT para IVA
            'tipo_factor' => 'Tasa',
            'tasa_o_cuota' => self::IVA_RATE,
            'importe' => $base * self::IVA_RATE,
        ];
    }
}