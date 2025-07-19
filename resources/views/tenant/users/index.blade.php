<x-layouts.admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Gestión de Usuarios') }} 
                {{-- Muestra el contador de usuarios --}}
                @if(isset($maxUsers))
                    <span class="badge bg-secondary fw-normal ms-2">{{ $userCount }} / {{ $maxUsers }}</span>
                @endif
            </h2>
            {{-- Deshabilita el botón si se alcanza el límite --}}
            <a href="{{ route('tenant.users.create') }}" class="btn btn-primary {{ (isset($maxUsers) && $userCount >= $maxUsers) ? 'disabled' : '' }}">
                <i class="fa-solid fa-plus"></i> {{ __('Nuevo Usuario') }}
            </a>
        </div>
    </x-slot>

    {{-- Alertas de éxito o error --}}
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
                            <th scope="col">Correo Electrónico</th>
                            <th scope="col">Rol</th>
                            <th scope="col" class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->roles->pluck('name')->implode(', ') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('tenant.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar este usuario?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>