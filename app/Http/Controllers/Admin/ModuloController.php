<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreModuloRequest;
use App\Http\Requests\Admin\UpdateModuloRequest;
use App\Models\Modulo;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuloController extends Controller
{
    public function index(): View
    {
        $modulos = Modulo::latest()->paginate(10);
        return view('admin.modulos.index', compact('modulos'));
    }

    public function create(): View
    {
        // Pasamos un modelo vacío con valores por defecto para estandarizar el formulario.
        $modulo = new Modulo(['is_active' => true]);
        return view('admin.modulos.create', compact('modulo'));
    }

    public function store(StoreModuloRequest $request): RedirectResponse
    {
        $data = $request->validated();
        // El checkbox no se envía si no está marcado, así que lo manejamos explícitamente.
        $data['is_active'] = $request->has('is_active');
        
        if (isset($data['submenu'])) {
            $data['submenu'] = json_decode($data['submenu'], true);
        }

        Modulo::create($data);

        return redirect()->route('admin.modulos.index')
            ->with('success', 'Módulo creado exitosamente.');
    }

    public function edit(Modulo $modulo): View
    {
        return view('admin.modulos.edit', compact('modulo'));
    }

    public function update(UpdateModuloRequest $request, Modulo $modulo): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        if (isset($data['submenu'])) {
            $data['submenu'] = json_decode($data['submenu'], true);
        }

        $modulo->update($data);

        return redirect()->route('admin.modulos.index')
            ->with('success', 'Módulo actualizado exitosamente.');
    }

    public function destroy(Modulo $modulo): RedirectResponse
    {
        // **Regla de seguridad:** Prevenir la eliminación si el módulo está en uso.
        // Para que esto funcione, asegúrate de que tu modelo Modulo tiene la relación `licencias()`.
        if ($modulo->licencias()->exists()) {
            return back()->with('error', 'No se puede eliminar el módulo porque tiene licencias asociadas.');
        }

        $modulo->delete();

        return redirect()->route('admin.modulos.index')
            ->with('success', 'Módulo eliminado exitosamente.');
    }
}