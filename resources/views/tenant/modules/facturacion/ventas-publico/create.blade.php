<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-cash-register me-2"></i>
            Registrar Nueva Venta al Público
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.facturacion.ventas-publico.store') }}" method="POST">
                @include('facturacion::ventas-publico._form')
            </form>
        </div>
    </div>
</x-layouts.app>