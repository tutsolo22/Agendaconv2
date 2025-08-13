<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-hashtag me-2"></i>
            Editar Serie y Folio: {{ $serieFolio->serie }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <form action="{{ route('tenant.facturacion.configuracion.series-folios.update', $serieFolio) }}" method="POST">
                @method('PUT')
                @include('facturacion::series-folios._form', ['serie' => $serieFolio])
            </form>
        </div>
    </div>
</x-layouts.app>