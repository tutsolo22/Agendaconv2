<x-layouts.app>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Administradores') }}
            </h2>
            <a href="{{ route('admin.super-admins.create') }}" class="px-4 py-2 bg-green-500 text-white font-semibold rounded-md hover:bg-green-600">
                Crear Nuevo Administrador
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
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Nombre</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Email</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Fecha de Creación</th>
                                    <th class="text-left py-3 px-4 uppercase font-semibold text-sm">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse ($superAdmins as $admin)
                                    <tr class="border-b">
                                        <td class="text-left py-3 px-4">{{ $admin->name }}</td>
                                        <td class="text-left py-3 px-4">{{ $admin->email }}</td>
                                        <td class="text-left py-3 px-4">{{ $admin->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-left py-3 px-4">
                                            <a href="{{ route('admin.super-admins.edit', $admin) }}" class="text-blue-500 hover:text-blue-700 font-semibold">Editar</a>
                                            
                                            {{-- Un admin no puede borrar su propia cuenta --}}
                                            @if(auth()->id() !== $admin->id)
                                                <form action="{{ route('admin.super-admins.destroy', $admin) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este administrador?');" class="inline-block ml-4">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">Eliminar</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">No hay administradores registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>