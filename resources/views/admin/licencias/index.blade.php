<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 fw-bold">
                    {{ __('Gestión de Licencias') }}
                </h2>
                @if($selectedTenant)
                    <p class="text-muted mb-0">Mostrando licencias para: <strong>{{ $selectedTenant->name }}</strong></p>
                @endif
            </div>
            <div>
                @if($selectedTenant)
                    <a href="{{ route('admin.licencias.index') }}" class="btn btn-secondary me-2" title="Quitar filtro"><i class="fa-solid fa-times"></i> Ver Todas</a>
                @endif
                <a href="{{ route('admin.licencias.create', $selectedTenant ? ['tenant_id' => $selectedTenant->id] : []) }}" class="btn btn-success">
                    <i class="fa-solid fa-plus me-1"></i> Crear Nueva Licencia
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
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
                            <th scope="col">Código de Licencia</th>
                            <th scope="col">Expira</th>
                            <th scope="col" class="text-center">Usuarios</th>
                            <th scope="col" class="text-center">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($licencias as $licencia)
                            <tr>
                                <td>{{ $licencia->tenant->name ?? 'Sin Asignar' }}</td>
                                <td>
                                    <i class="fa-solid {{ $licencia->modulo->icono ?? 'fa-cube' }} fa-fw me-2 text-secondary"></i>
                                    {{ $licencia->modulo->nombre }}
                                </td>
                                <td>
                                    <span class="font-monospace small">{{ $licencia->codigo_licencia }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($licencia->fecha_fin)->format('d/m/Y') }}</td>
                                <td class="text-center">{{ $licencia->limite_usuarios }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $licencia->is_active ? 'success' : 'danger' }}">
                                        {{ $licencia->is_active ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.licencias.edit', $licencia) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                    <form action="{{ route('admin.licencias.destroy', $licencia) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta licencia?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No hay licencias registradas.</td>
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
</x-layouts.app>