import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import { initClientSearchTomSelect, loadSeriesAndFolios } from '../shared/facturacion-common.js';
import { getCatalogos } from '../../../Config/catalogLoader.js';
import { populateSelect } from '../../../Utils/tomSelectHelper.js';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('payment-form');
    if (!form) return;

    const findInvoicesBtn = document.getElementById('find-invoices-btn');
    const invoicesContainer = document.getElementById('invoices-container');
    const invoicesTableBody = document.getElementById('invoices-table-body');
    const montoTotalInput = document.getElementById('monto_total');

    const config = window.pagosConfig || {};
    const urls = config.urls || {};
    let tomSelects = {};

    function init() {
        initTomSelects();
        loadCatalogosAndSeries();
        initEventListeners();
    }

    function initTomSelects() {
        tomSelects.cliente_id = initClientSearchTomSelect('cliente_id', urls.searchClients, urls.createClientUrl);
        tomSelects.serie_folio_id = new TomSelect('#serie_folio_id', { placeholder: 'Cargando...' });
        tomSelects.forma_pago = new TomSelect('#forma_pago', { placeholder: 'Cargando...' });
        tomSelects.moneda = new TomSelect('#moneda', { placeholder: 'Cargando...' });
    }

    async function loadCatalogosAndSeries() {
        try {
            const catalogos = await getCatalogos();
            populateSelect(tomSelects.forma_pago, catalogos.formasPago, { placeholder: 'Seleccione Forma de Pago' });
            populateSelect(tomSelects.moneda, catalogos.monedas, { placeholder: 'Seleccione Moneda' });
            
            // Preseleccionar MXN en moneda si está disponible
            const mxnOption = Object.values(tomSelects.moneda.options).find(opt => opt.value === 'MXN');
            if (mxnOption) {
                tomSelects.moneda.setValue('MXN');
            }

            // Cargar series y folios para complementos de pago (Tipo P)
            await loadSeriesAndFolios(tomSelects.serie_folio_id, `${urls.series}?tipo=P`, urls.createSerieUrl);

        } catch (error) {
            console.error('Error al cargar catálogos o series:', error);
        }
    }

    function initEventListeners() {
        tomSelects.cliente_id.on('change', (value) => {
            findInvoicesBtn.disabled = !value;
            invoicesContainer.classList.add('d-none');
            invoicesTableBody.innerHTML = '';
        });

        findInvoicesBtn.addEventListener('click', handleFindInvoices);
        invoicesTableBody.addEventListener('change', handleInvoiceSelectionChange);
        invoicesTableBody.addEventListener('input', handlePaymentAmountInput);
        form.addEventListener('submit', handleFormSubmit);
    }

    async function handleFindInvoices() {
        const clienteId = tomSelects.cliente_id.getValue();
        if (!clienteId) return;

        try {
            const response = await fetch(`${urls.searchInvoices}?cliente_id=${clienteId}`);
            if (!response.ok) throw new Error('Error al buscar facturas');
            const invoices = await response.json();

            invoicesTableBody.innerHTML = '';
            if (invoices.length > 0) {
                invoices.forEach(invoice => {
                    const row = `
                        <tr>
                            <td><input type="checkbox" class="invoice-checkbox" data-invoice='${JSON.stringify(invoice)}'></td>
                            <td><span title="${invoice.uuid_fiscal}">${invoice.uuid_fiscal.substring(0, 8)}...</span></td>
                            <td>${invoice.serie}-${invoice.folio}</td>
                            <td class="text-end">${formatCurrency(invoice.saldo_pendiente)}</td>
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
    }

    function handleInvoiceSelectionChange(e) {
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
    }

    function handlePaymentAmountInput(e) {
        if (e.target.classList.contains('payment-amount')) {
            updateTotalAmount();
        }
    }

    function updateTotalAmount() {
        let total = 0;
        document.querySelectorAll('.payment-amount:not(:disabled)').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        montoTotalInput.value = total.toFixed(2);
    }

    function handleFormSubmit(e) {
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
    }
    
    const formatCurrency = (amount) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);

    init();
});