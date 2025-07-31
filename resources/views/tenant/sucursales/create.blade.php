<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nueva Sucursal') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.sucursales.store') }}" method="POST">
                @include('tenant.sucursales._form', ['submitText' => __('Crear Sucursal')])
            </form>
        </div>
    </div>
</x-layouts.app>