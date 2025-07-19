<x-layouts.admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Licencias') }}
            </h2>
            {{-- Botón para crear nueva licencia --}}
            <a href="{{ route('admin.licencias.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('Nueva Licencia') }}
            </a>
        </div>
    </x-slot>

    {{-- Alerta de éxito --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Tenant</th>
                            <th scope="col">Módulo</th>
                            <th scope="col">Fecha Expiración</th>
                            <th scope="col">Máx. Usuarios</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($licencias as $licencia)
                            <tr>
                                <td>{{ $licencia->tenant->name }}</td>
                                <td>{{ $licencia->modulo->nombre }}</td>
                                <td>{{ \Carbon\Carbon::parse($licencia->fecha_expiracion)->format('d/m/Y') }}</td>
                                <td>{{ $licencia->max_usuarios }}</td>
                                <td>
                                    @if ($licencia->is_active)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-danger">Inactiva</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.licencias.edit', $licencia) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.licencias.destroy', $licencia) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar esta licencia?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay licencias registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($licencias->hasPages())
                <div class="mt-3">
                    {{ $licencias->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>