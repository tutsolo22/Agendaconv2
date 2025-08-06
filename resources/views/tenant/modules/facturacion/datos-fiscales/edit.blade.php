<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-id-card me-2"></i>
            Actualizar Datos Fiscales
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <form action="{{ route('tenant.datos-fiscales.update', $datoFiscal) }}" method="POST" enctype="multipart/form-data">
                @method('PUT')
                @include('facturacion::datos-fiscales._form')
            </form>
        </div>
    </div>
</x-layouts.app>