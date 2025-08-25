
import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import { initClientSearchTomSelect, loadSeriesAndFolios } from '../shared/facturacion-common.js';
import { getCatalogos } from '../../../Config/catalogLoader.js';
import { populateSelect } from '../../../Utils/tomSelectHelper.js';

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('retencion-form');
    if (!form) return;

    // Set window.apiUrls for catalogLoader.js
    window.apiUrls = window.apiUrls || {};
    window.apiUrls.catalogos = form.dataset.catalogosUrl;

    const config = {
        urls: {
            searchClients: form.dataset.searchClientsUrl,
            createClientUrl: form.dataset.createClientUrl, // Assuming you'll add this data attribute
            series: form.dataset.seriesUrl,
            createSerieUrl: form.dataset.createSerieUrl,
        }
    };

    let tomSelects = {};

    function init() {
        initTomSelects();
        loadCatalogosAndSeries();
        initEventListeners();
    }

    function initTomSelects() {
        tomSelects.cliente_id = initClientSearchTomSelect('cliente_id', config.urls.searchClients, config.urls.createClientUrl);
        tomSelects.serie_folio_id = new TomSelect('#serie_folio_id', { placeholder: 'Cargando...' });
        tomSelects.cve_retenc = new TomSelect('#cve_retenc', { placeholder: 'Cargando...' });
    }

    async function loadCatalogosAndSeries() {
        try {
            const catalogos = await getCatalogos();
            populateSelect(tomSelects.cve_retenc, catalogos.retenciones, { 
                placeholder: 'Seleccione una clave',
                valueField: 'id',
                textField: 'texto'
            });

            // Load series and folios for retentions (Tipo R)
            await loadSeriesAndFolios(tomSelects.serie_folio_id, `${config.urls.series}?tipo=R`, config.urls.createSerieUrl);

        } catch (error) {
            console.error('Error al cargar catálogos o series:', error);
        }
    }

    function initEventListeners() {
        tomSelects.cliente_id.on('change', (value) => {
            // Logic when client changes, for example, clear related fields
            tomSelects.cve_retenc.clear();
        });

        // Add other event listeners as needed for the form
    }

    init();
});
