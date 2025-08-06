@extends('layouts.tenant')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Nuevo Complemento de Pago</div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                                <label for="serie_folio_id" class="form-label">Serie</label>
                                <select class="form-control" name="serie_folio_id" required>
                                    @foreach ($series as $serie)
                                        <option value="{{ $serie->id }}">{{ $serie->serie }}</option>
                                    @endforeach
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
        </div>
    </div>
</div>
@endsection

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
                url: '{{ route("tenant.documents.search.clients") }}', // Reutilizamos la ruta de búsqueda de clientes
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

        // Habilitar el botón de búsqueda cuando se selecciona un cliente
        clienteSelect.on('select2:select', function (e) {
            findInvoicesBtn.disabled = false;
        });

        // Buscar facturas pendientes al hacer clic en el botón
        findInvoicesBtn.addEventListener('click', async function () {
            const clienteId = clienteSelect.val();
            if (!clienteId) return;

            try {
                const response = await fetch('{{ route("tenant.facturacion.pagos.search.invoices") }}?cliente_id=' + clienteId);
                if (!response.ok) throw new Error('Error al buscar facturas');
                const invoices = await response.json();
                
                invoicesTableBody.innerHTML = ''; // Limpiar tabla
                if (invoices.length > 0) {
                    invoices.forEach(invoice => {
                        const row = `
                            <tr>
                                <td><input type="checkbox" class="invoice-checkbox" data-invoice='${JSON.stringify(invoice)}'></td>
                                <td>${invoice.uuid_fiscal.substring(0, 8)}...</td>
                                <td>${invoice.serie}-${invoice.folio}</td>
                                <td>${parseFloat(invoice.saldo_pendiente).toFixed(2)}</td>
                                <td><input type="number" class="form-control form-control-sm payment-amount" style="width: 120px;" disabled></td>
                            </tr>
                        `;
                        invoicesTableBody.insertAdjacentHTML('beforeend', row);
                    });
                    invoicesContainer.classList.remove('d-none');
                } else {
                    alert('No se encontraron facturas con saldo pendiente para este cliente.');
                    invoicesContainer.classList.add('d-none');
                }
            } catch (error) {
                console.error(error);
                alert('Ocurrió un error al buscar las facturas.');
            }
        });

        // Lógica para manejar la selección de facturas y el cálculo de montos
        invoicesTableBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('invoice-checkbox')) {
                const paymentAmountInput = e.target.closest('tr').querySelector('.payment-amount');
                paymentAmountInput.disabled = !e.target.checked;
                if (e.target.checked) {
                    const invoice = JSON.parse(e.target.dataset.invoice);
                    paymentAmountInput.value = parseFloat(invoice.saldo_pendiente).toFixed(2);
                    paymentAmountInput.max = parseFloat(invoice.saldo_pendiente).toFixed(2);
                }
                updateTotalAmount();
            }
        });

        invoicesTableBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('payment-amount')) {
                updateTotalAmount();
            }
        });

        function updateTotalAmount() {
            let total = 0;
            document.querySelectorAll('.payment-amount:not(:disabled)').forEach(input => {
                total += parseFloat(input.value) || 0;
            });
            montoTotalInput.value = total.toFixed(2);
        }

        // Antes de enviar el formulario, construir los datos de los documentos relacionados
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            document.querySelectorAll('.invoice-checkbox:checked').forEach((checkbox, index) => {
                const invoice = JSON.parse(checkbox.dataset.invoice);
                const row = checkbox.closest('tr');
                const amount = row.querySelector('.payment-amount').value;

                // Añadir campos ocultos para enviar al backend
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][id_documento]" value="${invoice.uuid_fiscal}">`);
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][serie]" value="${invoice.serie}">`);
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][folio]" value="${invoice.folio}">`);
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][moneda_dr]" value="MXN">`); // Asumir MXN por ahora
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][num_parcialidad]" value="1">`); // Simplificado, se necesitaría lógica para parcialidades
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][imp_saldo_ant]" value="${invoice.saldo_pendiente}">`);
                this.insertAdjacentHTML('beforeend', `<input type="hidden" name="doctosRelacionados[${index}][imp_pagado]" value="${amount}">`);
            });
        });
    });
</script>
@endpush
