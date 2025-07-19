<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // El TenantScope filtra automáticamente las sucursales.
        $sucursales = Sucursal::latest()->paginate(10);
        return view('tenant.sucursales.index', compact('sucursales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tenant.sucursales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        // El Trait TenantScoped se encarga de añadir el tenant_id.
        Sucursal::create($validated);

        return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sucursal $sucursal): View
    {
        // El Route-Model Binding con el TenantScope asegura que solo se pueda editar
        // una sucursal del tenant actual.
        return view('tenant.sucursales.edit', compact('sucursal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sucursal $sucursal): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'is_active' => 'required|boolean',
        ]);

        $sucursal->update($validated);

        return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sucursal $sucursal): RedirectResponse
    {
        try {
            $sucursal->delete();
            return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('tenant.sucursales.index')->with('error', 'No se pudo eliminar la sucursal. Es posible que tenga datos asociados.');
        }
    }
}