<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Usuarios') }}
            </h2>
            <div class="d-flex align-items-center">
                <span class="badge bg-secondary me-3 fs-6">
                    Usuarios: {{ $licenseDetails['count'] }}/{{ $licenseDetails['limit'] }}
                </span>
                <a href="{{ route('tenant.users.create') }}"
                   class="btn btn-primary @if(!$licenseDetails['canAddUsers']) disabled @endif"
                   @if(!$licenseDetails['canAddUsers'])
                       title="Límite de usuarios alcanzado"
                   @endif>
                    <i class="fa-solid fa-plus"></i> {{ __('Crear Usuario') }}
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Alertas --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
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
                            <th scope="col">Email</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Sucursal</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-info">{{ $user->roles->first()->name ?? 'Sin rol' }}</span></td>
                                <td>{{ $user->sucursal->nombre ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                    @if(Auth::id() !== $user->id)
                                        <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este usuario?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="mt-3">{{ $users->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>