<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            @if(isset($facturacion))
                <i class="fa-solid fa-file-invoice me-2"></i>
                {{ __('Crear Nota de Crédito (Egreso)') }}
            @else
                <i class="fa-solid fa-file-circle-plus me-2"></i>
                {{ __('Crear Nueva Factura (Borrador)') }}
            @endif
        </h2>
    </x-slot>

    <form action="{{ route('tenant.facturacion.cfdis.store') }}" method="POST" id="factura-form">
        @if(isset($facturacion))
            <input type="hidden" name="related_uuid" value="{{ $facturacion->uuid_fiscal }}">
            <input type="hidden" name="relation_type" value="01"> {{-- 01 = Nota de crédito de los documentos relacionados --}}
        @endif
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5>1. Datos del Receptor (Cliente)</h5>
            </div>
            <div class="card-body">
                @include('partials.flash-messages')
                
                @if(isset($facturacion))
                    <div class="alert alert-warning">
                        Estás creando una nota de crédito para la factura <strong>{{ $facturacion->serie }}-{{ $facturacion->folio }}</strong>
                        del cliente <strong>{{ $facturacion->cliente->nombre_completo }}</strong>. Los datos se han pre-cargado.
                    </div>
                    <input type="hidden" name="cliente_id" id="cliente_id" value="{{ $facturacion->cliente_id }}">
                @else
                    <div class="mb-3">
                        <label for="search-client" class="form-label">Buscar Cliente (por nombre o RFC)</label>
                        <div class="input-group">
                            <input type="text" id="search-client-input" class="form-control" placeholder="Escriba para buscar...">
                        </div>
                        <div id="search-results" class="list-group mt-2" style="max-height: 200px; overflow-y: auto;"></div>
                    </div>

                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <div id="selected-client-info" class="alert alert-info d-none">
                        <strong>Cliente seleccionado:</strong> <span id="selected-client-name"></span>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5>2. Datos del Comprobante</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="serie_folio_id" class="form-label">Serie</label>
                        <select name="serie_folio_id" id="serie_folio_id" class="form-select" required>
                            @foreach($series as $s)
                                <option value="{{ $s->id }}" @selected(isset($facturacion) && $facturacion->serie_folio_id == $s->id)>Serie {{ $s->serie }} (Folio actual: {{ $s->folio_actual }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="forma_pago" class="form-label">Forma de Pago</label>
                        <select name="forma_pago" id="forma_pago" class="form-select" required>
                            @foreach($formasPago as $c => $d)
                                <option value="{{ $c }}" @selected(isset($facturacion) && $facturacion->forma_pago == $c)>{{ $c }} - {{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago</label>
                        <select name="metodo_pago" id="metodo_pago" class="form-select" required>
                            @foreach($metodosPago as $c => $d)
                                <option value="{{ $c }}" @selected(isset($facturacion) && $facturacion->metodo_pago == $c)>{{ $c }} - {{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="uso_cfdi" class="form-label">Uso de CFDI</label>
                        <select name="uso_cfdi" id="uso_cfdi" class="form-select" required>
                            @foreach($usosCfdi as $c => $d)
                                <option value="{{ $c }}" @selected(isset($facturacion) && $facturacion->uso_cfdi == $c)>{{ $c }} - {{ $d }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>3. Conceptos</h5>
                <button type="button" class="btn btn-success btn-sm" id="add-concepto">
                    <i class="fa-solid fa-plus me-1"></i> Añadir Concepto
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Clave Prod/Serv</th>
                                <th>Cantidad</th>
                                <th>Clave Unidad</th>
                                <th>Descripción</th>
                                <th>Valor Unitario</th>
                                <th>Importe</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="conceptos-container">
                            @if(isset($facturacion))
                                @foreach($facturacion->conceptos as $index => $concepto)
                                    <tr>
                                        <td><input type="text" name="conceptos[{{$index}}][clave_prod_serv]" class="form-control" value="{{ $concepto->clave_prod_serv }}" required readonly></td>
                                        <td><input type="number" name="conceptos[{{$index}}][cantidad]" class="form-control cantidad" value="{{ $concepto->cantidad }}" step="0.01" required readonly></td>
                                        <td><input type="text" name="conceptos[{{$index}}][clave_unidad]" class="form-control" value="{{ $concepto->clave_unidad }}" required readonly></td>
                                        <td><input type="text" name="conceptos[{{$index}}][descripcion]" class="form-control" value="{{ $concepto->descripcion }}" required readonly></td>
                                        <td><input type="number" name="conceptos[{{$index}}][valor_unitario]" class="form-control valor-unitario" value="{{ $concepto->valor_unitario }}" step="0.01" required readonly></td>
                                        <td><input type="text" name="conceptos[{{$index}}][importe]" class="form-control importe" value="{{ $concepto->importe }}" readonly></td>
                                        <td></td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="text-end">Subtotal:</th>
                                    <td class="text-end" id="subtotal-display">$0.00</td>
                                </tr>
                                <tr>
                                    <th class="text-end">IVA (16%):</th>
                                    <td class="text-end" id="impuestos-display">$0.00</td>
                                </tr>
                                <tr>
                                    <th class="text-end">Total:</th>
                                    <td class="text-end fw-bold fs-5" id="total-display">$0.00</td>
                                </tr>
                            </tbody>
                        </table>
                        <input type="hidden" name="subtotal" id="subtotal-input" value="{{ $facturacion->subtotal ?? 0 }}">
                        <input type="hidden" name="impuestos" id="impuestos-input" value="{{ $facturacion->impuestos ?? 0 }}">
                        <input type="hidden" name="total" id="total-input" value="{{ $facturacion->total ?? 0 }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('tenant.facturacion.cfdis.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="submit-button" @if(!isset($facturacion)) disabled @endif>
                <i class="fa-solid fa-save me-2"></i>Guardar Borrador
            </button>
        </div>
    </form>

    {{-- Plantilla para la fila de concepto --}}
    <template id="concepto-template">
        <tr>
            <td><input type="text" name="conceptos[__INDEX__][clave_prod_serv]" class="form-control" value="01010101" required></td>
            <td><input type="number" name="conceptos[__INDEX__][cantidad]" class="form-control cantidad" value="1" step="0.01" required></td>
            <td><input type="text" name="conceptos[__INDEX__][clave_unidad]" class="form-control" value="E48" required></td>
            <td><input type="text" name="conceptos[__INDEX__][descripcion]" class="form-control" placeholder="Servicio o producto" required></td>
            <td><input type="number" name="conceptos[__INDEX__][valor_unitario]" class="form-control valor-unitario" value="0.00" step="0.01" required></td>
            <td><input type="text" name="conceptos[__INDEX__][importe]" class="form-control importe" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-concepto"><i class="fa-solid fa-trash"></i></button></td>
        </tr>
    </template>

    @push('scripts')
    <script>
        @if(isset($facturacion))
            // Si es una nota de crédito, los campos de conceptos son de solo lectura
            // y el botón para añadir más se oculta.
            document.getElementById('add-concepto').style.display = 'none';
            
            // También, los totales ya están cargados, así que los mostramos.
            document.addEventListener('DOMContentLoaded', function () {
                function formatCurrency(amount) {
                    return `$${parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
                }
                document.getElementById('subtotal-display').textContent = formatCurrency({{ $facturacion->subtotal }});
                document.getElementById('impuestos-display').textContent = formatCurrency({{ $facturacion->impuestos }});
                document.getElementById('total-display').textContent = formatCurrency({{ $facturacion->total }});
            });
        @else
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search-client-input');
            const resultsContainer = document.getElementById('search-results');
            const clienteIdInput = document.getElementById('cliente_id');
            const selectedClientInfo = document.getElementById('selected-client-info');
            const selectedClientName = document.getElementById('selected-client-name');
            const submitButton = document.getElementById('submit-button');

            let searchTimeout;
            searchInput.addEventListener('keyup', function () {
                clearTimeout(searchTimeout);
                const term = this.value;
                if (term.length < 2) { resultsContainer.innerHTML = ''; return; }
                searchTimeout = setTimeout(() => {
                    fetch(`{{ route('tenant.facturacion.cfdis.search.clients') }}?term=${term}`)
                        .then(response => response.json())
                        .then(data => {
                            resultsContainer.innerHTML = '';
                            data.forEach(cliente => {
                                const item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = `${cliente.nombre_completo} (${cliente.rfc || 'Sin RFC'})`;
                                item.dataset.id = cliente.id;
                                item.addEventListener('click', function (e) {
                                    e.preventDefault();
                                    clienteIdInput.value = this.dataset.id;
                                    selectedClientName.textContent = this.textContent;
                                    selectedClientInfo.classList.remove('d-none');
                                    resultsContainer.innerHTML = '';
                                    searchInput.value = '';
                                    submitButton.disabled = false;
                                });
                                resultsContainer.appendChild(item);
                            });
                        });
                }, 300);
            });

            // Lógica para conceptos dinámicos
            const conceptosContainer = document.getElementById('conceptos-container');
            const conceptoTemplate = document.getElementById('concepto-template');
            let conceptoIndex = 0;

            document.getElementById('add-concepto').addEventListener('click', function() {
                const newRow = conceptoTemplate.innerHTML.replace(/__INDEX__/g, conceptoIndex);
                conceptosContainer.insertAdjacentHTML('beforeend', newRow);
                conceptoIndex++;
            });

            // Delegación de eventos para un manejo más eficiente
            conceptosContainer.addEventListener('click', (e) => {
                if (e.target.closest('.remove-concepto')) {
                    e.target.closest('tr').remove();
                    updateTotals();
                }
            });

            conceptosContainer.addEventListener('input', (e) => {
                if (e.target.matches('.cantidad, .valor-unitario')) {
                    const row = e.target.closest('tr');
                    const importe = (parseFloat(row.querySelector('.cantidad').value) || 0) * (parseFloat(row.querySelector('.valor-unitario').value) || 0);
                    row.querySelector('.importe').value = isNaN(importe) ? '0.00' : importe.toFixed(2);
                    updateTotals();
                }
            });

            function updateTotals() {
                let subtotal = 0;
                conceptosContainer.querySelectorAll('tr').forEach(row => {
                    subtotal += parseFloat(row.querySelector('.importe').value) || 0;
                });

                const impuestos = subtotal * 0.16; // IVA 16%
                const total = subtotal + impuestos;
                const formatCurrency = (amount) => `$${(isNaN(amount) ? 0 : amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;

                document.getElementById('subtotal-display').textContent = formatCurrency(subtotal);
                document.getElementById('impuestos-display').textContent = formatCurrency(impuestos);
                document.getElementById('total-display').textContent = formatCurrency(total);

                document.getElementById('subtotal-input').value = isNaN(subtotal) ? 0 : subtotal.toFixed(2);
                document.getElementById('impuestos-input').value = isNaN(impuestos) ? 0 : impuestos.toFixed(2);
                document.getElementById('total-input').value = isNaN(total) ? 0 : total.toFixed(2);
            }
            
            // Añadir una fila inicial
            document.getElementById('add-concepto').click();
        @endif
    </script>
    @endpush
</x-layouts.app>