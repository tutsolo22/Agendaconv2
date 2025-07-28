<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Licencia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TenantLicenciaController extends Controller
{
    /**
     * Muestra las licencias del tenant y el formulario de activación.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $tenantId = Auth::user()->tenant_id;
        $licencias = Licencia::where('tenant_id', $tenantId)
            ->with('modulo')
            ->latest()
            ->get();

        return view('tenant.licencias.index', compact('licencias'));
    }

    /**
     * Activa una licencia usando un código.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activar(Request $request): RedirectResponse
    {
        $request->validate([
            'codigo_licencia' => 'required|string|uuid',
        ]);

        $codigo = $request->input('codigo_licencia');
        $licencia = Licencia::where('codigo_licencia', $codigo)->first();

        // 1. Verificar que la licencia existe
        if (!$licencia) {
            return back()->with('error', 'El código de licencia no es válido.');
        }

        // 2. Verificar que la licencia no ha sido reclamada ya por otro tenant
        if ($licencia->tenant_id !== null) {
            return back()->with('error', 'Esta licencia ya ha sido activada.');
        }

        // 3. Asignar la licencia al tenant actual
        $licencia->tenant_id = Auth::user()->tenant_id;
        $licencia->is_active = true; // Opcional: activar la licencia al reclamarla
        $licencia->save();

        return redirect()->route('tenant.licencias.index')->with('success', '¡Licencia para el módulo "' . $licencia->modulo->nombre . '" activada exitosamente!');
    }
}