<?php

namespace App\Modules\Facturacion\Http\Controllers\Complemento_Pago;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Complemento\Pago\Pago;
use App\Modules\Facturacion\Models\Cfdi40\FormaPago;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use App\Modules\Facturacion\Services\SatCatalogService;
use App\Modules\Facturacion\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use CfdiUtils\Cfdi as CfdiReader;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;

class PagoController extends Controller
{
    /**
     * Muestra una lista de los complementos de pago.
     */
    public function index(): View
    {
        $pagos = Pago::with('cliente')->latest()->paginate(15);
        return view('facturacion::pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para crear un nuevo complemento de pago.
     */
    public function create(SatCatalogService $catalogService): View
    {
        $series = SerieFolio::where('tipo_comprobante', 'P')->where('is_active', true)->get();
        // Cargamos el catálogo de formas de pago para que esté disponible en el <select>
        $formasPago = $catalogService->getFormasPago()->pluck('texto', 'id');
 
        return view('facturacion::pagos.create', compact('series', 'formasPago'));
    }

    /**
     * Guarda un nuevo complemento de pago en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'serie_folio_id' => ['required', Rule::exists('facturacion_series_folios', 'id')->where('tipo_comprobante', 'P')],
            'fecha_pago' => 'required|date',
            'forma_pago' => ['required', 'exists:sat_cfdi_40_formas_pago,id'],
            'moneda' => 'required|string|size:3',
            'monto' => 'required|numeric|min:0.01',
            'doctosRelacionados' => 'required|array|min:1',
            'doctosRelacionados.*.id_documento' => 'required|string|uuid',
            'doctosRelacionados.*.serie' => 'nullable|string',
            'doctosRelacionados.*.folio' => 'nullable|string',
            'doctosRelacionados.*.moneda_dr' => 'required|string|size:3',
            'doctosRelacionados.*.num_parcialidad' => 'required|integer',
            'doctosRelacionados.*.imp_saldo_ant' => 'required|numeric',
            'doctosRelacionados.*.imp_pagado' => 'required|numeric|min:0.01',
        ]);

        try {
            $pago = DB::transaction(function () use ($validated) {
                $serieFolio = SerieFolio::findOrFail($validated['serie_folio_id']);
                $folio = $serieFolio->folio_actual + 1;

                $pago = Pago::create([
                    'cliente_id' => $validated['cliente_id'],
                    'serie_folio_id' => $validated['serie_folio_id'],
                    'serie' => $serieFolio->serie,
                    'folio' => $folio,
                    'fecha_pago' => $validated['fecha_pago'],
                    'forma_pago' => $validated['forma_pago'],
                    'moneda' => $validated['moneda'],
                    'monto' => $validated['monto'],
                    'status' => 'borrador',
                ]);

                foreach ($validated['doctosRelacionados'] as $doctoData) {
                    $impSaldoInsoluto = $doctoData['imp_saldo_ant'] - $doctoData['imp_pagado'];

                    $pago->documentos()->create([
                        'id_documento' => $doctoData['id_documento'],
                        'serie' => $doctoData['serie'],
                        'folio' => $doctoData['folio'],
                        'moneda_dr' => $doctoData['moneda_dr'],
                        'num_parcialidad' => $doctoData['num_parcialidad'],
                        'imp_saldo_ant' => $doctoData['imp_saldo_ant'],
                        'imp_pagado' => $doctoData['imp_pagado'],
                        'imp_saldo_insoluto' => $impSaldoInsoluto,
                    ]);

                    $facturaOriginal = Cfdi::where('uuid_fiscal', $doctoData['id_documento'])->first();
                    if ($facturaOriginal) {
                        $facturaOriginal->update(['saldo_pendiente' => $impSaldoInsoluto]);
                    }
                }

                $serieFolio->update(['folio_actual' => $folio]);
                return $pago;
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el complemento de pago: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.pagos.show', $pago)
            ->with('success', 'Borrador del complemento de pago creado exitosamente.');
    }

    /**
     * Muestra los detalles de un complemento de pago específico.
     */
    public function show(Pago $pago, SatCatalogService $catalogService): View
    {
        $pago->load('cliente', 'documentos');
        // Usamos el servicio de catálogos para obtener el texto de la forma de pago
        $formasPago = $catalogService->getFormasPago()->pluck('texto', 'id');
        return view('facturacion::pagos.show', compact('pago', 'formasPago'));
    }

    /**
     * Muestra el formulario para editar un complemento de pago.
     */
    public function edit(Pago $pago, SatCatalogService $catalogService): View|RedirectResponse
    {
        if ($pago->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.pagos.show', $pago)
                ->with('error', 'Solo se pueden editar complementos en estado de borrador.');
        }

        $series = SerieFolio::where('tipo_comprobante', 'P')->where('is_active', true)->get();
        $formasPago = $catalogService->getFormasPago()->pluck('texto', 'id');
        $pago->load('documentos', 'cliente');

        return view('facturacion::pagos.edit', compact('pago', 'series', 'formasPago'));
    }

    /**
     * Actualiza un complemento de pago en la base de datos.
     */
    public function update(Request $request, Pago $pago): RedirectResponse
    {
        if ($pago->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.pagos.show', $pago)
                ->with('error', 'Solo se pueden editar complementos en estado de borrador.');
        }

        $validated = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'serie_folio_id' => ['required', Rule::exists('facturacion_series_folios', 'id')->where('tipo_comprobante', 'P')],
            'fecha_pago' => 'required|date',
            'forma_pago' => ['required', 'exists:sat_cfdi_40_formas_pago,id'],
            'moneda' => 'required|string|size:3',
            'monto' => 'required|numeric|min:0.01',
            'doctosRelacionados' => 'nullable|array',
            'doctosRelacionados.*.id_documento' => 'required_with:doctosRelacionados|string|uuid',
            'doctosRelacionados.*.serie' => 'nullable|string',
            'doctosRelacionados.*.folio' => 'nullable|string',
            'doctosRelacionados.*.moneda_dr' => 'required_with:doctosRelacionados|string|size:3',
            'doctosRelacionados.*.num_parcialidad' => 'required_with:doctosRelacionados|integer',
            'doctosRelacionados.*.imp_saldo_ant' => 'required_with:doctosRelacionados|numeric',
            'doctosRelacionados.*.imp_pagado' => 'required_with:doctosRelacionados|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($pago, $validated, $request) {
                $pago->update($validated);

                if ($request->has('doctosRelacionados')) {
                    foreach ($validated['doctosRelacionados'] as $doctoData) {
                        if ($pago->documentos()->where('id_documento', $doctoData['id_documento'])->exists()) continue;

                        $impSaldoInsoluto = $doctoData['imp_saldo_ant'] - $doctoData['imp_pagado'];
                        $pago->documentos()->create(array_merge($doctoData, ['imp_saldo_insoluto' => $impSaldoInsoluto]));

                        $facturaOriginal = Cfdi::where('uuid_fiscal', $doctoData['id_documento'])->first();
                        if ($facturaOriginal) {
                            $facturaOriginal->update(['saldo_pendiente' => $impSaldoInsoluto]);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar el complemento de pago: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.pagos.show', $pago)
            ->with('success', 'Borrador del complemento de pago actualizado exitosamente.');
    }

    /**
     * Elimina un complemento de pago (solo si es borrador).
     */
    public function destroy(Pago $pago): RedirectResponse
    {
        if ($pago->status !== 'borrador') {
            return back()->with('error', 'Solo se pueden eliminar complementos en estado de borrador.');
        }

        try {
            DB::transaction(function () use ($pago) {
                foreach ($pago->documentos as $documento) {
                    $facturaOriginal = Cfdi::where('uuid_fiscal', $documento->id_documento)->first();
                    if ($facturaOriginal) {
                        // Restaura el saldo pendiente original
                        $facturaOriginal->update(['saldo_pendiente' => $documento->imp_saldo_ant]);
                    }
                }
                $pago->documentos()->delete();
                $pago->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el borrador: ' . $e->getMessage());
        }

        return redirect()->route('tenant.facturacion.pagos.index')
            ->with('success', 'Borrador de complemento de pago eliminado exitosamente.');
    }

    /**
     * Timbra un complemento de pago.
     */
    public function timbrar(Pago $pago, PagoService $pagoService): RedirectResponse
    {
        try {
            $resultado = $pagoService->timbrar($pago);

            if (!$resultado->success) {
                return back()->with('error', 'Error al timbrar: ' . $resultado->message);
            }

            return redirect()->route('tenant.facturacion.pagos.show', $pago)->with('success', $resultado->message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al timbrar: ' . $e->getMessage());
        }
    }

    /**
     * Cancela un complemento de pago timbrado.
     */
    public function cancelar(Request $request, Pago $pago, PagoService $pagoService): RedirectResponse
    {
        // La cancelación de retenciones/pagos no requiere motivo según la documentación de Formas Digitales.
        try {
            $resultado = $pagoService->cancelar($pago);

            if (!$resultado->success) {
                return back()->with('error', 'Error al cancelar: ' . $resultado->message);
            }

            return redirect()->route('tenant.facturacion.pagos.show', $pago)->with('success', $resultado->message);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al cancelar: ' . $e->getMessage());
        }
    }

    /**
     * Descarga el archivo XML de un pago timbrado.
     */
    public function downloadXml(Pago $pago)
    {
        if ($pago->status !== 'timbrado' || !$pago->path_xml) {
            abort(404, 'Archivo XML no encontrado.');
        }
        return Storage::download($pago->path_xml);
    }

    /**
     * Descarga una representación en PDF de un pago timbrado.
     */
    public function downloadPdf(Pago $pago)
    {
        if ($pago->status !== 'timbrado' || !$pago->path_xml) {
            abort(404, 'Solo se pueden descargar PDFs de complementos timbrados.');
        }

        $pago->load('cliente', 'documentos');

        // 1. Leer el XML timbrado desde el storage
        $xmlContent = Storage::get($pago->path_xml);
        $cfdiReader = CfdiReader::newFromString($xmlContent);

        // Se busca el nodo del Timbre Fiscal Digital. El método getTimbreFiscalDigital no existe en la clase Cfdi.
        // La forma correcta es buscar el nodo dentro del complemento.
        $tfd = $cfdiReader->getNode()->searchNode('cfdi:Complemento', 'tfd:TimbreFiscalDigital');

        if (!$tfd) {
            abort(500, 'El XML del complemento no contiene un Timbre Fiscal Digital válido.');
        }

        // 2. Generar la URL para el código QR usando el helper de cfdiutils
        $qrUrl = RequestParameters::createFromCfdi($cfdiReader)->expression();

        // 3. Generar el QR como imagen PNG y codificarla en base64 para embeberla en el HTML
        $qrCode = base64_encode(QrCode::format('png')->size(200)->generate($qrUrl));

        // 4. Cargar la vista del PDF y pasarle los datos necesarios
        $pdf = Pdf::loadView('facturacion::pagos.pdf', compact('pago', 'qrCode', 'tfd'));

        return $pdf->download("Pago-{$pago->serie}-{$pago->folio}.pdf");
    }

    /**
     * Busca facturas con saldo pendiente para un cliente específico (usado por AJAX).
     */
    public function searchInvoices(Request $request): JsonResponse
    {
        $request->validate(['cliente_id' => 'required|exists:clientes,id']);

        $facturas = Cfdi::where('cliente_id', $request->cliente_id)
            ->where('tipo_comprobante', 'I') // Ingreso
            ->where('status', 'timbrado')
            ->where('saldo_pendiente', '>', 0)
            ->get();

        return response()->json($facturas);
    }
}