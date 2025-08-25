<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Módulo') }}: {{ $modulo->nombre }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.modulos.update', $modulo) }}" accept-charset="UTF-8">
                @method('PUT')
                @include('admin.modulos._form', ['submitText' => __('Actualizar Módulo')])
            </form>
        </div>
    </div>
</x-layouts.app>