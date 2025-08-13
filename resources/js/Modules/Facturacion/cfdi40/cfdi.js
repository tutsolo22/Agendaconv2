import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import { getCatalogos } from '../../../Config/catalogLoader.js'; // Importamos la función central
import { populateSelect } from '../../../Utils/tomSelectHelper.js'; // Importamos el helper
import '/resources/css/modules/facturacion/cfdi40/create.css';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('cfdi-form');
    if (!form) return;

    // --- ESTADO Y CONSTANTES ---
    const facturaOriginalData = form.dataset.facturaOriginal ? JSON.parse(form.dataset.facturaOriginal) : null;
    const isCreditNote = !!facturaOriginalData;
    const conceptosContainer = document.getElementById('conceptos-container');
    const conceptoTemplate = document.getElementById('concepto-template');
    const IVA_RATE = 0.16;
    let conceptoIndex = 0;
    let tomSelects = {};

    // --- INICIALIZACIÓN ---
    init();

    async function init() {
        initTomSelects();
        await loadCatalogos(); // Esperamos a que los catálogos carguen
        initConceptos();

        if (isCreditNote) {
            populateForCreditNote();
        } else {
            addConcepto(); // Añadir una fila de concepto inicial para facturas nuevas
        }
    }

    // --- LÓGICA DE TOM-SELECT Y CATÁLOGOS ---

    function initTomSelects() {
        tomSelects.cliente_id = new TomSelect('#cliente_id', {
            valueField: 'id',
            labelField: 'nombre_completo',
            searchField: ['nombre_completo', 'rfc'],
            placeholder: 'Escriba para buscar un cliente por nombre o RFC...',
            create: false,
            load: (query, callback) => {
                if (query.length < 2) return callback();
                fetch(`${window.apiUrls.searchClients}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(json => callback(json))
                    .catch(() => callback());
            },
            render: {
                option: (item, escape) => `<div><span class="fw-bold">${escape(item.nombre_completo)}</span><div class="text-muted small">${escape(item.rfc)}</div></div>`,
                item: (item, escape) => `<div>${escape(item.nombre_completo)} (${escape(item.rfc)})</div>`,
                no_results: (data, escape) => `<div class="text-muted p-2">No se encontraron resultados. <a href="${window.createClientUrl}" target="_blank">Crear nuevo cliente</a></div>`,
            },
        });

        // Inicializar otros selects que se poblarán después
        ['serie', 'forma_pago', 'metodo_pago', 'uso_cfdi'].forEach(id => {
            tomSelects[id] = new TomSelect(`#${id}`, { placeholder: 'Cargando...' });
        });
        if (isCreditNote) {
            tomSelects.relation_type = new TomSelect('#relation_type', { placeholder: 'Cargando...' });
        }
    }

    async function loadCatalogos() {
        try {
            // 1. Añadimos headers para indicar a Laravel que esperamos una respuesta JSON, incluso en caso de error.
            const fetchOptions = {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            };

            const catalogos = await getCatalogos(); // ¡Llamada única a nuestra función!
            populateSelect(tomSelects.uso_cfdi, catalogos.usosCfdi, { placeholder: 'Seleccione un Uso de CFDI' });
            populateSelect(tomSelects.metodo_pago, catalogos.metodosPago, { placeholder: 'Seleccione un Método de Pago' });
            populateSelect(tomSelects.forma_pago, catalogos.formasPago, { placeholder: 'Seleccione una Forma de Pago' });

            if (isCreditNote) {
                // Asumimos que la API ahora devuelve 'tiposRelacion'
                populateSelect(tomSelects.relation_type, catalogos.tiposRelacion, { placeholder: 'Seleccione un Tipo de Relación' });
            }

            // --- Carga de Series y Folios con manejo de errores ---
            try {
                const seriesRes = await fetch(window.apiUrls.series, fetchOptions);
                const seriesData = await seriesRes.json();

                if (!seriesRes.ok || !Array.isArray(seriesData) || seriesData.length === 0) {
                    // Si la respuesta no es OK, o no es un array, o está vacío, mostramos el aviso.
                    throw new Error('No hay series configuradas o hubo un error al cargarlas.');
                }

                // Si todo está bien, poblamos el selector.
                populateSelect(tomSelects.serie, seriesData, {
                    valueField: 'id',
                    textField: 'serie',
                    textTemplate: (item) => `Serie ${item.serie} (Folio: ${item.folio_actual})`,
                    placeholder: 'Seleccione una Serie'
                });

            } catch (error) {
                // Capturamos cualquier error (fetch, json parse, o el que lanzamos manualmente)
                console.warn('No se pudieron cargar las series:', error.message);
                tomSelects.serie.disable();
                tomSelects.serie.clear();
                tomSelects.serie.addOption({ value: '', text: 'No configurado' });

                // Se busca el contenedor .mb-3 más cercano para inyectar la alerta.
                const serieWrapper = document.getElementById('serie').closest('.mb-3');
                if (serieWrapper) {
                    const existingAlert = serieWrapper.querySelector('#serie-alert');
                    if (existingAlert) existingAlert.remove();

                    const alertHtml = `
                        <div class="alert alert-warning alert-dismissible fade show mt-2" role="alert" id="serie-alert">
                            <p class="mb-2 small">No hay series y folios configurados para facturar.</p>
                            <a href="${window.createSerieUrl}" class="btn btn-primary btn-sm w-100">Configurar ahora</a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    serieWrapper.insertAdjacentHTML('beforeend', alertHtml);
                }
            }
        } catch (error) {
            console.error('Error final al procesar catálogos:', error.message);            Object.values(tomSelects).forEach(ts => {
                ts.clear();
                ts.addOption({ value: '', text: 'Error al cargar datos' });
                ts.disable();
            });
        }
    }

    // --- LÓGICA DE CONCEPTOS ---

    function initConceptos() {
        document.getElementById('add-concepto').addEventListener('click', addConcepto);
        conceptosContainer.addEventListener('click', e => {
            if (e.target.closest('.remove-concepto')) {
                e.target.closest('.concepto-row').remove();
                calculateTotals();
            }
        });
        conceptosContainer.addEventListener('input', e => {
            if (e.target.classList.contains('concept-cantidad') || e.target.classList.contains('concept-valor-unitario')) {
                calculateTotals();
            }
        });
    }

    function addConcepto(conceptoData = null) {
        const newRowHtml = conceptoTemplate.innerHTML.replace(/\[ID\]/g, conceptoIndex);
        conceptosContainer.insertAdjacentHTML('beforeend', newRowHtml);
        const newRow = conceptosContainer.lastElementChild;
        const claveProdServSelect = newRow.querySelector('.concept-prod-serv');

        new TomSelect(claveProdServSelect, {
            valueField: 'id',
            labelField: 'texto',
            searchField: ['texto'],
            placeholder: 'Escriba para buscar producto/servicio...',
            create: false,
            load: (query, callback) => {
                if (query.length < 3) return callback();
                fetch(`${window.apiUrls.searchProductos}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(json => callback(json))
                    .catch(() => callback());
            },
            onChange: function (value) {
                const selectedOption = this.options[value];
                if (selectedOption) {
                    const descriptionInput = newRow.querySelector('.concept-descripcion');
                    const description = selectedOption.texto.substring(selectedOption.texto.indexOf(' - ') + 3);
                    descriptionInput.value = description;
                }
            }
        });

        if (conceptoData) {
            newRow.querySelector('.concept-cantidad').value = conceptoData.cantidad;
            newRow.querySelector('.concept-producto').value = conceptoData.descripcion;
            newRow.querySelector('.concept-descripcion').value = conceptoData.descripcion;
            newRow.querySelector('.concept-valor-unitario').value = conceptoData.valor_unitario;

            const ts = claveProdServSelect.tomselect;
            ts.addOption({ id: conceptoData.clave_prod_serv, text: `${conceptoData.clave_prod_serv} - ${conceptoData.descripcion}` });
            ts.setValue(conceptoData.clave_prod_serv);

            newRow.querySelectorAll('input, select:not(.concept-prod-serv)').forEach(input => input.readOnly = true);
            newRow.querySelectorAll('.remove-concepto').forEach(btn => btn.style.display = 'none');
            if (ts) {
                ts.disable();
            }
        }

        conceptoIndex++;
        calculateTotals();
    }

    // --- LÓGICA DE CÁLCULOS Y TOTALES ---
    const formatCurrency = (amount) => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(amount);

    const calculateTotals = () => {
        let subtotal = 0;
        document.querySelectorAll('.concepto-row').forEach(row => {
            const cantidad = parseFloat(row.querySelector('.concept-cantidad').value) || 0;
            const valorUnitario = parseFloat(row.querySelector('.concept-valor-unitario').value) || 0;
            const importe = cantidad * valorUnitario;
            // El input de importe es de solo lectura, así que no se envía.
            // Lo usamos solo para mostrar el valor formateado.
            row.querySelector('.concept-importe').value = formatCurrency(importe);
            subtotal += importe;
        });

        const iva = subtotal * IVA_RATE;
        const total = subtotal + iva;

        document.getElementById('display-subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('display-iva').textContent = formatCurrency(iva);
        document.getElementById('display-total').textContent = formatCurrency(total);
    };

    // --- LÓGICA ESPECÍFICA PARA NOTAS DE CRÉDITO ---
    function populateForCreditNote() {
        const cliente = facturaOriginalData.cliente;
        tomSelects.cliente_id.addOption({ id: cliente.id, nombre_completo: cliente.nombre_completo, rfc: cliente.rfc });
        tomSelects.cliente_id.setValue(cliente.id);
        tomSelects.cliente_id.disable();

        // Poblar y deshabilitar campos generales
        tomSelects.serie.setValue(facturaOriginalData.serie_folio_id);
        tomSelects.forma_pago.setValue(facturaOriginalData.forma_pago);
        tomSelects.metodo_pago.setValue(facturaOriginalData.metodo_pago);
        
        // El tipo de relación para notas de crédito es fijo.
        tomSelects.relation_type.setValue('01'); // 01 = Nota de crédito de los documentos relacionados
        tomSelects.relation_type.disable();

        // El Uso de CFDI para notas de crédito es específico y fijo.
        tomSelects.uso_cfdi.setValue(window.usoCfdiCreditNote);
        tomSelects.uso_cfdi.disable();

        // Conceptos
        facturaOriginalData.conceptos.forEach(concepto => {
            addConcepto(concepto);
        });

        // Ocultar el botón de "Agregar Concepto"
        document.getElementById('add-concepto').style.display = 'none';

        // Finalmente, calcular los totales con los datos cargados.
        calculateTotals();
    }

});