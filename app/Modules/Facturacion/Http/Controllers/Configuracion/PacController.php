<?php

namespace App\Modules\Facturacion\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Http\Requests\StorePacRequest;
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
        return view('facturacion::pacs.create', [
            'pac' => new Pac(),
            'supportedDrivers' => $this->getSupportedDrivers(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePacRequest $request)
    {
        $validated = $request->validated();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, &$validated) {
            // Si se marca este PAC como activo, desactivamos todos los demás.
            if ($request->has('is_active')) {
                Pac::query()->update(['is_active' => false]);
            }
            $validated['is_active'] = $request->has('is_active');

            // Limpiamos las credenciales que no pertenecen al driver seleccionado.
            $driver = $validated['driver'];
            $credentials = $validated['credentials'];
            $finalCredentials = [];

            if ($driver === 'edicom' || $driver === 'formas_digitales') {
                $finalCredentials['user'] = $credentials['user'];
                $finalCredentials['password'] = $credentials['password'];
            } elseif ($driver === 'sw_sapiens') {
                $finalCredentials['token'] = $credentials['token'];
            }

            $validated['credentials'] = $finalCredentials;

            Pac::create($validated);
        });


        return redirect()->route('tenant.facturacion.configuracion.pacs.index')
            ->with('success', 'Proveedor (PAC) creado exitosamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pac $pac)
    {
        return view('facturacion::pacs.edit', [
            'pac' => $pac,
            'supportedDrivers' => $this->getSupportedDrivers(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StorePacRequest $request, Pac $pac)
    {
        $validated = $request->validated();

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $pac, &$validated) {
            // Si se marca este PAC como activo, desactivamos todos los demás.
            if ($request->has('is_active')) {
                Pac::where('id', '!=', $pac->id)->update(['is_active' => false]);
            }
            $validated['is_active'] = $request->has('is_active');

            // Actualización inteligente de credenciales
            $driver = $validated['driver'];
            $newCredentials = $validated['credentials'];
            $currentCredentials = $pac->credentials;
            $finalCredentials = [];

            if ($driver === 'edicom' || $driver === 'formas_digitales') {
                $finalCredentials['user'] = $newCredentials['user'];
                // Solo actualiza la contraseña si se proporcionó una nueva.
                $finalCredentials['password'] = !empty($newCredentials['password'])
                    ? $newCredentials['password']
                    : ($currentCredentials['password'] ?? '');
            } elseif ($driver === 'sw_sapiens') {
                $finalCredentials['token'] = $newCredentials['token'];
            }

            $validated['credentials'] = $finalCredentials;
            $pac->update($validated);
        });

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

    /**
     * Devuelve la lista de drivers de PACs soportados.
     */
    private function getSupportedDrivers(): array
    {
        return [
            'sw_sapiens' => 'SW Sapiens (Token)',
            'edicom' => 'EDICOM (Usuario y Contraseña)',
            'formas_digitales' => 'Formas Digitales (Usuario y Contraseña)',
        ];
    }
}