<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-signature me-2"></i>
            Nueva Retención
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')

            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info me-2"></i>
                Funcionalidad para guardar y timbrar en desarrollo.
            </div>
            
            <form action="{{ route('tenant.facturacion.retenciones.store') }}" method="POST" id="retencion-form">
                @include('facturacion::retenciones._form')
            </form>
        </div>
    </div>
</x-layouts.app>