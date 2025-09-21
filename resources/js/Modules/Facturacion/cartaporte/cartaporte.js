import TomSelect from 'tom-select';

document.addEventListener('DOMContentLoaded', function () {

    const { apiUrls, oldData, errors } = window.cartaPorteData;

    /**
     * =========================================================================
     * UTILITIES
     * =========================================================================
     */

    const createRemoteTomSelect = (selector, catalogName, customOptions = {}) => {
        const url = apiUrls.searchableCatalogos + catalogName + '?q=';
        return new TomSelect(selector, {
            valueField: 'id',
            labelField: 'descripcion',
            searchField: ['id', 'descripcion'],
            load: function (query, callback) {
                fetch(url + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(json => {
                        callback(json);
                    }).catch(() => {
                        callback();
                    });
            },
            render: {
                option: function (data, escape) {
                    return `<div>${escape(data.id)} - ${escape(data.descripcion)}</div>`;
                },
                item: function (item, escape) {
                    return `<div>${escape(item.id)} - ${escape(item.descripcion)}</div>`;
                }
            },
            ...customOptions
        });
    };

    const createLocalTomSelect = (selector, options, customConfig = {}) => {
        return new TomSelect(selector, {
            valueField: 'id',
            labelField: 'descripcion',
            searchField: 'descripcion',
            options: options,
            ...customConfig
        });
    };

    const applyError = (element, index, fieldName) => {
        const errorKey = `mercancias.${index}.${fieldName}`;
        if (errors[errorKey]) {
            element.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[errorKey][0];
            // Insert after the parent of the select (TomSelect replaces the original select)
            if (element.tomselect) {
                 element.tomselect.wrapper.parentNode.appendChild(errorDiv);
            } else {
                 element.parentNode.appendChild(errorDiv);
            }
        }
    };

    /**
     * =========================================================================
     * CÓDIGO POSTAL LOGIC
     * =========================================================================
     */

    const setupCodigoPostalListener = (cpInputId, estadoInputId, municipioInputId, localidadInputId, coloniaSelectId) => {
        const cpInput = document.getElementById(cpInputId);
        if (!cpInput) return;

        const coloniaSelect = new TomSelect(`#${coloniaSelectId}`, { create: false });

        cpInput.addEventListener('input', function () {
            const cp = this.value;
            if (cp.length === 5) {
                fetch(apiUrls.codigoPostalInfo + cp)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }
                        document.getElementById(estadoInputId).value = data.estado || '';
                        document.getElementById(municipioInputId).value = data.municipio || '';
                        document.getElementById(localidadInputId).value = data.localidad || '';

                        coloniaSelect.clear();
                        coloniaSelect.clearOptions();
                        if (data.colonias) {
                            const coloniasOptions = Object.entries(data.colonias).map(([id, desc]) => ({ id: id, descripcion: desc }));
                            coloniaSelect.addOptions(coloniasOptions);
                        }
                    })
                    .catch(error => console.error('Error fetching CP info:', error));
            }
        });
    };

    setupCodigoPostalListener('origen_codigo_postal', 'origen_estado', 'origen_municipio', 'origen_localidad', 'origen_colonia');
    setupCodigoPostalListener('destino_codigo_postal', 'destino_estado', 'destino_municipio', 'destino_localidad', 'destino_colonia');

    if (document.getElementById('origen_colonia')) {
        new TomSelect('#origen_colonia', { create: false });
    }

    /**
     * =========================================================================
     * CARGA DE CATÁLOGOS ESTÁTICOS
     * =========================================================================
     */

    fetch(apiUrls.cartaPorteCatalogos)
        .then(response => response.json())
        .then(data => {
            if (data.tiposPermiso) createLocalTomSelect('#autotransporte_perm_sct', data.tiposPermiso);
            if (data.configuracionesAutotransporte) createLocalTomSelect('#autotransporte_config_vehicular', data.configuracionesAutotransporte);
            if (data.figurasTransporte) createLocalTomSelect('#figura_transporte_tipo_figura', data.figurasTransporte);
        })
        .catch(error => console.error('Error fetching static catalogs:', error));

    /**
     * =========================================================================
     * MANEJO DE MERCANCÍAS
     * =========================================================================
     */
    let mercanciaIndex = 0;
    const mercanciasTableBody = document.querySelector('#mercancias-table tbody');

    const addMercanciaRow = (data = {}, index = -1) => {
        const currentIndex = index === -1 ? mercanciaIndex : index;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select id="mercancia_bienes_${currentIndex}" name="mercancias[${currentIndex}][bienes_transp]"></select>
            </td>
            <td><input type="text" class="form-control" name="mercancias[${currentIndex}][descripcion]" value="${data.descripcion || ''}"></td>
            <td><input type="number" class="form-control" name="mercancias[${currentIndex}][cantidad]" value="${data.cantidad || ''}" step="any"></td>
            <td>
                <select id="mercancia_clave_unidad_${currentIndex}" name="mercancias[${currentIndex}][clave_unidad]"></select>
            </td>
            <td><input type="number" class="form-control" name="mercancias[${currentIndex}][peso_kg]" value="${data.peso_kg || ''}" step="any"></td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-mercancia"><i class="fa-solid fa-trash"></i></button>
            </td>
        `;
        mercanciasTableBody.appendChild(newRow);

        // --- Inicializar TomSelect y cargar valores --- 
        const bienesSelectEl = newRow.querySelector(`#mercancia_bienes_${currentIndex}`);
        const claveUnidadSelectEl = newRow.querySelector(`#mercancia_clave_unidad_${currentIndex}`);

        const bienesSelect = createRemoteTomSelect(bienesSelectEl, 'productosServicios');
        const claveUnidadSelect = createRemoteTomSelect(claveUnidadSelectEl, 'clavesUnidad');

        if (data.bienes_transp) {
            bienesSelect.load(function(callback) {
                fetch(`${apiUrls.searchableCatalogos}productosServicios?q=${data.bienes_transp}`)
                    .then(res => res.json()).then(json => {
                        callback(json);
                        bienesSelect.setValue(data.bienes_transp, 'silent');
                    });
            });
        }
        if (data.clave_unidad) {
             claveUnidadSelect.load(function(callback) {
                fetch(`${apiUrls.searchableCatalogos}clavesUnidad?q=${data.clave_unidad}`)
                    .then(res => res.json()).then(json => {
                        callback(json);
                        claveUnidadSelect.setValue(data.clave_unidad, 'silent');
                    });
            });
        }

        bienesSelect.on('item_add', function(value, $item) {
            const selectedData = this.options[value];
            if (selectedData) {
                newRow.querySelector(`input[name="mercancias[${currentIndex}][descripcion]"]`).value = selectedData.descripcion;
            }
        });

        // --- Aplicar Errores de Validación ---
        applyError(bienesSelectEl, currentIndex, 'bienes_transp');
        applyError(newRow.querySelector(`input[name="mercancias[${currentIndex}][descripcion]"]`), currentIndex, 'descripcion');
        applyError(newRow.querySelector(`input[name="mercancias[${currentIndex}][cantidad]"]`), currentIndex, 'cantidad');
        applyError(claveUnidadSelectEl, currentIndex, 'clave_unidad');
        applyError(newRow.querySelector(`input[name="mercancias[${currentIndex}][peso_kg]"]`), currentIndex, 'peso_kg');

        if (index === -1) {
            mercanciaIndex++;
        }
    };

    // --- Lógica de Inicialización ---
    document.getElementById('add-mercancia').addEventListener('click', () => addMercanciaRow());

    mercanciasTableBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-mercancia')) {
            e.target.closest('tr').remove();
        }
    });

    if (oldData && oldData.length > 0) {
        oldData.forEach((mercancia, index) => {
            addMercanciaRow(mercancia, index);
        });
        mercanciaIndex = oldData.length;
    }

    /**
     * =========================================================================
     * SAVE DRAFT LOGIC
     * =========================================================================
     */
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const mainForm = document.getElementById('create-cartaporte-form');
    const draftForm = document.getElementById('draft-form');

    if (saveDraftBtn && mainForm && draftForm) {
        saveDraftBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Limpiar el formulario de borrador
            draftForm.innerHTML = '';
            draftForm.appendChild(mainForm.querySelector('input[name="_token"]').cloneNode(true));

            // Clonar todos los campos del formulario principal al de borrador
            const formData = new FormData(mainForm);
            for (let [key, value] of formData.entries()) {
                // No clonar el token de nuevo, ya lo hemos añadido
                if (key === '_token') continue;

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                draftForm.appendChild(input);
            }
            
            draftForm.submit();
        });
    }
});
