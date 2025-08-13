<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-server me-2"></i>
                Gestión de Proveedores de Timbrado (PACs)
            </h2>
            <a href="{{ route('tenant.facturacion.configuracion.pacs.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Añadir PAC
            </a>
        </div>
    </x-slot>

    @include('partials.flash-messages')

    <div class="alert alert-info">
        <i class="fa-solid fa-circle-info me-2"></i>
        Aquí se registran los proveedores de timbrado disponibles. Para seleccionar cuál usar, vaya a la sección de "Datos Fiscales".
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Usuario</th>
                            <th>URL Producción</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pacs as $pac)
                            <tr>
                                <td>{{ $pac->nombre }}</td>
                                <td>{{ $pac->rfc }}</td>
                                <td>{{ $pac->usuario }}</td>
                                <td class="small">{{ $pac->url_produccion }}</td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $pac->is_active ? 'success' : 'secondary' }}">
                                        {{ $pac->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.facturacion.configuracion.pacs.edit', $pac) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('tenant.facturacion.configuracion.pacs.destroy', $pac) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este proveedor?');">
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
                                <td colspan="6" class="text-center text-muted py-4">No hay proveedores (PACs) registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
