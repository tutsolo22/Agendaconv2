<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nuevo Módulo') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.modulos.store') }}" accept-charset="UTF-8">
                @include('admin.modulos._form', ['submitText' => __('Crear Módulo')])
            </form>
        </div>
    </div>
</x-layouts.app>