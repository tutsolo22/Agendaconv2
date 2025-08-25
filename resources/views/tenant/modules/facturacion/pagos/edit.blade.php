<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-pencil me-2"></i>
            Editar Complemento de Pago: {{ $pago->serie }}-{{ $pago->folio }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')

            <form action="{{ route('tenant.facturacion.pagos.update', $pago) }}" method="POST" id="payment-form">
                @csrf
                @method('PUT')

                {{-- Datos Generales del Pago --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="{{ $pago->cliente->id }}" selected>{{ $pago->cliente->nombre_completo }} ({{ $pago->cliente->rfc }})</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="serie_folio_id" class="form-label">Serie y Folio</label>
                        <select class="form-control" id="serie_folio_id" name="serie_folio_id" required>
                            <option value="{{ $pago->serie_folio_id }}" selected>{{ $pago->serie }}-{{ $pago->folio }}</option>
                            {{-- Options will be loaded dynamically by TomSelect --}}
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fecha_pago" class="form-label">Fecha de Pago</label>
                        <input type="datetime-local" class="form-control" name="fecha_pago" value="{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('Y-m-d\TH:i') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="forma_pago" class="form-label">Forma de Pago</label>
                        <select class="form-control" name="forma_pago" required>
                            @foreach ($formasPago as $key => $value)
                                <option value="{{ $key }}" @if($pago->forma_pago == $key) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="moneda" class="form-label">Moneda</label>
                        <input type="text" class="form-control" name="moneda" value="{{ $pago->moneda }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="monto" class="form-label">Monto Total del Pago</label>
                        <input type="number" step="0.01" class="form-control" name="monto" id="monto_total" value="{{ $pago->monto }}" required>
                    </div>
                </div>

                <hr>

                {{-- Documentos Ya Relacionados --}}
                @if($pago->documentos->isNotEmpty())
                    <h4>Documentos Ya Relacionados</h4>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>UUID</th>
                                    <th>Serie-Folio</th>
                                    <th class="text-end">Monto Pagado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pago->documentos as $docto)
                                    <tr>
                                        <td><span title="{{ $docto->id_documento }}">{{ substr($docto->id_documento, 0, 8) }}...</span></td>
                                        <td>{{ $docto->serie }}-{{ $docto->folio }}</td>
                                        <td class="text-end">${{ number_format($docto->imp_pagado, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small">Nota: Para modificar o eliminar documentos existentes, por favor, elimine este borrador y créelo de nuevo. Solo puede añadir nuevos documentos relacionados.</p>
                @endif

                {{-- Añadir Nuevos Documentos Relacionados --}}
                <h4>Añadir Nuevos Documentos Relacionados</h4>
                <button type="button" class="btn btn-secondary mb-3" id="find-invoices-btn">Buscar Facturas Pendientes</button>

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
                    <button type="submit" class="btn btn-primary">Actualizar Borrador</button>
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
            isNewRecord: false,
            initialAmount: {{ $pago->monto ?? 0 }},
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
