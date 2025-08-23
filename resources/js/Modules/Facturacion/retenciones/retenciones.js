import $ from 'jquery';
import 'select2';

document.addEventListener('DOMContentLoaded', function () {
    const formContainer = document.getElementById('retencion-form');
    if (!formContainer) {
        return; // No ejecutar si el formulario no está en la página
    }

    const impuestosContainer = document.getElementById('impuestos-container');
    const impuestosTableBody = document.querySelector('#impuestos-table tbody');
    const addImpuestoBtn = document.getElementById('add-impuesto-btn');
    const clienteSelect = $('#cliente_id');

    let impuestoIndex = parseInt(impuestosContainer.dataset.initialIndex, 10) || 0;

    // 1. Inicialización de Select2 para búsqueda de clientes
    if (clienteSelect.length) {
        clienteSelect.select2({
            placeholder: 'Busque un cliente por nombre o RFC',
            ajax: {
                url: clienteSelect.data('search-url'),
                dataType: 'json',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: `${item.nombre_completo || item.razon_social} (${item.rfc})`,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            }
        });
    }

    // 2. Lógica para añadir filas de impuestos
    if (addImpuestoBtn) {
        addImpuestoBtn.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="number" step="0.01" name="impuestos[${impuestoIndex}][base_ret]" class="form-control base-ret" required></td>
                <td><select name="impuestos[${impuestoIndex}][impuesto]" class="form-select" required><option value="01">01 - ISR</option><option value="02">02 - IVA</option><option value="03">03 - IEPS</option></select></td>
                <td><select name="impuestos[${impuestoIndex}][tipo_pago_ret]" class="form-select" required><option value="Pago provisional">Pago provisional</option><option value="Pago definitivo">Pago definitivo</option></select></td>
                <td><input type="number" step="0.01" name="impuestos[${impuestoIndex}][monto_ret]" class="form-control monto-ret" required></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-impuesto-btn"><i class="fa-solid fa-trash"></i></button></td>
            `;
            impuestosTableBody.appendChild(newRow);
            impuestoIndex++;
        });
    }

    // 3. Lógica para quitar filas de impuestos (usando delegación de eventos)
    if (impuestosTableBody) {
        impuestosTableBody.addEventListener('click', e => {
            if (e.target.closest('.remove-impuesto-btn')) {
                e.target.closest('tr').remove();
                calculateTotals();
            }
        });

        impuestosTableBody.addEventListener('input', e => {
            if (e.target.classList.contains('base-ret') || e.target.classList.contains('monto-ret')) {
                calculateTotals();
            }
        });
    }

    // 4. Función para calcular totales
    function calculateTotals() {
        let totalOperacion = 0;
        let totalRetenido = 0;
        document.querySelectorAll('#impuestos-table tbody tr').forEach(row => {
            const baseRetInput = row.querySelector('.base-ret');
            const montoRetInput = row.querySelector('.monto-ret');
            if (baseRetInput) totalOperacion += parseFloat(baseRetInput.value) || 0;
            if (montoRetInput) totalRetenido += parseFloat(montoRetInput.value) || 0;
        });

        const montoTotalOperacionInput = document.getElementById('monto_total_operacion');
        const montoTotalRetenidoInput = document.getElementById('monto_total_retenido');
        if (montoTotalOperacionInput) montoTotalOperacionInput.value = totalOperacion.toFixed(4);
        if (montoTotalRetenidoInput) montoTotalRetenidoInput.value = totalRetenido.toFixed(4);
    }

    // Calcular totales al cargar la página (para el modo de edición)
    calculateTotals();
});
