<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Tenants') }}
            </h2>
            <a href="{{ route('admin.tenants.create') }}" class="btn btn-success">
                <i class="fa-solid fa-plus me-1"></i> Crear Nuevo Tenant
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Nombre del Tenant</th>
                            <th scope="col">Admin</th>
                            <th scope="col">Email del Admin</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tenants as $tenant)
                            <tr>
                                <td>{{ $tenant->name }}</td>
                                <td>{{ $tenant->users->first()->name ?? 'N/A' }}</td>
                                <td>{{ $tenant->users->first()->email ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.licencias.index', ['tenant' => $tenant->id]) }}" class="btn btn-sm btn-info" title="Gestionar Licencias"><i class="fa-solid fa-id-card"></i></a>
                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                    <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tenant? Esta acción no se puede deshacer.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay tenants registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tenants->hasPages())
                <div class="mt-3">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>