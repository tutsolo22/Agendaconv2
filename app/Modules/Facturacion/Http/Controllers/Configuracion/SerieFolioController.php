<?php

namespace App\Modules\Facturacion\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Modules\Facturacion\Models\Configuracion\SerieFolio;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SerieFolioController extends Controller
{
    public function index(): View
    {
        $series = SerieFolio::latest()->paginate(10); // TenantScope se aplica
        return view('facturacion::series-folios.index', compact('series'));
    }

    public function create(): View
    {
        // Creamos una instancia vacía para que el formulario no falle y pre-llenamos valores por defecto
        $serie = new SerieFolio(['is_active' => true, 'folio_actual' => 0, 'tipo_comprobante' => 'I']);
        // Obtenemos las sucursales del tenant para el <select>
        $sucursales = Sucursal::all(); // TenantScope se encarga de filtrar por el tenant actual
        return view('facturacion::series-folios.create', compact('serie', 'sucursales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'serie' => ['required', 'string', 'max:10', Rule::unique('facturacion_series_folios', 'serie')->where('tenant_id', tenant('id'))],
            'tipo_comprobante' => 'required|string|size:1',
            'folio_actual' => 'required|integer|min:0',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        SerieFolio::create($validated);

        return redirect()->route('tenant.facturacion.configuracion.series-folios.index')
            ->with('success', 'Serie y folio creados exitosamente.');
    }

    public function edit(SerieFolio $serieFolio): View
    {
        // También necesitamos las sucursales en la vista de edición
        $sucursales = Sucursal::all(); // TenantScope se encarga de filtrar
        return view('facturacion::series-folios.edit', ['serieFolio' => $serieFolio, 'sucursales' => $sucursales]);
    }

    public function update(Request $request, SerieFolio $serieFolio): RedirectResponse
    {
        $validated = $request->validate([
            'serie' => ['required', 'string', 'max:10', Rule::unique('facturacion_series_folios', 'serie')->where('tenant_id', tenant('id'))->ignore($serieFolio->id)],
            'tipo_comprobante' => 'required|string|size:1',
            'folio_actual' => 'required|integer|min:0',
            'sucursal_id' => 'nullable|exists:sucursales,id',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $serieFolio->update($validated);

        return redirect()->route('tenant.facturacion.configuracion.series-folios.index')
            ->with('success', 'Serie y folio actualizados exitosamente.');
    }

    public function destroy(SerieFolio $serieFolio): RedirectResponse
    {
        $serieFolio->delete();
        return redirect()->route('tenant.facturacion.configuracion.series-folios.index')
            ->with('success', 'Serie y folio eliminados exitosamente.');
    }
}