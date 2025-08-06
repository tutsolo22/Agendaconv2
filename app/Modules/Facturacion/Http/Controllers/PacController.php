<?php

namespace App\Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Models\Pac;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PacController extends Controller
{
    public function index(): View
    {
        $pacs = Pac::latest()->paginate(10);
        return view('facturacion::pacs.index', compact('pacs'));
    }

    public function create(): View
    {
        $pac = new Pac();
        return view('facturacion::pacs.create', compact('pac'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'password' => 'required|string|max:255',
            'url_pruebas' => 'required|url',
            'url_produccion' => 'required|url',
            'is_active' => 'boolean',
        ]);

        Pac::create($validated);

        return redirect()->route('tenant.facturacion.pacs.index')
            ->with('success', 'PAC creado exitosamente.');
    }

    public function edit(Pac $pac): View
    {
        return view('facturacion::pacs.edit', compact('pac'));
    }

    public function update(Request $request, Pac $pac): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255',
            'password' => 'nullable|string|max:255', // Nullable para no forzar cambio
            'url_pruebas' => 'required|url',
            'url_produccion' => 'required|url',
            'is_active' => 'boolean',
        ]);

        // No actualizar la contraseña si no se proporciona una nueva
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $pac->update($validated);

        return redirect()->route('tenant.facturacion.pacs.index')
            ->with('success', 'PAC actualizado exitosamente.');
    }

    public function destroy(Pac $pac): RedirectResponse
    {
        $pac->delete();
        return redirect()->route('tenant.facturacion.pacs.index')
            ->with('success', 'PAC eliminado exitosamente.');
    }
}