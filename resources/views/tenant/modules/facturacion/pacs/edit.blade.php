<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-server me-2"></i>
            Editar Proveedor: {{ $pac->nombre }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.facturacion.pacs.update', $pac) }}" method="POST">
                @method('PUT')
                @include('facturacion::pacs._form')
            </form>
        </div>
    </div>
</x-layouts.app>
