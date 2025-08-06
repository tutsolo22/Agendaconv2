<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Clientes') }}
            </h2>
            <a href="{{ route('tenant.clientes.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Crear Cliente
            </a>
        </div>
    </x-slot>

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-header">
            <form action="{{ route('tenant.clientes.index') }}" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, RFC o email..." value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-search"></i></button>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Nombre / Razón Social</th>
                            <th>RFC</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->nombre_completo }}</td>
                                <td>{{ $cliente->rfc ?? 'N/A' }}</td>
                                <td>{{ $cliente->email ?? 'N/A' }}</td>
                                <td>{{ $cliente->telefono ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver Documentos">
                                        <i class="fa-solid fa-folder-open"></i>
                                    </a>
                                    <a href="{{ route('tenant.clientes.edit', $cliente) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('tenant.clientes.destroy', $cliente) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este cliente?');">
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
                                <td colspan="5" class="text-center text-muted py-4">
                                    No se encontraron clientes.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($clientes->hasPages())
            <div class="card-footer">
                {{ $clientes->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>