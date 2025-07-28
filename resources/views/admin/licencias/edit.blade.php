<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Licencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.licencias.update', $licencia) }}">
                        @method('PUT')
                        @include('admin.licencias._form', ['submitText' => 'Actualizar Licencia'])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>