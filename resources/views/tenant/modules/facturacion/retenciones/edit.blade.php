<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-signature me-2"></i>
            Editar Retención {{ $retencion->serie }}-{{ $retencion->folio }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')

            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info me-2"></i>
                Funcionalidad para guardar y timbrar en desarrollo.
            </div>

            <form action="{{ route('tenant.facturacion.retenciones.update', $retencion) }}" method="POST" id="retencion-form">
                @method('PUT')
                @include('facturacion::retenciones._form', ['retencion' => $retencion])
            </form>
        </div>
    </div>
</x-layouts.app>