<?php

namespace App\Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\VentaPublico;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VentaPublicoController extends Controller
{
    public function index(): View
    {
        $ventas = VentaPublico::whereNull('cfdi_global_id') // Mostrar solo las no facturadas
            ->latest()
            ->paginate(15);
        return view('facturacion::ventas-publico.index', compact('ventas'));
    }

    public function create(): View
    {
        return view('facturacion::ventas-publico.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'total' => 'required|numeric|min:0.01',
        ]);

        VentaPublico::create($validated);

        return redirect()->route('tenant.facturacion.ventas-publico.index')
            ->with('success', 'Venta al público registrada exitosamente.');
    }

    public function edit(VentaPublico $ventaPublico): View
    {
        return view('facturacion::ventas-publico.edit', ['venta' => $ventaPublico]);
    }

    public function update(Request $request, VentaPublico $ventaPublico): RedirectResponse
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'total' => 'required|numeric|min:0.01',
        ]);

        $ventaPublico->update($validated);

        return redirect()->route('tenant.facturacion.ventas-publico.index')
            ->with('success', 'Venta al público actualizada exitosamente.');
    }

    public function destroy(VentaPublico $ventaPublico): RedirectResponse
    {
        $ventaPublico->delete();
        return redirect()->route('tenant.facturacion.ventas-publico.index')
            ->with('success', 'Venta al público eliminada exitosamente.');
    }
}