<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-hashtag me-2"></i>
            Crear Nueva Serie y Folio
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <form action="{{ route('tenant.facturacion.series-folios.store') }}" method="POST">
                @include('facturacion::series-folios._form')
            </form>
        </div>
    </div>
</x-layouts.app>