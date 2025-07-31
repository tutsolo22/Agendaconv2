<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Sucursal;
use App\Models\TenantSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function index(): View
    {
        $tenantId = Auth::user()->tenant_id;

        // Obtenemos todas las configuraciones y las agrupamos por su 'key' para fácil acceso en la vista
        $settings = TenantSetting::where('tenant_id', $tenantId)
            ->get()
            ->keyBy(function ($item) {
                // Creamos una clave única para configuraciones por sucursal
                return $item->sucursal_id ? $item->key . '_sucursal_' . $item->sucursal_id : $item->key;
            });

        $sucursales = Sucursal::all(); // TenantScoped se encarga de filtrar

        return view('tenant.configuration.index', compact('settings', 'sucursales'));
    }

    public function update(Request $request): RedirectResponse
    {
        $tenantId = Auth::user()->tenant_id;
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

            // Detectamos si la clave es para una sucursal específica
            if (preg_match('/(.*)_sucursal_(\d+)/', $keyWithSucursal, $matches)) {
                $key = $matches[1];
                $sucursalId = $matches[2];
            }

            // Usamos updateOrCreate para insertar o actualizar la configuración
            TenantSetting::updateOrCreate(
                ['tenant_id' => $tenantId, 'key' => $key, 'sucursal_id' => $sucursalId],
                ['value' => $value, 'group' => $this->getGroupForKey($key)]
            );
        }

        return redirect()->route('tenant.configuration.index')->with('success', 'Configuración guardada exitosamente.');
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