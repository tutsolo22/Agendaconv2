<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nuevo Cliente') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.clientes.store') }}" method="POST">
                @include('tenant.clientes._form')
            </form>
        </div>
    </div>
</x-layouts.app>