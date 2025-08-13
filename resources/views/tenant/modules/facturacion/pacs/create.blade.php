<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-server me-2"></i>
            Añadir Nuevo Proveedor (PAC)
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.facturacion.configuracion.pacs.store') }}" method="POST">
                @include('facturacion::pacs._form', ['pac' => new \App\Modules\Facturacion\Models\Configuracion\Pac()])
            </form>
        </div>
    </div>
</x-layouts.app>
