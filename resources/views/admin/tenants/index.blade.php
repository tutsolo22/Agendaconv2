<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Tenants') }}
            </h2>
            <a href="{{ route('admin.tenants.create') }}" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600">
                Crear Nuevo Tenant
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Nombre del Tenant</th>
                                    <th class="w-1/4 text-left py-3 px-4 uppercase font-semibold text-sm">Admin</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email del Admin</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($tenants as $tenant)
                                    <tr class="border-b">
                                        <td class="text-left py-3 px-4">{{ $tenant->name }}</td>
                                        <td class="text-left py-3 px-4">{{ $tenant->users->first()->name ?? 'N/A' }}</td>
                                        <td class="text-left py-3 px-4">{{ $tenant->users->first()->email ?? 'N/A' }}</td>
                                        <td class="text-left py-3 px-4">
                                            <a href="{{ route('admin.tenants.edit', $tenant) }}" class="text-blue-500 hover:text-blue-700 font-semibold">Editar</a>
                                            <form action="{{ route('admin.tenants.destroy', $tenant) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este tenant? Esta acción no se puede deshacer.');" class="inline-block ml-4">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No hay tenants registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $tenants->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>