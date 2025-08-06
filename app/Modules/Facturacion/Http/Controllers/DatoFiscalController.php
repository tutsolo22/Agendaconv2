<?php

namespace App\Modules\Facturacion\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Http\Requests\StoreDatoFiscalRequest;
use App\Modules\Facturacion\Models\DatoFiscal;
use App\Modules\Facturacion\Models\Pac;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DatoFiscalController extends Controller
{
    public function index(): View
    {
        $datoFiscal = DatoFiscal::firstOrNew(['tenant_id' => auth()->user()->tenant_id]);
        $pacs = Pac::where('is_active', true)->get();

        return view('facturacion::datos-fiscales.index', compact('datoFiscal', 'pacs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rfc' => 'required|string|max:13',
            'razon_social' => 'required|string|max:255',
            'regimen_fiscal_clave' => 'required|string|max:3',
            'cp_fiscal' => 'required|string|max:5',
            'pac_id' => 'nullable|exists:facturacion_pacs,id',
            'en_pruebas' => 'boolean',
            'password_csd' => 'nullable|string|max:255',
            'archivo_cer' => 'nullable|file|mimes:cer',
            'archivo_key' => 'nullable|file|mimes:key',
        ]);

        $tenantId = auth()->user()->tenant_id;
        $datoFiscal = DatoFiscal::updateOrCreate(['tenant_id' => $tenantId], $validated);

        // Manejo de archivos CSD
        if ($request->hasFile('archivo_cer')) {
            $path = $request->file('archivo_cer')->store("tenants/{$tenantId}/csd");
            $datoFiscal->update(['path_cer' => $path]);
        }

        if ($request->hasFile('archivo_key')) {
            $path = $request->file('archivo_key')->store("tenants/{$tenantId}/csd");
            $datoFiscal->update(['path_key' => $path]);
        }

        return back()->with('success', 'Datos fiscales guardados exitosamente.');
    }
}