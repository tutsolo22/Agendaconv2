<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SucursalController extends Controller
{
    public function index(): View
    {
        // El TenantScope filtra automáticamente las sucursales.
        $sucursales = Sucursal::latest()->paginate(10);
        return view('tenant.sucursales.index', compact('sucursales'));
    }

    public function create(): View
    {
        $sucursal = new Sucursal(['is_active' => true]);
        return view('tenant.sucursales.create', compact('sucursal'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $data['is_active'] = $request->has('is_active');
        // El trait TenantScoped asignará el tenant_id automáticamente.
        Sucursal::create($data);

        return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function edit(Sucursal $sucursal): View
    {
        // El TenantScope ya previene que un tenant edite sucursales de otro.
        return view('tenant.sucursales.edit', compact('sucursal'));
    }

    public function update(Request $request, Sucursal $sucursal): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:50',
        ]);

        $data['is_active'] = $request->has('is_active');
        $sucursal->update($data);

        return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal): RedirectResponse
    {
        // Regla de negocio: No permitir borrar si tiene usuarios asignados.
        if ($sucursal->users()->exists()) {
            return back()->with('error', 'No se puede eliminar la sucursal porque tiene usuarios asignados.');
        }

        $sucursal->delete();

        return redirect()->route('tenant.sucursales.index')->with('success', 'Sucursal eliminada exitosamente.');
    }
}