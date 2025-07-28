<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Licencia;
use App\Models\Modulo;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class LicenciaController extends Controller
{
    /**
     * Muestra la lista de licencias.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Obtenemos las licencias con sus relaciones (tenant y modulo) para evitar N+1.
        $licencias = Licencia::with(['tenant', 'modulo'])->latest()->paginate(10);
        return view('admin.licencias.index', compact('licencias'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $tenants = Tenant::all();
        $modulos = Modulo::where('is_active', true)->get();
        return view('admin.licencias.create', compact('tenants', 'modulos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // Preparamos los datos para asegurar que 'is_active' tenga un valor booleano
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        // Validamos los datos preparados
        $validatedData = validator($data, [
            'tenant_id' => 'nullable|exists:tenants,id', // Hacemos que el tenant_id sea opcional
            'modulo_id' => [
                'required',
                'exists:modulos,id',
                Rule::unique('licencias')->where(function ($query) use ($request) {
                    return $query->where('tenant_id', $request->tenant_id)->whereNotNull('tenant_id');
                }), // La regla unique solo aplica si se proporciona un tenant_id
            ],
            'fecha_fin' => 'required|date|after:today',
            'limite_usuarios' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ], ['modulo_id.unique' => 'Este tenant ya tiene una licencia para el módulo seleccionado.'])->validate();

        Licencia::create($validatedData);

        return redirect()->route('admin.licencias.index')->with('success', 'Licencia creada exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Licencia  $licencia
     * @return \Illuminate\View\View
     */
    public function edit(Licencia $licencia): View
    {
        $tenants = Tenant::all();
        $modulos = Modulo::where('is_active', true)->get();
        return view('admin.licencias.edit', compact('licencia', 'tenants', 'modulos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Licencia  $licencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Licencia $licencia): RedirectResponse
    {
        // Preparamos los datos para el checkbox
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        // Validamos los datos preparados
        $validatedData = validator($data, [
            'tenant_id' => 'nullable|exists:tenants,id',
            'modulo_id' => [
                'required',
                'exists:modulos,id',
                Rule::unique('licencias')->where(function ($query) use ($request) {
                    return $query->where('tenant_id', $request->tenant_id)->whereNotNull('tenant_id');
                })->ignore($licencia->id), // La regla unique solo aplica si se proporciona un tenant_id
            ],
            'fecha_fin' => 'required|date|after:today',
            'limite_usuarios' => 'required|integer|min:1',
            'is_active' => 'boolean',
        ], ['modulo_id.unique' => 'Este tenant ya tiene una licencia para el módulo seleccionado.'])->validate();

        $licencia->update($validatedData);

        return redirect()->route('admin.licencias.index')->with('success', 'Licencia actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Licencia  $licencia
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Licencia $licencia): RedirectResponse
    {
        try {
            $licencia->delete();
            return redirect()->route('admin.licencias.index')->with('success', 'Licencia eliminada exitosamente.');
        } catch (\Exception $e) {
            // Manejar posibles errores, por ejemplo, si hay restricciones de clave foránea
            return redirect()->route('admin.licencias.index')->with('error', 'No se pudo eliminar la licencia.');
        }
    }
}