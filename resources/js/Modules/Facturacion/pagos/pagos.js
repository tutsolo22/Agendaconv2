document.addEventListener('DOMContentLoaded', function () {
    // Verificar si estamos en la página correcta
    const form = document.getElementById('payment-form');
    if (!form) return;

    // Selectores de elementos del DOM
    const clienteSelect = $('#cliente_id');
    const findInvoicesBtn = document.getElementById('find-invoices-btn');
    const invoicesContainer = document.getElementById('invoices-container');
    const invoicesTableBody = document.getElementById('invoices-table-body');
    const montoTotalInput = document.getElementById('monto_total');

    // Obtener configuración desde la vista Blade
    const config = window.pagosConfig || {};
    const urls = config.urls || {};

    // Inicializar Select2 para clientes
    clienteSelect.select2({
        placeholder: 'Selecciona un cliente',
        ajax: {
            url: urls.searchClients,
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

    // --- LÓGICA DE EVENTOS ---

    // Habilitar el botón de búsqueda si ya hay un cliente seleccionado (modo edición)
    if (clienteSelect.val()) {
        findInvoicesBtn.disabled = false;
    }

    // Habilitar botón y limpiar facturas si se cambia de cliente
    clienteSelect.on('select2:select', function (e) {
        findInvoicesBtn.disabled = false;
        invoicesContainer.classList.add('d-none');
        invoicesTableBody.innerHTML = '';
    });

    // Buscar facturas pendientes al hacer clic en el botón
    findInvoicesBtn.addEventListener('click', async function () {
        const clienteId = clienteSelect.val();
        if (!clienteId) return;

        try {
            const response = await fetch(`${urls.searchInvoices}?cliente_id=${clienteId}`);
            if (!response.ok) throw new Error('Error al buscar facturas');
            const invoices = await response.json();

            invoicesTableBody.innerHTML = ''; // Limpiar tabla
            if (invoices.length > 0) {
                invoices.forEach(invoice => {
                    const row = `
                        <tr>
                            <td><input type="checkbox" class="invoice-checkbox" data-invoice='${JSON.stringify(invoice)}'></td>
                            <td><span title="${invoice.uuid_fiscal}">${invoice.uuid_fiscal.substring(0, 8)}...</span></td>
                            <td>${invoice.serie}-${invoice.folio}</td>
                            <td class="text-end">$${parseFloat(invoice.saldo_pendiente).toFixed(2)}</td>
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

    // Manejar la selección de facturas y el cálculo de montos
    invoicesTableBody.addEventListener('change', function(e) {
        if (e.target.classList.contains('invoice-checkbox')) {
            const paymentAmountInput = e.target.closest('tr').querySelector('.payment-amount');
            paymentAmountInput.disabled = !e.target.checked;
            if (e.target.checked) {
                const invoice = JSON.parse(e.target.dataset.invoice);
                paymentAmountInput.value = parseFloat(invoice.saldo_pendiente).toFixed(2);
                paymentAmountInput.max = parseFloat(invoice.saldo_pendiente).toFixed(2);
            } else {
                paymentAmountInput.value = '';
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
        // En modo edición, sumamos el monto existente al nuevo total
        const initialAmount = config.isNewRecord ? 0 : parseFloat(config.initialAmount || 0);
        montoTotalInput.value = (initialAmount + total).toFixed(2);
    }

    // Antes de enviar el formulario, construir los datos de los documentos relacionados
    form.addEventListener('submit', function(e) {
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
