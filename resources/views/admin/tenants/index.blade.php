<x-layouts.admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Tenants') }}
            </h2>
            <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('Nuevo Tenant') }}
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            Listado de Tenants
        </div>
        <div class="card-body">
            @if ($tenants->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dominio</th>
                                <th>Creado el</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->id }}</td>
                                    <td>{{ $tenant->name }}</td>
                                    <td>{{ $tenant->domain }}</td>
                                    <td>{{ $tenant->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                                        <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este tenant? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash-alt"></i> Eliminar</button>
                                        </form>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.tenants.assignModules', $tenant) }}" class="text-blue-600 hover:text-blue-900">Asignar Módulos</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $tenants->links() }}
            @else
                <div class="alert alert-info" role="alert">
                    No se encontraron tenants registrados.
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>