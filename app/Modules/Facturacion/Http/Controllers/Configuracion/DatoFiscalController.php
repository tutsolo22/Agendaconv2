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
use Illuminate\Validation\ValidationException;
use PhpCfdi\Credentials\Credential;
use Throwable;

class DatoFiscalController extends Controller
{
    public function index(): View
    {
        // Solo puede haber un registro de datos fiscales por tenant. Lo pasamos como una colección para reusar la lógica de la vista.
        $datosFiscales = DatoFiscal::where('tenant_id', auth()->user()->tenant_id)->get();
        return view('facturacion::datos-fiscales.index', compact('datosFiscales'));
    }

    public function create(): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
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

        $this->handleFileUploads($request, $datoFiscal, $validated['password_csd'] ?? null);

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

        $this->handleFileUploads($request, $datoFiscal, $validated['password_csd'] ?? $datoFiscal->password_csd);

        return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')->with('success', 'Datos fiscales guardados exitosamente.');
    }

    public function destroy(DatoFiscal $datoFiscal): RedirectResponse
    {
        // El TenantScope en el modelo previene el borrado de datos de otros tenants.
        // Se corrige el nombre de la columna para que coincida con la migración.
        if ($datoFiscal->path_cer_pem) {
            Storage::delete($datoFiscal->path_cer_pem);
        }
        if ($datoFiscal->path_key_pem) {
            Storage::delete($datoFiscal->path_key_pem);
        }

        $datoFiscal->delete();

        return redirect()->route('tenant.facturacion.configuracion.datos-fiscales.index')
            ->with('success', 'Datos fiscales eliminados exitosamente.');
    }

    /**
     * Maneja la subida, validación y almacenamiento de los archivos CSD.
     * Extrae datos del certificado y los guarda en el modelo.
     *
     * @throws ValidationException
     */
    private function handleFileUploads(StoreDatoFiscalRequest $request, DatoFiscal $datoFiscal, ?string $password): void
    {
        $tenantId = $datoFiscal->tenant_id;
        $updateData = [];
        $hasCer = $request->hasFile('archivo_cer');
        $hasKey = $request->hasFile('archivo_key');

        // Si se sube un archivo, se requiere el otro y la contraseña.
        if (($hasCer || $hasKey) && (!$hasCer || !$hasKey || !$password)) {
            throw ValidationException::withMessages([
                'archivo_cer' => 'Para actualizar los CSD, debe proporcionar el archivo .cer, el .key y la contraseña.',
            ]);
        }

        // Si no se suben nuevos archivos, no hay nada que hacer.
        if (!$hasCer && !$hasKey) {
            return;
        }

        try {
            // Usamos la librería phpcfdi/credentials para validar los archivos antes de guardarlos.
            $credential = Credential::openFiles(
                $request->file('archivo_cer')->getRealPath(),
                $request->file('archivo_key')->getRealPath(),
                $password
            );

            // Verificamos que el RFC del certificado coincida con el del formulario.
            if ($credential->certificate()->rfc() !== $request->input('rfc')) {
                throw ValidationException::withMessages(['rfc' => 'El RFC del certificado no coincide con el RFC proporcionado.']);
            }

            // Si la validación es exitosa, extraemos los datos y preparamos la actualización.
            $updateData['no_certificado'] = $credential->certificate()->serialNumber()->bytes();
            $updateData['razon_social'] = $credential->certificate()->legalName();
            $updateData['valido_desde'] = $credential->certificate()->validFrom();
            $updateData['valido_hasta'] = $credential->certificate()->validTo();
            $updateData['path_cer_pem'] = $request->file('archivo_cer')->store("tenants/{$tenantId}/csd");
            $updateData['path_key_pem'] = $request->file('archivo_key')->store("tenants/{$tenantId}/csd");

        } catch (Throwable $e) {
            // Si algo falla (contraseña incorrecta, archivos no válidos), lanzamos una excepción de validación.
            throw ValidationException::withMessages(['password_csd' => 'La contraseña del CSD es incorrecta o los archivos .cer y .key no son válidos o no se corresponden.']);
        }

        if (!empty($updateData)) $datoFiscal->update($updateData);
    }
}
