<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Sucursales') }}
            </h2>
            <a href="{{ route('tenant.sucursales.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> {{ __('Crear Sucursal') }}
            </a>
        </div>
    </x-slot>

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
                            <th scope="col">Nombre</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sucursales as $sucursal)
                            <tr>
                                <td>{{ $sucursal->nombre }}</td>
                                <td>{{ $sucursal->direccion }}</td>
                                <td>{{ $sucursal->telefono }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.sucursales.edit', $sucursal) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                    <form action="{{ route('tenant.sucursales.destroy', $sucursal) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar esta sucursal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay sucursales registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($sucursales->hasPages())
                <div class="mt-3">{{ $sucursales->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>