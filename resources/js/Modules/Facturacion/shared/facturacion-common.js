// c:/xampp/htdocs/Agendaconv2/resources/js/Modules/Facturacion/shared/facturacion-common.js

import TomSelect from 'tom-select'; // Keep TomSelect import for now, will abstract later if needed
import 'tom-select/dist/css/tom-select.bootstrap5.min.css'; // Keep CSS import for now
import { populateSelect } from '../../Utils/tomSelectHelper.js'; // Importamos el helper

/**
 * Initializes a TomSelect instance for client search.
 * @param {string} selectId - The ID of the select element.
 * @param {string} searchUrl - The URL for searching clients.
 * @param {string} createClientUrl - The URL for creating a new client.
 * @returns {TomSelect} The initialized TomSelect instance.
 */
export function initClientSearchTomSelect(selectId, searchUrl, createClientUrl) {
    return new TomSelect(`#${selectId}`, {
        valueField: 'id',
        labelField: 'nombre_completo',
        searchField: ['nombre_completo', 'rfc'],
        placeholder: 'Escriba para buscar un cliente por nombre o RFC...',
        create: false,
        load: (query, callback) => {
            if (query.length < 2) return callback();
            fetch(`${searchUrl}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(json => callback(json))
                .catch(() => callback());
        },
        render: {
            option: (item, escape) => `<div><span class="fw-bold">${escape(item.nombre_completo)}</span><div class="text-muted small">${escape(item.rfc)}</div></div>`,
            item: (item, escape) => `<div>${escape(item.nombre_completo)} (${escape(item.rfc)})</div>`,
            no_results: (data, escape) => `<div class="text-muted p-2">No se encontraron resultados. <a href="${createClientUrl}" target="_blank">Crear nuevo cliente</a></div>`,
        },
    });
}

/**
 * Initializes a Select2 instance for client search.
 * Assumes jQuery and Select2 are already loaded.
 * @param {jQuery} selectElement - The jQuery wrapped select element.
 * @param {string} searchUrl - The URL for searching clients.
 */
export function initClientSearchSelect2(selectElement, searchUrl) {
    selectElement.select2({
        placeholder: 'Selecciona un cliente',
        ajax: {
            url: searchUrl,
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
}

/**
 * Loads series and folios and populates a TomSelect instance.
 * @param {TomSelect} tomSelectInstance - The TomSelect instance for series.
 * @param {string} seriesUrl - The URL for fetching series and folios.
 * @param {string} createSerieUrl - The URL for creating a new series.
 */
export async function loadSeriesAndFolios(tomSelectInstance, seriesUrl, createSerieUrl) {
    try {
        const fetchOptions = {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        };

        const seriesRes = await fetch(seriesUrl, fetchOptions);
        const seriesData = await seriesRes.json();

        if (!seriesRes.ok || !Array.isArray(seriesData) || seriesData.length === 0) {
            throw new Error('No hay series configuradas o hubo un error al cargarlas.');
        }

        populateSelect(tomSelectInstance, seriesData, {
            valueField: 'id',
            textField: 'serie',
            textTemplate: (item) => `Serie ${item.serie} (Folio: ${item.folio_actual})`,
            placeholder: 'Seleccione una Serie'
        });

    } catch (error) {
        console.warn('No se pudieron cargar las series:', error.message);
        tomSelectInstance.disable();
        tomSelectInstance.clear();
        tomSelectInstance.addOption({ value: '', text: 'No configurado' });

        const serieWrapper = document.getElementById(tomSelectInstance.settings.id).closest('.mb-3');
        if (serieWrapper) {
            const existingAlert = serieWrapper.querySelector('#serie-alert');
            if (existingAlert) existingAlert.remove();

            const alertHtml = `
                <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert" id="serie-alert">
                    <p class="mb-2 small">No hay series y folios configurados para facturar.</p>
                    <a href="${createSerieUrl}" class="btn btn-primary btn-sm w-100">Configurar ahora</a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            serieWrapper.insertAdjacentHTML('beforeend', alertHtml);
        }
    }
}

// Helper function (assuming it's available globally or imported from somewhere else)
// For now, I'll include a placeholder for populateSelect, as it's used in loadSeriesAndFolios
// In a real scenario, this would be imported from '../../../Utils/tomSelectHelper.js'
// function populateSelect(selectInstance, data, options = {}) {
//     selectInstance.clearOptions();
//     data.forEach(item => {
//         const value = options.valueField ? item[options.valueField] : item.value;
//         const text = options.textField ? item[options.textField] : item.text;
//         const optionText = options.textTemplate ? options.textTemplate(item) : text;
//         selectInstance.addOption({ value: value, text: optionText });
//     });
//     if (options.placeholder) {
//         selectInstance.settings.placeholder = options.placeholder;
//     }
//     selectInstance.enable();
// }
