<?php

namespace App\Modules\Facturacion\Http\Controllers\Nomina;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Complemento\Nomina\Recibo;
use App\Modules\Facturacion\Http\Requests\StoreNominaRequest;
use App\Modules\Facturacion\Services\NominaService;
use App\Modules\Facturacion\Services\SatCatalogService; // Asumiendo que este servicio puede obtener catálogos de nómina
use Illuminate\Http\Request;
use Exception;

class NominaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recibos = Recibo::with('empleado')->latest()->paginate(15);
        return view('facturacion::nomina.index', compact('recibos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(SatCatalogService $catalogService)
    {
        // TODO: El SatCatalogService necesita métodos para obtener catálogos de nómina.
        $catalogos = [
            'tipos_nomina' => [], // $catalogService->getTiposNomina(),
            'periodicidades_pago' => [], // $catalogService->getPeriodicidadesPago(),
            // ... otros catálogos necesarios
        ];
        return view('facturacion::nomina.create', compact('catalogos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNominaRequest $request, NominaService $nominaService)
    {
        try {
            $nominaService->crearYTimbrar($request->validated());
            return redirect()->route('tenant.facturacion.nomina.index')
                ->with('success', 'Recibo de nómina creado y timbrado exitosamente.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error al crear el recibo: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $recibo = Recibo::with('empleado', 'detalles', 'incapacidades')->findOrFail($id);
        return view('facturacion::nomina.show', compact('recibo')); // Se necesita crear esta vista
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // La edición de recibos timbrados no suele permitirse.
        return redirect()->route('tenant.facturacion.nomina.show', $id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('tenant.facturacion.nomina.show', $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Lógica para cancelar un recibo
        return redirect()->route('tenant.facturacion.nomina.index')->with('info', 'Función de cancelación no implementada.');
    }

    // --- API Methods ---
    public function searchEmpleados(Request $request)
    {
        $query = $request->input('q', '');
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $empleados = \App\Modules\Facturacion\Models\Complemento\Nomina\Empleado::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('apellido_paterno', 'like', "%{$query}%")
                  ->orWhere('rfc', 'like', "%{$query}%")
                  ->orWhere('curp', 'like', "%{$query}%");
            })
            ->limit(15)
            ->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno', 'rfc']);

        $formatted = $empleados->map(fn($emp) => [
            'id' => $emp->id,
            'text' => "{$emp->nombre} {$emp->apellido_paterno} {$emp->apellido_materno} ({$emp->rfc})"
        ]);

        return response()->json($formatted);
    }
}

