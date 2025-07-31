<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Sucursal') }}: {{ $sucursal->nombre }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.sucursales.update', $sucursal) }}" method="POST">
                @method('PUT')
                @include('tenant.sucursales._form', ['submitText' => __('Actualizar Sucursal')])
            </form>
        </div>
    </div>
</x-layouts.app>