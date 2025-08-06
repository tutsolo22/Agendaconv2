<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 fw-bold mb-0">
                    {{ __('Detalles del Cliente') }}
                </h2>
                <p class="text-muted mb-0">{{ $cliente->nombre_completo }}</p>
            </div>
            <a href="{{ route('tenant.clientes.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="row">
        {{-- Columna de Información del Cliente --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <p><strong>RFC:</strong> {{ $cliente->rfc ?? 'No especificado' }}</p>
                    <p><strong>Email:</strong> {{ $cliente->email ?? 'No especificado' }}</p>
                    <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? 'No especificado' }}</p>
                    <p><strong>Tipo:</strong> {{ ucfirst($cliente->tipo) }}</p>
                    <hr>
                    <p class="mb-0"><strong>Dirección Fiscal:</strong><br>{{ $cliente->direccion_fiscal ?? 'No especificada' }}</p>
                </div>
                <div class="card-footer text-end">
                     <a href="{{ route('tenant.clientes.edit', $cliente) }}" class="btn btn-sm btn-warning">
                        <i class="fa-solid fa-pencil-alt me-1"></i> Editar Cliente
                    </a>
                </div>
            </div>
        </div>

        {{-- Columna de Documentos --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Documentos Asociados ({{ $cliente->documentos->count() }})</h5>
                    <a href="{{ route('tenant.documents.upload.index') }}" class="btn btn-sm btn-primary">
                        <i class="fa-solid fa-upload me-1"></i> Subir Nuevo Documento
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Descripción</th>
                                    <th>Módulo</th>
                                    <th>Subido por</th>
                                    <th>Fecha</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cliente->documentos as $documento)
                                    <tr>
                                        <td>
                                            {{ $documento->descripcion ?? 'Sin descripción' }}
                                            <small class="d-block text-muted">{{ $documento->nombre_original }}</small>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $documento->modulo->nombre }}</span></td>
                                        <td>{{ $documento->subidoPor->name }}</td>
                                        <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ Illuminate\Support\Facades\Storage::url($documento->ruta_archivo) }}" target="_blank" class="btn btn-sm btn-success" title="Descargar">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                            <form action="{{ route('tenant.documents.destroy', $documento) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este documento? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Documento"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Este cliente no tiene documentos asociados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>