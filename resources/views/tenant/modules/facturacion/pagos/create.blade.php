<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Nuevo Complemento de Pago
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')

            <form action="{{ route('tenant.facturacion.pagos.store') }}" method="POST" id="payment-form">
                @csrf

                {{-- Datos Generales del Pago --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            {{-- Opciones de cliente se cargarán con Select2 --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="serie_folio_id" class="form-label">Serie y Folio</label>
                        <select class="form-control" id="serie_folio_id" name="serie_folio_id" required>
                            {{-- Options will be loaded dynamically by TomSelect --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                        <input type="datetime-local" class="form-control" name="fecha_pago" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="forma_pago" class="form-label">Forma de Pago</label>
                        <select class="form-control" name="forma_pago" required>
                            @foreach ($formasPago as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="moneda" class="form-label">Moneda</label>
                        <input type="text" class="form-control" name="moneda" value="MXN" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="monto" class="form-label">Monto Total del Pago</label>
                        <input type="number" step="0.01" class="form-control" name="monto" id="monto_total" required>
                    </div>
                </div>

                <hr>

                {{-- Documentos Relacionados --}}
                <h4>Documentos Relacionados</h4>
                <button type="button" class="btn btn-secondary mb-3" id="find-invoices-btn" disabled>Buscar Facturas Pendientes</button>

                <div id="invoices-container" class="d-none">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th>UUID</th>
                                <th>Serie-Folio</th>
                                <th>Saldo Pendiente</th>
                                <th>Monto a Pagar</th>
                            </tr>
                        </thead>
                        <tbody id="invoices-table-body">
                            {{-- Las facturas se insertarán aquí con JS --}}
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Guardar Borrador</button>
                    <a href="{{ route('tenant.facturacion.pagos.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{-- Pasamos las rutas y configuración al script externo --}}
    <script>
        window.pagosConfig = {
            isNewRecord: true,
            urls: {
                searchClients: '{{ route("tenant.documents.search.clients") }}',
                searchInvoices: '{{ route("tenant.facturacion.pagos.search.invoices") }}',
                series: '{{ route("tenant.api.facturacion.series") }}',
                createSerieUrl: '{{ route("tenant.facturacion.configuracion.series-folios.create") }}'
            }
        };
    </script>
    @vite(['resources/js/Modules/Facturacion/pagos/pagos.js'])
    @endpush
</x-layouts.app>
