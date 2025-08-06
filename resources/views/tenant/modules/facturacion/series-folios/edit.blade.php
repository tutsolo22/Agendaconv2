<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-hashtag me-2"></i>
            Editar Serie y Folio: {{ $series_folio->serie }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <form action="{{ route('tenant.facturacion.series-folios.update', $series_folio) }}" method="POST">
                @method('PUT')
                @include('facturacion::series-folios._form', ['serie' => $series_folio])
            </form>
        </div>
    </div>
</x-layouts.app>