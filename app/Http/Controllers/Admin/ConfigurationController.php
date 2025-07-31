<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\Tenant;
use App\Models\TenantSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(Request $request, Tenant $tenant = null): View|RedirectResponse
    {
        // Si se envía el formulario de filtro, redirigimos a una URL limpia
        if ($request->has('tenant_id') && $request->tenant_id) {
            return redirect()->route('admin.configuration.index', $request->tenant_id);
        }

        $tenants = Tenant::orderBy('name')->get();
        $settings = collect();
        $sucursales = collect();

        if ($tenant) {
            $settings = TenantSetting::where('tenant_id', $tenant->id)
                ->get()
                ->keyBy(function ($item) {
                    return $item->sucursal_id ? $item->key . '_sucursal_' . $item->sucursal_id : $item->key;
                });

            // Cambiamos temporalmente el contexto al tenant seleccionado para obtener sus sucursales
            tenancy()->initialize($tenant);
            $sucursales = Sucursal::all();
            tenancy()->end(); // Volvemos al contexto central
        }

        return view('admin.configuration.index', [
            'tenants' => $tenants,
            'selectedTenant' => $tenant,
            'settings' => $settings,
            'sucursales' => $sucursales,
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validatedData = $request->except('_token', '_method');

        // Manejo de subida de logos (principal y de sucursales)
        foreach ($request->files as $key => $file) {
            if (str_starts_with($key, 'logo_') || $key === 'main_logo') {
                $path = $file->store('logos', 'public');
                $validatedData[$key] = $path;
            }
        }

        foreach ($validatedData as $keyWithSucursal => $value) {
            $sucursalId = null;
            $key = $keyWithSucursal;

            if (preg_match('/(.*)_sucursal_(\d+)/', $keyWithSucursal, $matches)) {
                $key = $matches[1];
                $sucursalId = $matches[2];
            }

            TenantSetting::updateOrCreate(
                ['tenant_id' => $tenant->id, 'key' => $key, 'sucursal_id' => $sucursalId],
                ['value' => $value, 'group' => $this->getGroupForKey($key)]
            );
        }

        return redirect()->route('admin.configuration.index', $tenant)->with('success', 'Configuración guardada exitosamente para ' . $tenant->name);
    }

    // Helper para asignar un grupo a cada clave
    private function getGroupForKey(string $key): string
    {
        if (in_array($key, ['main_logo', 'slogan', 'primary_color', 'secondary_color', 'logo_sucursal'])) return 'general';
        if (in_array($key, ['print_rfc', 'print_footer_text', 'print_show_address', 'print_doctor_cedula'])) return 'impresion';
        if (in_array($key, ['social_facebook', 'social_instagram', 'contact_whatsapp'])) return 'social';
        return 'general';
    }
}