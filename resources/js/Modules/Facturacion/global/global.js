document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('global-invoice-form');
    if (!form) return; // Salir si no estamos en la página correcta

    // --- Elementos del DOM ---
    const seriesSelect = document.getElementById('serie_folio_id');
    const addConceptoBtn = document.getElementById('add-concepto');
    const container = document.getElementById('conceptos-container');
    const noConceptosMsg = document.getElementById('no-conceptos');
    const submitButton = document.getElementById('submit-button');

    // Elementos de totales
    const totalSubtotalDisplay = document.getElementById('total-subtotal');
    const totalIvaDisplay = document.getElementById('total-iva');
    const totalDisplay = document.getElementById('total-facturar');

    const IVA_RATE = 0.16;
    let conceptoIndex = 0;

    // --- Lógica de Alerta para Series y Folios ---
    if (seriesSelect && seriesSelect.options.length === 0) {
        seriesSelect.disabled = true;
        const alertContainer = document.getElementById('serie-alert-container');
        const placeholder = document.createElement('option');
        placeholder.value = "";
        placeholder.textContent = "No hay series configuradas";
        seriesSelect.appendChild(placeholder);

        const alertHtml = `
            <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert">
                <p class="mb-2 small">No hay series y folios configurados para Facturas Globales.</p>
                <a href="${window.createSerieUrl}" class="btn btn-primary btn-sm w-100">Configurar ahora</a>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        if (alertContainer) {
            alertContainer.innerHTML = alertHtml;
        }
    }

    // --- Lógica para agregar y eliminar conceptos ---
    addConceptoBtn.addEventListener('click', () => {
        const today = new Date().toISOString().split('T')[0];
        const newRow = `
            <tr class="concepto-row">
                <td><input type="text" name="conceptos[${conceptoIndex}][folio]" class="form-control form-control-sm" required></td>
                <td><input type="text" name="conceptos[${conceptoIndex}][descripcion]" class="form-control form-control-sm" value="Venta" required></td>
                <td><input type="date" name="conceptos[${conceptoIndex}][fecha]" class="form-control form-control-sm" value="${today}" required></td>
                <td><input type="number" name="conceptos[${conceptoIndex}][monto]" class="form-control form-control-sm text-end monto-base" step="0.01" min="0" value="0.00" required></td>
                <td class="text-center align-middle">
                    <div class="form-check form-switch d-inline-block">
                         <input class="form-check-input agregar-iva" type="checkbox" name="conceptos[${conceptoIndex}][agregar_iva]">
                    </div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-concepto">
                        <i class="fa-solid fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        container.insertAdjacentHTML('beforeend', newRow);
        conceptoIndex++;
        updateUI();
    });

    container.addEventListener('click', (e) => {
        if (e.target.closest('.remove-concepto')) {
            e.target.closest('.concepto-row').remove();
            updateUI();
        }
    });

    container.addEventListener('input', (e) => {
        if (e.target.classList.contains('monto-base') || e.target.classList.contains('agregar-iva')) {
            updateUI();
        }
    });

    function updateUI() {
        calculateTotals();
        toggleNoConceptosMessage();
        toggleSubmitButton();
    }

    function calculateTotals() {
        let granSubtotal = 0;
        let granIva = 0;
        let granTotal = 0;

        container.querySelectorAll('.concepto-row').forEach(row => {
            const montoBaseInput = row.querySelector('.monto-base');
            const agregarIvaCheckbox = row.querySelector('.agregar-iva');

            const montoBase = parseFloat(montoBaseInput.value) || 0;
            const agregarIva = agregarIvaCheckbox.checked;

            let subtotal, iva, total;

            if (agregarIva) {
                // El monto base es el subtotal, hay que AGREGAR el IVA
                subtotal = montoBase;
                iva = subtotal * IVA_RATE;
                total = subtotal + iva;
            } else {
                // El monto base es el total, hay que DESGLOSAR el IVA
                total = montoBase;
                subtotal = total / (1 + IVA_RATE);
                iva = total - subtotal;
            }

            granSubtotal += subtotal;
            granIva += iva;
            granTotal += total;
        });

        totalSubtotalDisplay.textContent = formatCurrency(granSubtotal);
        totalIvaDisplay.textContent = formatCurrency(granIva);
        totalDisplay.textContent = formatCurrency(granTotal);
    }

    function toggleNoConceptosMessage() {
        const hasRows = container.querySelectorAll('.concepto-row').length > 0;
        noConceptosMsg.style.display = hasRows ? 'none' : 'block';
    }

    function toggleSubmitButton() {
        const hasRows = container.querySelectorAll('.concepto-row').length > 0;
        const hasSeries = seriesSelect && seriesSelect.options.length > 0 && seriesSelect.value !== "";
        submitButton.disabled = !(hasRows && hasSeries);
    }

    function formatCurrency(amount) {
        return `$${amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}`;
    }

    // Inicializar la UI al cargar la página
    updateUI();

    // Agregar listener al select de series para habilitar/deshabilitar el botón
    if (seriesSelect) {
        seriesSelect.addEventListener('change', toggleSubmitButton);
    }
});
