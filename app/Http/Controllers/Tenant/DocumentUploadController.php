<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\ClienteDocumento;
use App\Models\Modulo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DocumentUploadController extends Controller
{
    /**
     * Muestra la vista para subir documentos.
     */
    public function index(): View
    {
        // El TenantScope se aplica automáticamente
        $modulos = Modulo::whereIn('id', Auth::user()->tenant->licencias->pluck('modulo_id'))->get();
        return view('tenant.documents.upload', compact('modulos'));
    }

    /**
     * Busca clientes por término de búsqueda (para AJAX).
     */
    public function searchClients(Request $request): JsonResponse
    {
        $term = $request->input('term');

        // El TenantScope se aplica automáticamente
        $clientes = Cliente::where('nombre_completo', 'LIKE', "%{$term}%")
            ->orWhere('rfc', 'LIKE', "%{$term}%")
            ->select('id', 'nombre_completo', 'rfc')
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    /**
     * Almacena el archivo subido.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'modulo_id' => 'required|exists:modulos,id',
            'documento' => 'required|file|mimes:pdf,jpg,png,jpeg,xml,zip|max:10240', // 10MB Max
            'descripcion' => 'nullable|string|max:255',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $cliente = Cliente::findOrFail($request->cliente_id); // TenantScope aplica
        $modulo = Modulo::findOrFail($request->modulo_id);

        // Construye la ruta dinámica y segura
        $directorio = "{$tenantId}/{$modulo->slug}/cliente_{$cliente->id}";

        // Almacena el archivo y obtiene su ruta
        $rutaArchivo = $request->file('documento')->store($directorio, 'public');

        // Guarda la referencia en la base de datos
        $cliente->documentos()->create([
            'modulo_id' => $modulo->id,
            'subido_por_user_id' => Auth::id(),
            'nombre_original' => $request->file('documento')->getClientOriginalName(),
            'ruta_archivo' => $rutaArchivo,
            'mime_type' => $request->file('documento')->getMimeType(),
            'descripcion' => $request->input('descripcion'),
        ]);

        return back()->with('success', 'Documento subido exitosamente.');
    }

    /**
     * Elimina un documento específico.
     */
    public function destroy(ClienteDocumento $documento): RedirectResponse
    {
        // El TenantScope en el modelo ya asegura que el documento pertenece al tenant.
        // 1. Eliminar el archivo físico del storage.
        Storage::disk('public')->delete($documento->ruta_archivo);

        // 2. Eliminar el registro de la base de datos.
        $documento->delete();

        return back()->with('success', 'Documento eliminado exitosamente.');
    }
}