<?php

namespace App\Modules\Facturacion\Http\Controllers\Api;

use App\Http\Controllers\Controller; // This line is already present and correct.
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use App\Models\Cliente;
use App\Modules\Facturacion\Services\SatCatalogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CatalogosApiController extends Controller
{
    protected SatCatalogService $catalogService;

    public function __construct(SatCatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    /**
     * Devuelve todos los catálogos estáticos necesarios para el formulario de creación de CFDI.
     * Es más eficiente que hacer múltiples llamadas a la API.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(): JsonResponse
    {
        // El servicio ya usa caché, por lo que estas llamadas son muy rápidas.
        // --- INICIO: Cambio Solicitado ---
        // Consolidamos todos los catálogos estáticos en una sola respuesta.
        $catalogos = [
            'usosCfdi' => $this->catalogService->getUsosCfdi(),
            'metodosPago' => $this->catalogService->getMetodosPago(),
            'formasPago' => $this->catalogService->getFormasPago(),
            'monedas' => $this->catalogService->getMonedas(),
            'tiposComprobante' => $this->catalogService->getTiposDeComprobante(),
            'objetosImpuesto' => $this->catalogService->getObjetosImpuesto(),
            'regimenesFiscales' => $this->catalogService->getRegimenesFiscales(),
            'clavesUnidad' => $this->catalogService->getClavesUnidad(),
            'periocidades' => $this->catalogService->getPeriodicidades(),
            'meses' => $this->catalogService->getMeses(),
            'tiposRelacion' => $this->catalogService->getTiposRelacion(),
            'retenciones' => $this->catalogService->getRetenciones(),
        ];
        // --- FIN: Cambio Solicitado ---

        return response()->json($catalogos, 200, [], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Método de depuración para aislar la obtención del catálogo de Formas de Pago.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function debugFormasPago(): JsonResponse
    {
        try {
            $formasPago = $this->catalogService->getFormasPago();
            // Si esto devuelve un array vacío, el problema está en SatCatalogService->getFormasPago()
            // Si devuelve datos, el problema está en cómo se integra en el método getAll().
            return response()->json($formasPago);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Ocurrió una excepción en el servicio', 'message' => $e->getMessage()], 500);
        }
    }

    public function tiposComprobante()
    {
        // El frontend espera 'id' y 'descripcion' para construir el texto del dropdown.
        $tiposComprobante = $this->catalogService->getTiposDeComprobante();
        return response()->json($tiposComprobante, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function series()
    {
        // El frontend espera 'id', 'serie' y 'folio_actual' para construir el texto del dropdown.
        $series = SerieFolio::where('is_active', true)
                              ->where('tipo_comprobante', 'I') // Filtramos solo series de Ingreso para facturas.
                              ->get(['id', 'serie', 'folio_actual']);
        return response()->json($series, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function searchClients(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $clients = Cliente::where(function ($q) use ($query) {
                              $q->where('nombre_completo', 'like', "%{$query}%")
                                ->orWhere('rfc', 'like', "%{$query}%");
                          })
                          ->limit(15)
                          ->get(['id', 'nombre_completo', 'rfc', 'regimen_fiscal_receptor', 'codigo_postal_receptor']);

        return response()->json($clients, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function productosServicios(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->searchProductosServicios($query), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function clavesUnidad(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getClavesUnidad($query), 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function usosCfdi(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getUsosCfdi($query), 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function regimenesFiscales(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getRegimenesFiscales($query), 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function periodicidades(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getPeriodicidades($query), 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function meses(Request $request)
    {
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getMeses($query), 200, [], JSON_UNESCAPED_UNICODE);
    }
    public function tiposRelacion(Request $request)
    { 
        $query = $request->input('q', '');
        return response()->json($this->catalogService->getTiposRelacion($query), 200, [], JSON_UNESCAPED_UNICODE);
    }
}
