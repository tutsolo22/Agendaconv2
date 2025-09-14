<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-truck-ramp-box me-2"></i>
            Detalle Carta Porte #{{ $cartaporte->id }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>CFDI Relacionado:</strong> {{ $cartaporte->facturacion_cfdi_id ?? 'N/A' }}</p>
                    <p><strong>Versión:</strong> {{ $cartaporte->version }}</p>
                    <p><strong>Transporte Internacional:</strong> {{ $cartaporte->transp_internac }}</p>
                    <p><strong>ID Carta Porte (CCP):</strong> {{ $cartaporte->id_ccp }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Estado:</strong> 
                        <span class="badge bg-{{ $cartaporte->status == 'timbrado' ? 'success' : ($cartaporte->status == 'cancelado' ? 'danger' : 'warning') }}">
                            {{ ucfirst($cartaporte->status) }}
                        </span>
                    </p>
                    <p><strong>Fecha Creación:</strong> {{ $cartaporte->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Última Actualización:</strong> {{ $cartaporte->updated_at->format('d/m/Y H:i') }}</p>
                    {{-- Display UUID and download links if available --}}
                    @if($cartaporte->uuid)
                        <p><strong>UUID Fiscal:</strong> {{ $cartaporte->uuid }}</p>
                        <div class="d-flex gap-2 mt-3">
                            <a href="#" class="btn btn-sm btn-dark">Descargar XML</a>
                            <a href="#" class="btn btn-sm btn-danger">Descargar PDF</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Details sections (Ubicaciones, Mercancias, etc.) would go here --}}
        {{-- You can iterate through relationships and display them in cards similar to this --}}

        <div class="mt-4">
            <a href="{{ route('tenant.facturacion.cartaporte.index') }}" class="btn btn-secondary">Volver al Listado</a>
        </div>
    </div>

</x-layouts.app>
