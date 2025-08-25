<?php
namespace App\Modules\Facturacion\Http\Controllers\Cfdi_40;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Cliente;
use App\Modules\Facturacion\Services\CancelacionService;
use App\Modules\Facturacion\Services\FacturacionService;
use App\Modules\Facturacion\Services\SatCatalogService;
use Barryvdh\DomPDF\Facade\Pdf;
use CfdiUtils\Cfdi\CfdiReader;
use CfdiUtils\ConsultaCfdiSat\RequestParameters;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Exception;

class CfdiController extends Controller
{
    /**
     * Muestra una lista de los CFDI generados.
     */
    public function index(): View
    {
        $facturas = Cfdi::with('cliente')
            ->latest()
            ->paginate(15);

        return view('facturacion::cfdis.index', ['facturas' => $facturas]);
    }

    /**
     * Muestra el formulario para crear un nuevo CFDI (Factura).
     */
    public function create(Request $request): View
    {
        // El parámetro 'factura_original' se usa para notas de crédito
        $facturaOriginalId = $request->query('factura_original');
        $facturaOriginal = null;
        if ($facturaOriginalId) {
            $facturaOriginal = Cfdi::with('cliente', 'conceptos')->findOrFail($facturaOriginalId);
        }

        return view('facturacion::cfdis.create', [
            'facturaOriginal' => $facturaOriginal,
        ]);
    }

    /**
     * Guarda y timbra un nuevo CFDI.
     */
    public function store(Request $request, FacturacionService $facturacionService)
    {
        // Aquí normalmente usarías un FormRequest para una validación más robusta.
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'serie_folio_id' => 'required|exists:facturacion_series_folios,id',
            'forma_pago' => 'required|string|max:2',
            'metodo_pago' => 'required|string|max:3',
            'uso_cfdi' => 'required|string|max:4',
            'tipo_relacion' => 'nullable|string|max:2',
            'factura_relacionada_uuid' => 'nullable|uuid',
            'conceptos' => 'required|array|min:1',
            'conceptos.*.clave_prod_serv' => 'required|string',
            'conceptos.*.cantidad' => 'required|numeric|min:0.000001',
            'conceptos.*.clave_unidad' => 'required|string',
            'conceptos.*.descripcion' => 'required|string',
            'conceptos.*.valor_unitario' => 'required|numeric|min:0',
            'conceptos.*.objeto_imp' => 'required|string|max:2',
        ]);

        try {
            // Determinar el tipo de comprobante (Ingreso o Egreso)
            $validatedData['tipo_comprobante'] = $request->has('factura_relacionada_uuid') ? 'E' : 'I';

            $cfdi = $facturacionService->crearYTimbrar($validatedData);

            return redirect()->route('tenant.facturacion.cfdis.show', $cfdi->id)
                ->with('success', 'CFDI creado y timbrado exitosamente.');

        } catch (Exception $e) {
            Log::error("Error al crear CFDI: " . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear el CFDI: ' . $e->getMessage());
        }
    }

    /**
     * Muestra los detalles de un CFDI.
     */
    public function show(Cfdi $cfdi): View
    {
        $cfdi->load('cliente', 'conceptos', 'relaciones.cfdiRelacionado');
        return view('facturacion::cfdis.show', ['facturacion' => $cfdi]);
    }

    /**
     * Muestra el formulario para editar un CFDI (si la lógica de negocio lo permite).
     * Nota: Generalmente los CFDI timbrados no se editan, se cancelan y sustituyen.
     */
    public function edit(Cfdi $cfdi)
    {
        if ($cfdi->status === 'timbrado') {
            return redirect()->route('tenant.facturacion.cfdis.show', $cfdi->id)
                ->with('info', 'Un CFDI timbrado no puede ser editado. Debe cancelarlo y crear uno nuevo si es necesario.');
        }
        // Lógica para mostrar el formulario de edición para borradores.
        return view('facturacion::cfdis.edit', compact('cfdi'));
    }

    /**
     * Actualiza un CFDI (ej. un borrador).
     */
    public function update(Request $request, Cfdi $cfdi)
    {
        // Lógica para actualizar...
        return redirect()->route('tenant.facturacion.cfdis.show', $cfdi->id)
            ->with('success', 'CFDI actualizado.');
    }

    /**
     * Elimina un CFDI (si es un borrador).
     * Los CFDI timbrados se cancelan, no se eliminan.
     */
    public function destroy(Cfdi $cfdi)
    {
        if ($cfdi->status === 'timbrado') {
            return back()->with('error', 'No se puede eliminar un CFDI timbrado. Use la opción de cancelar.');
        }
        $cfdi->delete();
        return redirect()->route('tenant.facturacion.cfdis.index')
            ->with('success', 'Borrador de CFDI eliminado.');
    }
        
    // --- MÉTODOS EXISTENTES FUNCIONALES ---

    public function downloadPdf(Cfdi $cfdi)
    {
        if ($cfdi->status !== 'timbrado' || !$cfdi->xml) {
            abort(404, 'El PDF solo está disponible para CFDI timbrados.');
        }
        $pdfService = app(PdfService::class);
        $pdfContent = $pdfService->generate($cfdi);
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"Factura-{$cfdi->serie}-{$cfdi->folio}.pdf\"",
        ]);
    }

    public function downloadXml(Cfdi $cfdi): Response
    {
        if ($cfdi->status !== 'timbrado' || !$cfdi->xml) {
            abort(404, 'El XML solo está disponible para CFDI timbrados.');
        }
        $filename = "{$cfdi->serie}-{$cfdi->folio}.xml";
        return response($cfdi->xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function cancelar(Request $request, Cfdi $cfdi, CancelacionService $cancelacionService)
    {
        $validated = $request->validate([
            'motivo' => 'required|in:01,02,03,04',
            'folio_sustitucion' => 'required_if:motivo,01|nullable|uuid',
        ]);

        try {
            $resultado = $cancelacionService->cancelarCfdi($cfdi, $validated['motivo'], $validated['folio_sustitucion']);

            if (!$resultado->success) {
                return back()->with('error', $resultado->message);
            }

            return redirect()->route('tenant.facturacion.cfdis.show', $cfdi->id)->with('success', 'CFDI cancelado exitosamente.');
        } catch (Exception $e) {
            return back()->with('error', 'Error al cancelar el CFDI: ' . $e->getMessage());
        }
    }

    // --- MÉTODOS PERSONALIZADOS (PLACEHOLDERS) ---

    public function createCreditNote(Cfdi $factura)
    {
        // Lógica para crear una nota de crédito basada en una factura existente
        return view('facturacion::cfdis.create', ['facturaOriginal' => $factura]);
    }

    public function createGlobal()
    {
        // Lógica para la vista de factura global
        return view('facturacion::cfdis.create-global');
    }

    public function searchVentas(Request $request)
    {
        // Lógica para buscar ventas para la factura global
        return response()->json([]);
    }

    public function storeGlobal(Request $request)
    {
        // Lógica para guardar la factura global
        return redirect()->route('tenant.facturacion.cfdis.index')->with('success', 'Factura global creada.');
    }
}
