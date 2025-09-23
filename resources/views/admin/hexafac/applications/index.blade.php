<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <i class="fa-solid fa-mobile-alt me-2"></i>
                {{ __('Administrar Aplicaciones de HexaFac') }}
            </h2>
            <a href="{{ route('admin.hexafac.applications.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>
                {{ __('Crear Nueva Aplicación') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>{{ __('Nombre') }}</th>
                                    <th>{{ __('Tenant Asociado') }}</th>
                                    <th>{{ __('Descripción') }}</th>
                                    <th>{{ __('Estado') }}</th>
                                    <th class="text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($applications as $app)
                                    <tr>
                                        <td>{{ $app->name }}</td>
                                        <td>
                                            <a href="{{ route('admin.tenants.show', $app->tenant) }}">{{ $app->tenant->id }}</a>
                                        </td>
                                        <td>{{ Str::limit($app->description, 50) }}</td>
                                        <td>
                                            @if ($app->active)
                                                <span class="badge bg-success">{{ __('Activa') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Inactiva') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.hexafac.applications.edit', $app) }}" class="btn btn-sm btn-warning me-2">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.hexafac.applications.destroy', $app) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta aplicación? Esta acción no se puede deshacer.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type.submit="" class="btn btn-sm btn-danger">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            {{ __('No se encontraron aplicaciones.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($applications->hasPages())
                        <div class="mt-4">
                            {{ $applications->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
