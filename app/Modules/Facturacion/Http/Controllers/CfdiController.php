<?php

namespace App\Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Modules\Facturacion\Http\Requests\StoreCfdiRequest;
use App\Mail\FacturaEnviada;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Models\Pago;
use App\Modules\Facturacion\Models\PagoDocto;
use App\Modules\Facturacion\Models\CfdiRelacion;
use App\Modules\Facturacion\Models\DatoFiscal;
use App\Modules\Facturacion\Models\VentaPublico;
use App\Modules\Facturacion\Services\SatCatalogService;
use App\Modules\Facturacion\Services\FacturacionService;
use App\Modules\Facturacion\Models\SerieFolio;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Luecano\NumeroALetras\NumeroALetras;
use Illuminate\Http\JsonResponse;
use SimpleXMLElement;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class CfdiController extends Controller
{
    protected $facturacionService;
    protected $catalogService;

    public function __construct(FacturacionService $facturacionService, SatCatalogService $catalogService)
    {
        $this->facturacionService = $facturacionService;
        $this->catalogService = $catalogService;
    }

    public function index(): View
    {
        $facturas = Cfdi::with('cliente')->latest()->paginate(15); // TenantScope se aplica
        return view('facturacion::cfdis.index', compact('facturas'));
    }

    public function create(): View
    {
        // El TenantScope se aplica automáticamente
        $series = SerieFolio::where('is_active', true)->get();

        return view('facturacion::cfdis.create', [
            'series' => $series,
            'usosCfdi' => $this->catalogService->getUsosCfdi(),
            'formasPago' => $this->catalogService->getFormasPago(),
            'metodosPago' => $this->catalogService->getMetodosPago(),
        ]);
    }

    public function store(StoreCfdiRequest $request): RedirectResponse
    {
        // La validación ahora es manejada automáticamente por StoreCfdiRequest.
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request) {
            $serieFolio = SerieFolio::findOrFail($validated['serie_folio_id']);
            $folioActual = $serieFolio->folio_actual + 1;

            $cfdiData = [
                'cliente_id' => $validated['cliente_id'],
                'serie_folio_id' => $serieFolio->id,
                'serie' => $serieFolio->serie,
                'folio' => $folioActual,
                'tipo_comprobante' => $request->has('related_uuid') ? 'E' : 'I', // E = Egreso (Nota de Crédito)
                'forma_pago' => $validated['forma_pago'],
                'metodo_pago' => $validated['metodo_pago'],
                'uso_cfdi' => $validated['uso_cfdi'],
                'subtotal' => $request->input('subtotal'),
                'impuestos' => $request->input('impuestos'),
                'total' => $request->input('total'),
                'status' => 'borrador',
            ];

            $cfdi = Cfdi::create($cfdiData);

            foreach ($validated['conceptos'] as $conceptoData) {
                $cfdi->conceptos()->create($conceptoData);
            }

            // Si es una nota de crédito, guardar la relación
            if ($request->has('related_uuid')) {
                $cfdi->relaciones()->create([
                    'tipo_relacion' => $validated['relation_type'],
                    'cfdi_relacionado_uuid' => $validated['related_uuid'],
                ]);
            }

            $serieFolio->update(['folio_actual' => $folioActual]);
        });

        return redirect()->route('tenant.facturacion.cfdis.index')->with('success', 'Borrador de factura creado exitosamente.');
    }

    public function show(Cfdi $facturacion): View
    {
        $this->authorizeAccess($facturacion);

        // Cargar las relaciones donde ESTA factura es el origen (es una nota de crédito)
        $facturacion->load('relaciones');

        // Buscar las relaciones donde ESTA factura es el destino (tiene notas de crédito asociadas)
        $relacionadoPor = CfdiRelacion::with('cfdi')
            ->where('cfdi_relacionado_uuid', $facturacion->uuid_fiscal)
            ->get();

        // Si es una nota de crédito, buscar la factura original para mostrar un enlace
        $facturaOriginal = null;
        if ($facturacion->tipo_comprobante === 'E' && $facturacion->relaciones->isNotEmpty()) {
            // Cargar la relación explícitamente si no está ya cargada
            $facturacion->loadMissing('relaciones');
            $uuidOriginal = $facturacion->relaciones->first()->cfdi_relacionado_uuid;
            $facturaOriginal = Cfdi::where('uuid_fiscal', $uuidOriginal)->first();
        }

        return view('facturacion::cfdis.show', compact('facturacion', 'relacionadoPor', 'facturaOriginal'));
    }

    public function createCreditNote(Cfdi $facturacion): View|RedirectResponse
    {
        $this->authorizeAccess($facturacion);

        if ($facturacion->status !== 'timbrado') {
            return back()->with('error', 'Solo se pueden crear notas de crédito de facturas timbradas.');
        }

        $facturacion->load('cliente', 'conceptos');

        $series = SerieFolio::where('is_active', true)->get();

        return view('facturacion::cfdis.create', [
            'series' => $series,
            'usosCfdi' => $this->catalogService->getUsosCfdi(true),
            'formasPago' => $this->catalogService->getFormasPago(),
            'metodosPago' => $this->catalogService->getMetodosPago(true),
            'facturacion' => $facturacion, // Factura original para la relación
        ]);
    }

    public function createGlobal(): View
    {
        return view('facturacion::global.create', [
            'series' => SerieFolio::where('is_active', true)->get(),
            'periodicidades' => $this->catalogService->getPeriodicidades(),
            'meses' => $this->catalogService->getMeses(),
        ]);
    }

    /**
     * Busca clientes por término para AJAX.
     */
    public function searchClients(Request $request): JsonResponse
    {
        $term = $request->get('term');
        if (strlen($term) < 2) {
            return response()->json([]);
        }

        // El TenantScope se aplica automáticamente al modelo Cliente
        $clientes = Cliente::where(function ($query) use ($term) {
                $query->where('nombre_completo', 'LIKE', "%{$term}%")
                      ->orWhere('rfc', 'LIKE', "%{$term}%");
            })
            ->limit(10)
            ->get(['id', 'nombre_completo', 'rfc']);

        return response()->json($clientes);
    }

    public function searchVentas(Request $request)
    {
        $request->validate(['mes' => 'required|string|size:2', 'anio' => 'required|integer']);

        $ventas = VentaPublico::whereYear('fecha', $request->anio)
            ->whereMonth('fecha', $request->mes)
            ->whereNull('cfdi_global_id')
            ->get();

        return response()->json($ventas);
    }

    public function storeGlobal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'serie_folio_id' => 'required|exists:facturacion_series_folios,id',
            'periodicidad' => 'required|string|size:2',
            'meses' => 'required|string|size:2',
            'anio' => 'required|integer',
            'ventas_ids' => 'required|array|min:1',
            'ventas_ids.*' => 'exists:facturacion_ventas_publico,id',
        ]);

        try {
            $this->facturacionService->crearFacturaGlobal($validated);
            return redirect()->route('tenant.facturacion.cfdis.index')
                ->with('success', 'Borrador de Factura Global creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear la factura global: ' . $e->getMessage());
        }
    }

    public function timbrar(Cfdi $facturacion): RedirectResponse
    {
        try {
            $this->facturacionService->timbrar($facturacion);
            return back()->with('success', "Factura {$facturacion->serie}-{$facturacion->folio} timbrada exitosamente.");
        } catch (\Exception $e) {
            // En un caso real, aquí se registraría el error detallado.
            return back()->with('error', 'Error al timbrar la factura: ' . $e->getMessage());
        }
    }

    public function downloadXml(Cfdi $facturacion): StreamedResponse
    {
        $this->authorizeAccess($facturacion);

        if (!$facturacion->path_xml || !Storage::exists($facturacion->path_xml)) {
            abort(404, 'Archivo XML no encontrado.');
        }

        return Storage::download($facturacion->path_xml);
    }

    public function downloadPdf(Cfdi $facturacion)
    {
        $this->authorizeAccess($facturacion);

        if (!in_array($facturacion->status, ['timbrado', 'cancelado']) || !$facturacion->path_xml || !Storage::exists($facturacion->path_xml)) {
            abort(404, 'La factura no está timbrada o el archivo XML no existe.');
        }

        try {
            // El servicio se encarga de generar, guardar y devolver el PDF para la descarga.
            $pdf = $this->facturacionService->generarPdf($facturacion);
            $filename = "{$facturacion->serie}-{$facturacion->folio}.pdf";
            return $pdf->download($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo generar el PDF: ' . $e->getMessage());
        }
    }

    public function enviarPorCorreo(Cfdi $facturacion): RedirectResponse
    {
        $this->authorizeAccess($facturacion);

        if ($facturacion->status !== 'timbrado') {
            return back()->with('error', 'Solo se pueden enviar por correo facturas timbradas.');
        }

        if (empty($facturacion->cliente->email)) {
            return back()->with('error', 'El cliente no tiene una dirección de correo electrónico registrada.');
        }

        // Asegurarse de que el PDF exista antes de enviarlo
        if (!$this->facturacionService->pdfExiste($facturacion)) {
            $this->facturacionService->generarPdf($facturacion, true); // Generar y guardar
        }

        try {
            Mail::to($facturacion->cliente->email)->send(new FacturaEnviada($facturacion));
            return back()->with('success', "Factura enviada exitosamente a {$facturacion->cliente->email}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Hubo un problema al enviar el correo: ' . $e->getMessage());
        }
    }

    public function cancelar(Request $request, Cfdi $facturacion): RedirectResponse
    {
        $this->authorizeAccess($facturacion);

        if ($facturacion->status !== 'timbrado') {
            return back()->with('error', 'Solo se pueden cancelar facturas timbradas.');
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:255',
        ]);

        try {
            $this->facturacionService->cancelar($facturacion, $request->input('motivo_cancelacion'));
            return back()->with('success', "Factura {$facturacion->serie}-{$facturacion->folio} cancelada exitosamente.");
        } catch (\Exception $e) {
            // En un caso real, aquí se registraría el error detallado.
            return back()->with('error', 'Error al cancelar la factura: ' . $e->getMessage());
        }
    }

    private function authorizeAccess(Cfdi $factura)
    {
        if ($factura->tenant_id !== auth()->user()->tenant_id) {
            abort(403);
        }
    }
}