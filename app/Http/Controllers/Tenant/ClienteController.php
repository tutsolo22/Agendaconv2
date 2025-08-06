<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Cliente::query(); // El TenantScope se aplica automáticamente

        if ($request->has('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nombre_completo', 'LIKE', $searchTerm)
                  ->orWhere('rfc', 'LIKE', $searchTerm)
                  ->orWhere('email', 'LIKE', $searchTerm);
            });
        }

        $clientes = $query->latest()->paginate(10)->withQueryString();

        return view('tenant.clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        $cliente = new Cliente();
        return view('tenant.clientes.create', compact('cliente'));
    }

    public function show(Cliente $cliente): View
    {
        // El TenantScope ya previene que un tenant vea clientes de otro.
        // Carga ansiosa (eager loading) para optimizar consultas y ordenar por más reciente
        $cliente->load(['documentos' => function ($query) {
            $query->latest();
        }, 'documentos.modulo', 'documentos.subidoPor']);

        return view('tenant.clientes.show', compact('cliente'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateRequest($request);

        // El trait TenantScoped asignará el tenant_id automáticamente.
        Cliente::create($data);

        return redirect()->route('tenant.clientes.index')->with('success', 'Cliente creado exitosamente.');
    }

    public function edit(Cliente $cliente): View
    {
        // El TenantScope ya previene que un tenant edite clientes de otro.
        return view('tenant.clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente): RedirectResponse
    {
        $data = $this->validateRequest($request, $cliente->id);
        $cliente->update($data);

        return redirect()->route('tenant.clientes.index')->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        // Regla de negocio: No permitir borrar si tiene documentos asociados.
        if ($cliente->documentos()->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene documentos asociados.');
        }

        $cliente->delete();

        return redirect()->route('tenant.clientes.index')->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Valida los datos del request para store y update.
     */
    private function validateRequest(Request $request, int $clienteId = null): array
    {
        $rules = [
            'nombre_completo' => 'required|string|max:255',
            'rfc' => 'nullable|string|max:13|unique:clientes,rfc,' . $clienteId,
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion_fiscal' => 'nullable|string',
            'tipo' => 'required|in:persona,empresa',
        ];

        $messages = [
            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'rfc.unique' => 'El RFC ingresado ya está registrado.',
            'tipo.required' => 'Debe seleccionar un tipo (Persona o Empresa).',
        ];

        return $request->validate($rules, $messages);
    }
}