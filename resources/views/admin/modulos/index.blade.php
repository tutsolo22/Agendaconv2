<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Módulos') }}
            </h2>
            <a href="{{ route('admin.modulos.create') }}" class="btn btn-success">
                <i class="fa-solid fa-plus me-1"></i> Crear Nuevo Módulo
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Ruta Principal</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($modulos as $modulo)
                            <tr>
                                <td>
                                    <i class="fa-solid {{ $modulo->icono ?? 'fa-cube' }} fa-fw me-2 text-secondary"></i>
                                    {{ $modulo->nombre }}
                                </td>
                                <td><span class="font-monospace">{{ $modulo->slug }}</span></td>
                                <td><span class="font-monospace">{{ $modulo->route_name ?? 'N/A' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $modulo->is_active ? 'success' : 'danger' }}">
                                        {{ $modulo->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.modulos.edit', $modulo) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                    <form action="{{ route('admin.modulos.destroy', $modulo) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este módulo?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay módulos registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($modulos->hasPages())
                <div class="mt-3">
                    {{ $modulos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>