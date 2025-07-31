<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nueva Licencia') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.licencias.store') }}">
                @include('admin.licencias._form', ['submitText' => __('Crear Licencia')])
            </form>
        </div>
    </div>
</x-layouts.app>