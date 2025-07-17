<x-layouts.admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Módulos') }}
            </h2>
            <a href="{{ route('admin.modulos.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('Nuevo Módulo') }}
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
            Listado de Módulos
        </div>
        <div class="card-body">
            @if ($modulos->count())
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($modulos as $modulo)
                                <tr>
                                    <td>{{ $modulo->id }}</td>
                                    <td>{{ $modulo->nombre }}</td>
                                    <td>
                                        @if ($modulo->is_active)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-danger">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.modulos.edit', $modulo) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                                        <form action="{{ route('admin.modulos.destroy', $modulo) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este módulo?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash-alt"></i> Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $modulos->links() }}
            @else
                <div class="alert alert-info" role="alert">
                    No se encontraron módulos registrados.
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>