<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\Tenant;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tenants = Tenant::latest()->paginate(10);

        return view('admin.tenants.index', compact('tenants'));
    }
     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'id' => 'required|string|max:255|unique:tenants', // 'id' se usa a menudo para el subdominio
        ]);

        Tenant::create($validatedData);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant creado exitosamente.');
    }
    public function assignModules(Tenant $tenant): View
    {
        $modulos = Modulo::all();
        $tenantModulos = $tenant->modulos->pluck('id')->toArray(); // Modulos asignados actualmente

        return view('admin.tenants.assign_modules', compact('tenant', 'modulos', 'tenantModulos'));
    }

    public function updateAssignedModules(Request $request, Tenant $tenant): RedirectResponse
    {
        $tenant->modulos()->sync($request->input('modulos', [])); // Sincroniza los módulos asignados

        return redirect()->route('admin.tenants.index')
                        ->with('success', 'Módulos asignados actualizados correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'id' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tenants')->ignore($tenant->id),
            ],
        ]);

        $tenant->update($validatedData);

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')->with('success', 'Tenant eliminado exitosamente.');
    }
}
