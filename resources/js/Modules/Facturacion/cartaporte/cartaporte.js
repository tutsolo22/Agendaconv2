import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-cartaporte-form');
    if (!form) return;

    let mercanciaIndex = 0;

    // --- TomSelect Initializer ---
    function initializeTomSelect(element, url, options = {}) {
        if (!element) return;
        const defaultOptions = {
            valueField: 'id',
            labelField: 'texto',
            searchField: ['id', 'texto'],
            create: false,
            placeholder: 'Escriba para buscar...',
            load: function(query, callback) {
                if (!query.length) return callback();
                const fullUrl = `${window.apiUrls.satCatalogos}/${url}?q=${encodeURIComponent(query)}`;
                fetch(fullUrl)
                    .then(response => response.json())
                    .then(json => callback(json))
                    .catch(() => callback());
            }
        };
        return new TomSelect(element, { ...defaultOptions, ...options });
    }

    // --- Initialize Static Selects ---
    initializeTomSelect(document.getElementById('origen_colonia'), 'coloniasCcp31');
    initializeTomSelect(document.getElementById('destino_colonia'), 'coloniasCcp31');
    initializeTomSelect(document.getElementById('autotransporte_perm_sct'), 'tiposPermisoCcp31');
    initializeTomSelect(document.getElementById('autotransporte_config_vehicular'), 'configuracionesAutotransporteCcp31');
    initializeTomSelect(document.getElementById('figura_transporte_tipo_figura'), 'figurasTransporteCcp31');

    // --- Dynamic Mercancias Table ---
    document.getElementById('add-mercancia').addEventListener('click', function () {
        const tableBody = document.getElementById('mercancias-table').getElementsByTagName('tbody')[0];
        const newRow = tableBody.insertRow();
        newRow.dataset.index = mercanciaIndex;

        newRow.innerHTML = `
            <td><select name="mercancias[${mercanciaIndex}][bienes_transp]"></select></td>
            <td><input type="text" class="form-control form-control-sm" name="mercancias[${mercanciaIndex}][descripcion]"></td>
            <td><input type="number" class="form-control form-control-sm" name="mercancias[${mercanciaIndex}][cantidad]" min="0.01" step="0.01"></td>
            <td><select name="mercancias[${mercanciaIndex}][clave_unidad]"></select></td>
            <td><input type="number" class="form-control form-control-sm" name="mercancias[${mercanciaIndex}][peso_en_kg]" min="0.001" step="0.001"></td>
            <td><button type="button" class="btn btn-danger btn-sm remove-mercancia"><i class="fa-solid fa-trash"></i></button></td>
        `;

        initializeTomSelect(newRow.querySelector(`select[name="mercancias[${mercanciaIndex}][bienes_transp]"]`), 'productosServiciosCcp31');
        initializeTomSelect(newRow.querySelector(`select[name="mercancias[${mercanciaIndex}][clave_unidad]"]`), 'clavesUnidadesCcp31');

        mercanciaIndex++;
    });

    // Remove Mercancia Row
    document.getElementById('mercancias-table').addEventListener('click', function (e) {
        if (e.target && e.target.closest('.remove-mercancia')) {
            e.target.closest('tr').remove();
        }
    });

    // --- Draft Saving Logic ---
    const saveDraftBtn = document.getElementById('save-draft-btn');
    if(saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            const mainForm = document.getElementById('create-cartaporte-form');
            const draftForm = document.getElementById('draft-form');
            if (!mainForm || !draftForm) return;

            draftForm.innerHTML = ''; // Clear previous fields
            
            // Clone CSRF token
            const csrfToken = mainForm.querySelector('input[name="_token"]');
            if (csrfToken) {
                draftForm.appendChild(csrfToken.cloneNode(true));
            }

            // Clone all form inputs
            const inputs = mainForm.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                if(input.name && input.name !== '_token') {
                    // Create a new hidden input to carry the value
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = input.name;
                    hiddenInput.value = input.value;
                    draftForm.appendChild(hiddenInput);
                }
            });
            draftForm.submit();
        });
    }
});