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
                        <label for="serie_folio_id" class="form-label">Serie</label>
                        <select class="form-control" name="serie_folio_id" required>
                            @foreach ($series as $serie)
                                <option value="{{ $serie->id }}" @if($pago->serie_folio_id == $serie->id) selected @endif>{{ $serie->serie }}</option>
                            @endforeach
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clienteSelect = $('#cliente_id');
            const findInvoicesBtn = document.getElementById('find-invoices-btn');
            const invoicesContainer = document.getElementById('invoices-container');
            const invoicesTableBody = document.getElementById('invoices-table-body');
            const montoTotalInput = document.getElementById('monto_total');

            // Inicializar Select2 para clientes
            clienteSelect.select2({
                placeholder: 'Selecciona un cliente',
                ajax: {
                    url: '{{ route("tenant.documents.search.clients") }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.nombre_completo + ' (' + item.rfc + ')',
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            // Habilitar el botón de búsqueda si hay un cliente seleccionado al cargar
            if (clienteSelect.val()) {
                findInvoicesBtn.disabled = false;
            }

            clienteSelect.on('select2:select', function (e) {
                findInvoicesBtn.disabled = false;
                // Limpiar facturas si se cambia de cliente
                invoicesContainer.classList.add('d-none');
                invoicesTableBody.innerHTML = '';
            });

            // Buscar facturas pendientes al hacer clic en el botón
            findInvoicesBtn.addEventListener('click', async function () {
                // ... (La lógica de búsqueda y renderizado de facturas es idéntica a create.blade.php)
            });

            // ... (El resto del script para manejar checkboxes y montos es idéntico a create.blade.php)

            // Antes de enviar el formulario, construir los datos de los documentos relacionados
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                // Esta lógica solo añade los NUEVOS documentos seleccionados.
                // El controlador deberá manejar la lógica de no duplicar si se vuelve a seleccionar uno ya existente.
                document.querySelectorAll('.invoice-checkbox:checked').forEach((checkbox, index) => {
                    const invoice = JSON.parse(checkbox.dataset.invoice);
                    const row = checkbox.closest('tr');
                    const amount = row.querySelector('.payment-amount').value;

                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][id_documento]" value="${invoice.uuid_fiscal}">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][serie]" value="${invoice.serie}">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][folio]" value="${invoice.folio}">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][moneda_dr]" value="MXN">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][num_parcialidad]" value="1">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][imp_saldo_ant]" value="${invoice.saldo_pendiente}">`);
                    this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][imp_pagado]" value="${amount}">`);
                });
            });
        });
    </script>
    @endpush
</x-layouts.app>
