<?php

namespace App\Modules\Facturacion\Http\Controllers\Cfdi_40;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use Illuminate\Http\Request;
use App\Modules\Facturacion\Models\Cfdi;
use App\Modules\Facturacion\Http\Requests\StoreCfdiRequest;
use App\Modules\Facturacion\Services\FacturacionService;
use App\Modules\Services\ModuleLoggerService;

class CfdiController extends Controller
{
    /**
     * Muestra una lista de los CFDI (Facturas y Notas de Crédito).
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Cfdi::with('cliente');

        // Aplicar filtros de búsqueda si existen
        if ($request->filled('search_serie')) {
            $query->where('serie', 'like', '%' . $request->search_serie . '%');
        }

        if ($request->filled('search_folio')) {
            $query->where('folio', 'like', '%' . $request->search_folio . '%');
        }

        // Ordenar y paginar los resultados
        $facturas = $query->latest()->paginate(15);

        // Se pasa la variable 'facturas' a la vista.
        return view('facturacion::cfdis.index', compact('facturas'));
    }

    /**
     * Muestra el formulario para crear un nuevo CFDI.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Simplemente retornamos la vista. El JavaScript se encargará
        // de llenarla con los datos de la API.
        return view('facturacion::cfdis.create');
    }

    /**
     * Muestra el formulario para crear una nota de crédito basada en una factura existente.
     *
     * @param Cfdi $factura La factura original a la que se le aplicará la nota de crédito.
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function createCreditNote(Cfdi $factura)
    {
        // Validamos que solo se puedan crear notas de crédito para facturas timbradas.
        if ($factura->status !== 'timbrado') {
            return redirect()->route('tenant.facturacion.cfdis.index')
                ->with('error', 'Solo se pueden crear notas de crédito para facturas timbradas.');
        }

        // Pasamos la factura original a la misma vista de creación.
        // La vista y el JS se encargarán del resto.
        return view('facturacion::cfdis.create', ['facturaOriginal' => $factura]);
    }

    /**
     * Muestra el formulario para crear una nueva Factura Global.
     * (Placeholder)
     *
     * @return \Illuminate\View\View
     */
    public function createGlobal()
    {
        // Los catálogos de periodicidad y meses se cargarán vía API para mantener la consistencia.
        // Solo se pasan las series, que son específicas de este tenant y tipo de comprobante.
        $series = SerieFolio::where('is_active', true)
                            ->where('tipo_comprobante', 'global')
                            ->get(['id', 'serie', 'folio_actual']);

        return view('facturacion::global.create', compact('series'));
    }

    /**
     * Busca ventas no facturadas para un periodo específico (AJAX).
     * (Placeholder)
     */
    public function searchVentas(Request $request)
    {
        // TODO: Implementar la lógica real para buscar en el modelo de Ventas
        // $ventas = Venta::where('facturado', false)
        //                ->whereMonth('fecha', $request->input('mes'))
        //                ->whereYear('fecha', $request->input('anio'))
        //                ->get(['id', 'folio_venta', 'fecha', 'total']);

        // Datos de ejemplo para la demostración
        $ventas = [
            ['id' => 1, 'folio_venta' => 'V-001', 'fecha' => '2024-05-10', 'total' => 150.75],
            ['id' => 2, 'folio_venta' => 'V-002', 'fecha' => '2024-05-12', 'total' => 89.00],
            ['id' => 3, 'folio_venta' => 'V-003', 'fecha' => '2024-05-15', 'total' => 230.50],
        ];

        return response()->json($ventas);
    }

    /**
     * Almacena una nueva Factura Global.
     * (Placeholder)
     */
    public function storeGlobal(Request $request)
    {
        // TODO: Implementar la validación y la lógica para crear el CFDI Global
        // 1. Validar request (periodicidad, mes, anio, ventas_ids, serie_folio_id)
        // 2. Obtener los modelos de las ventas seleccionadas a partir de $request->ventas_ids
        // 3. Llamar a un servicio que genere el CFDI Global con los datos.
        // 4. Marcar las ventas como facturadas.

        return redirect()->route('tenant.facturacion.cfdis.index')->with('success', 'Borrador de Factura Global creado exitosamente.');
    }

        /**
     * Almacena un nuevo CFDI, ya sea como borrador o timbrado.
     *
     * @param StoreCfdiRequest $request
     * @param FacturacionService $facturacionService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreCfdiRequest $request, FacturacionService $facturacionService, ModuleLoggerService $logger)
    {
        $validatedData = $request->validated();
        $action = $request->input('action');

        // Determinar el tipo de documento para los mensajes de éxito y error.
        $esNotaDeCredito = ($validatedData['tipo_comprobante'] ?? 'I') === 'E';
        $tipoDocumento = $esNotaDeCredito ? 'Nota de Crédito' : 'Factura';
        $tipoDocumentoBorrador = $esNotaDeCredito ? 'Borrador de nota de crédito' : 'Borrador de factura';

        try {
            if ($action === 'guardar') {
                $cfdi = $facturacionService->guardarBorrador($validatedData);
                return redirect()->route('tenant.facturacion.cfdis.index')
                                 ->with('success', "{$tipoDocumentoBorrador} #{$cfdi->folio} guardado correctamente.");

            } elseif ($action === 'timbrar') {
                $cfdi = $facturacionService->crearYTimbrar($validatedData);
                return redirect()->route('tenant.facturacion.cfdis.show', $cfdi->id)
                                 ->with('success', "{$tipoDocumento} #{$cfdi->folio} timbrada correctamente. UUID: {$cfdi->uuid_fiscal}");
            }
        } catch (\Exception $e) {
            // --- INICIO: Uso del Logger Modular ---
            // Ahora el error se registrará en storage/logs/facturacion/facturacion.log
            $logger->error('facturacion', "Error al procesar {$tipoDocumento}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(), // Podemos añadir contexto útil
                'data' => $validatedData // Añadimos los datos que causaron el error para facilitar la depuración
            ]);
            // --- FIN: Uso del Logger Modular ---

            // Devuelve al usuario al formulario con los datos que ya había llenado y un mensaje de error claro
            return back()->withInput()->with('error', "Error al procesar la {$tipoDocumento}: " . $e->getMessage());
        }

        return back()->with('error', 'Acción no válida.')->withInput();
    }

    /**
     * Muestra los detalles de un CFDI específico.
     *
     * @param Cfdi $cfdi
     * @return \Illuminate\View\View
     */
    public function show(Cfdi $cfdi)
    {
        // La vista show.blade.php espera la variable $facturacion, así que la pasamos con ese nombre.
        // También cargamos las relaciones necesarias para la vista de detalle.
        $cfdi->load('cliente', 'conceptos');

        // Lógica para encontrar CFDI relacionados (si es nota de crédito o tiene notas de crédito)
        $facturaOriginal = $cfdi->cfdiRelacionado->relacionado ?? null;
        $relacionadoPor = $cfdi->relacionadoPor ?? collect();

        return view('facturacion::cfdis.show', [
            'facturacion' => $cfdi,
            'facturaOriginal' => $facturaOriginal,
            'relacionadoPor' => $relacionadoPor,
        ]);
    }
}