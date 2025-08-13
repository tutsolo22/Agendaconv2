<?php

namespace App\Modules\Facturacion\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Configuracion\Pac;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PacController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pacs = Pac::latest()->get();
        return view('facturacion::pacs.index', compact('pacs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pasamos una nueva instancia para consistencia en el formulario
        $pac = new Pac();
        return view('facturacion::pacs.create', compact('pac'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => ['required', 'string', 'size:12,13', Rule::unique('facturacion_pacs', 'rfc')->where('tenant_id', tenant('id'))],
            'url_produccion' => 'required|url',
            'url_pruebas' => 'nullable|url',
            'usuario' => 'nullable|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Pac::create($validated);

        return redirect()->route('tenant.facturacion.configuracion.pacs.index')
            ->with('success', 'Proveedor (PAC) creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pac $pac)
    {
        return view('facturacion::pacs.edit', compact('pac'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pac $pac)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'rfc' => ['required', 'string', 'size:12,13', Rule::unique('facturacion_pacs', 'rfc')->where('tenant_id', tenant('id'))->ignore($pac->id)],
            'url_produccion' => 'required|url',
            'url_pruebas' => 'nullable|url',
            'usuario' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // No actualizar la contraseña si el campo viene vacío
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $pac->update($validated);

        return redirect()->route('tenant.facturacion.configuracion.pacs.index')
            ->with('success', 'Proveedor (PAC) actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pac $pac)
    {
        $pac->delete();

        return redirect()->route('tenant.facturacion.configuracion.pacs.index')
            ->with('success', 'Proveedor (PAC) eliminado exitosamente.');
    }
}