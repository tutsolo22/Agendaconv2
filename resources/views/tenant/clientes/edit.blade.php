<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Cliente') }}: {{ $cliente->nombre_completo }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.clientes.update', $cliente) }}" method="POST">
                @method('PUT')
                @include('tenant.clientes._form')
            </form>
        </div>
    </div>
</x-layouts.app>