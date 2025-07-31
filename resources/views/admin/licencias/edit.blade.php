<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Licencia') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.licencias.update', $licencia) }}">
                @method('PUT')
                @include('admin.licencias._form', ['submitText' => __('Actualizar Licencia')])
            </form>
        </div>
    </div>
</x-layouts.app>