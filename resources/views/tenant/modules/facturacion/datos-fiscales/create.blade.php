<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-id-card me-2"></i>
            Configurar Datos Fiscales
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <form id="datos-fiscales-form" action="{{ route('tenant.facturacion.configuracion.datos-fiscales.store') }}" method="POST" enctype="multipart/form-data">
                @include('facturacion::datos-fiscales._form')
            </form>
        </div>
    </div>

    @push('scripts')
    {{-- Pasamos datos y rutas de forma segura a JavaScript --}}
    <script>
        window.apiUrls = {};
        window.currentData = {};
        Object.assign(window.apiUrls, {
            catalogos: "{{ route('tenant.api.facturacion.catalogos') }}"
        });
        window.currentData.isNewRecord = true; // Modo creación
        window.currentData.regimenFiscal = ''; // El régimen fiscal empieza vacío
    </script>
    @vite(['resources/js/Modules/Facturacion/configuracion/datosfiscales.js'])
    @endpush
</x-layouts.app>