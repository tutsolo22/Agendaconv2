<?php

namespace App\Modules\Facturacion\Http\Controllers\Configuracion;

use App\Http\Controllers\Controller;
use App\Modules\Facturacion\Http\Requests\StoreDatoFiscalRequest;
use App\Modules\Facturacion\Models\Configuracion\DatoFiscal;
use App\Modules\Facturacion\Models\Configuracion\Pac;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DatoFiscalController extends Controller
{
    public function index(): View
    {
        // Solo puede haber un registro de datos fiscales por tenant. Lo pasamos como una colección para reusar la lógica de la vista.
        $datosFiscales = DatoFiscal::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('facturacion::datos-fiscales.index', compact('datosFiscales'));
    }

    public function create(): View
    {
        // Si ya existen datos fiscales, no se permite crear otro. Redirigir al index.
        if (DatoFiscal::where('tenant_id', auth()->user()->tenant_id)->exists()) {
            return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')
                ->with('error', 'Ya existen datos fiscales configurados. Solo puede editar los existentes.');
        }

        $datoFiscal = new DatoFiscal(); // Instancia vacía para un nuevo registro
        $pacs = Pac::where('is_active', true)->get();

        return view('facturacion::datos-fiscales.create', compact('datoFiscal', 'pacs'));
    }

    public function store(StoreDatoFiscalRequest $request): RedirectResponse
    {
        $tenantId = auth()->user()->tenant_id;

        // Doble verificación para evitar condiciones de carrera
        if (DatoFiscal::where('tenant_id', $tenantId)->exists()) {
            return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')
                ->with('error', 'Ya existen datos fiscales configurados. Solo puede editar los existentes.');
        }

        $validated = $request->validated();
        $validated['tenant_id'] = $tenantId;
        $validated['en_pruebas'] = $request->has('en_pruebas');

        $datoFiscal = DatoFiscal::create($validated);

        $this->handleFileUploads($request, $datoFiscal);

        return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')->with('success', 'Datos fiscales creados exitosamente.');
    }

    public function edit(DatoFiscal $datoFiscal): View
    {
        // Route model binding se encarga de encontrar el registro.
        // El TenantScope en el modelo DatoFiscal debería prevenir el acceso a datos de otros tenants.
        $pacs = Pac::where('is_active', true)->get();
        return view('facturacion::datos-fiscales.edit', compact('datoFiscal', 'pacs'));
    }

    public function update(StoreDatoFiscalRequest $request, DatoFiscal $datoFiscal): RedirectResponse
    {
        $validated = $request->validated();
        $validated['en_pruebas'] = $request->has('en_pruebas');

        if (empty($validated['password_csd'])) {
            unset($validated['password_csd']);
        }

        $datoFiscal->update($validated);

        $this->handleFileUploads($request, $datoFiscal);

        return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')->with('success', 'Datos fiscales guardados exitosamente.');
    }

    public function destroy(DatoFiscal $datoFiscal): RedirectResponse
    {
        // El TenantScope en el modelo previene el borrado de datos de otros tenants.
        if ($datoFiscal->path_cer) {
            Storage::delete($datoFiscal->path_cer);
        }
        if ($datoFiscal->path_key) {
            Storage::delete($datoFiscal->path_key);
        }

        $datoFiscal->delete();

        return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')
            ->with('success', 'Datos fiscales eliminados exitosamente.');
    }

    private function handleFileUploads(Request $request, DatoFiscal $datoFiscal): void
    {
        $tenantId = $datoFiscal->tenant_id;
        $updateData = [];

        if ($request->hasFile('archivo_cer')) $updateData['path_cer'] = $request->file('archivo_cer')->store("tenants/{$tenantId}/csd");
        if ($request->hasFile('archivo_key')) $updateData['path_key'] = $request->file('archivo_key')->store("tenants/{$tenantId}/csd");

        if (!empty($updateData)) $datoFiscal->update($updateData);
    }
}