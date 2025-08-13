<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-globe me-2"></i>
            Crear Factura Global
        </h2>
    </x-slot>

    <form action="{{ route('tenant.facturacion.cfdis.store-global') }}" method="POST" id="global-invoice-form">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5>1. Conceptos de la Factura Global</h5>
                <button type="button" id="add-concepto" class="btn btn-success btn-sm">
                    <i class="fa-solid fa-plus me-2"></i>Agregar Concepto
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0" id="conceptos-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Folio / Nota</th>
                                <th style="width: 30%;">Descripción</th>
                                <th style="width: 15%;">Fecha</th>
                                <th style="width: 15%;" class="text-end">Monto Base</th>
                                <th style="width: 15%;" class="text-center">Agregar 16% IVA</th>
                                <th style="width: 10%;" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="conceptos-container">
                            {{-- Las filas de conceptos se agregarán aquí dinámicamente --}}
                        </tbody>
                    </table>
                </div>
                <div id="no-conceptos" class="text-center text-muted p-4">
                    Aún no has agregado ningún concepto.
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <label for="serie_folio_id" class="form-label">Serie para la Factura Global</label>
                        <select name="serie_folio_id" id="serie_folio_id" class="form-select" required>
                            @foreach($series as $serie)
                                <option value="{{ $serie->id }}">Serie {{ $serie->serie }} (Folio actual: {{ $serie->folio_actual }})</option>
                            @endforeach
                        </select>
                        <div id="serie-alert-container"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-end">
                            <p class="mb-1">Subtotal: <span id="total-subtotal" class="fw-bold">$0.00</span></p>
                            <p class="mb-1">IVA (16%): <span id="total-iva" class="fw-bold">$0.00</span></p>
                            <h5 class="mb-0">Total a Facturar: <span id="total-facturar" class="fw-bold">$0.00</span></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('tenant.facturacion.cfdis.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-times me-2"></i>Cancelar
            </a>
            <button type="submit" class="btn btn-primary" id="submit-button" disabled>
                <i class="fa-solid fa-globe me-2"></i>Generar Factura Global
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        // Pasamos las rutas necesarias a JavaScript de forma segura
        window.createSerieUrl = "{{ route('tenant.facturacion.configuracion.series-folios.create') }}";
    </script>
    @vite(['resources/js/Modules/Facturacion/global/global.js'])
    @endpush
</x-layouts.app>