<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HexaFacApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $applications = \App\Models\HexaFac\HexaFacClientApplication::with('tenant')
            ->latest()
            ->paginate(15);

        return view('admin.hexafac.applications.index', compact('applications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tenants = \App\Models\Tenant::all();
        return view('admin.hexafac.applications.create', compact('tenants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['active'] = $request->has('active');

        \App\Models\HexaFac\HexaFacClientApplication::create($validated);

        return redirect()->route('admin.hexafac.applications.index')
            ->with('success', 'Aplicación creada con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HexaFacClientApplication $application)
    {
        $tenants = Tenant::all();
        return view('admin.hexafac.applications.edit', compact('application', 'tenants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, HexaFacClientApplication $application)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['active'] = $request->has('active');

        $application->update($validated);

        return redirect()->route('admin.hexafac.applications.index')
            ->with('success', 'Aplicación actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HexaFacClientApplication $application)
    {
        $application->delete();

        return redirect()->route('admin.hexafac.applications.index')
            ->with('success', 'Aplicación eliminada con éxito.');
    }
}
