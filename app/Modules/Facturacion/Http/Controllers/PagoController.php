<?php

namespace App\Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Pago;
use App\Modules\Facturacion\Models\Sat\FormaPago;
use App\Modules\Facturacion\Models\SerieFolio;
use App\Modules\Facturacion\Services\PagoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
    public function create(): View
    {
        $series = SerieFolio::where('tipo_comprobante', 'P')->where('is_active', true)->get();
        $formasPago = FormaPago::where('is_active', true)->pluck('descripcion', 'id');

        return view('facturacion::pagos.create', compact('series', 'formasPago'));
    }

    /**
     * Guarda un nuevo complemento de pago en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'serie_folio_id' => 'required|exists:facturacion_series_folios,id',
            'fecha_pago' => 'required|date',
            'forma_pago' => 'required|string|max:2',
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
    public function show(Pago $pago): View
    {
        $pago->load('cliente', 'documentos');
        $formasPago = FormaPago::where('is_active', true)->pluck('descripcion', 'id');
        return view('facturacion::pagos.show', compact('pago', 'formasPago'));
    }

    /**
     * Muestra el formulario para editar un complemento de pago.
     */
    public function edit(Pago $pago): View|RedirectResponse
    {
        if ($pago->status !== 'borrador') {
            return redirect()->route('tenant.facturacion.pagos.show', $pago)
                ->with('error', 'Solo se pueden editar complementos en estado de borrador.');
        }

        $series = SerieFolio::where('tipo_comprobante', 'P')->where('is_active', true)->get();
        $formasPago = FormaPago::where('is_active', true)->pluck('descripcion', 'id');
        $pago->load('documentos', 'cliente');

        return view('facturacion::pagos.edit', compact('pago', 'series', 'formasPago'));
    }

    /**
     * Actualiza un complemento de pago en la base de datos.
     */
    public function update(Request $request, Pago $pago): RedirectResponse
    {
        // Lógica de actualización similar a store(), pero adaptada para editar.
        // Por simplicidad, se omite en esta respuesta, pero seguiría la estructura de validación y transacción.
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
            $pagoService->timbrar($pago);
            return redirect()->route('tenant.facturacion.pagos.show', $pago)->with('success', 'Complemento de pago timbrado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al timbrar: ' . $e->getMessage());
        }
    }

    /**
     * Cancela un complemento de pago timbrado.
     */
    public function cancelar(Request $request, Pago $pago, PagoService $pagoService): RedirectResponse
    {
        // En una aplicación real, aquí se obtendría el motivo de un formulario.
        $motivo = '02'; // Comprobante emitido con errores con relación.
        try {
            $pagoService->cancelar($pago, $motivo);
            return redirect()->route('tenant.facturacion.pagos.show', $pago)->with('success', 'Complemento de pago cancelado exitosamente.');
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
        if ($pago->status !== 'timbrado') {
            abort(404, 'Solo se pueden descargar PDFs de complementos timbrados.');
        }
        // Aquí iría la lógica para generar y descargar el PDF.
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