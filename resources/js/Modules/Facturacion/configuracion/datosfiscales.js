import TomSelect from 'tom-select';
import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
import { getCatalogos } from '../../../Config/catalogLoader.js'; // Importamos la función central
import { populateSelect } from '../../../Utils/tomSelectHelper.js'; // Importamos el helper

document.addEventListener('DOMContentLoaded', () => {
    console.log('[DF] Script cargado.'); // Log inicial para confirmar que el archivo se ejecuta.

    // Selector robusto que busca el formulario por su ID único.
    const form = document.getElementById('datos-fiscales-form');
    if (!form) return;

    const isNewRecord = window.currentData.isNewRecord;

    // --- SELECTORES DE ELEMENTOS ---
    const razonSocialInput = document.getElementById('razon_social');
    const rfcInput = document.getElementById('rfc');
    const regimenSelectEl = document.getElementById('regimen_fiscal_clave');
    let tomSelects = {};

    // --- INICIALIZACIÓN ---
    init();

    async function init() {
        initTomSelect();
        await loadAndPopulateRegimenes();
        initEventListeners();
    }

    // --- LÓGICA DE TOM-SELECT Y CATÁLOGOS ---

    function initTomSelect() {
        tomSelects.regimen_fiscal_clave = new TomSelect(regimenSelectEl, {
            placeholder: 'Seleccione un régimen fiscal...',
            create: false,
        });
    }

    async function loadAndPopulateRegimenes() {
        const tomSelect = tomSelects.regimen_fiscal_clave;
        console.log('[DF] 1. Iniciando carga de regímenes.');
        try {
            const catalogos = await getCatalogos(); // <-- ESTA LÍNEA ES CRÍTICA Y FALTABA
            console.log('[DF] 2. Catálogos recibidos:', catalogos);

            // 1. Poblamos el selector con los datos de la API.
            populateSelect(tomSelects.regimen_fiscal_clave, catalogos.regimenesFiscales, {
                placeholder: 'Seleccione un régimen fiscal...'
            });

            // 2. (CRÍTICO) Establecemos el valor que ya está guardado en la base de datos.
            const currentRegimen = window.currentData.regimenFiscal;
            if (currentRegimen) {
                console.log(`[DF] 3. Estableciendo valor de edición: ${currentRegimen}`);
                tomSelect.setValue(currentRegimen);
            } else {
                console.log('[DF] 3. Modo creación, dejando el campo vacío.');
                tomSelect.setValue(''); // Si no hay valor, nos aseguramos de que esté vacío.
            }
        } catch (error) {
            console.error('[DF] - ERROR FATAL durante la carga:', error);
            tomSelect.clear(); // Usamos la variable tomSelect definida al inicio de la función.
            tomSelect.addOption({ value: '', text: 'Error al cargar datos' });
            tomSelect.disable();
        }
    }

    // --- LÓGICA DE EVENTOS Y VALIDACIÓN ---

    function initEventListeners() {
        // Forzar mayúsculas
        razonSocialInput.addEventListener('input', (e) => e.target.value = e.target.value.toUpperCase());
        rfcInput.addEventListener('input', (e) => e.target.value = e.target.value.toUpperCase());

        // Validación en el envío del formulario
        form.addEventListener('submit', (e) => {
            if (!validateForm()) {
                e.preventDefault(); // Detener el envío si la validación falla.

                // Enfocar en el primer campo con error para mejorar la UX.
                const firstInvalidElement = form.querySelector('.is-invalid');
                if (firstInvalidElement) {
                    firstInvalidElement.focus();
                    firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    function validateForm() {
        let isValid = true;
        const inputsToValidate = form.querySelectorAll('input[required], select[required]');
        const fileCer = document.getElementById('archivo_cer');
        const fileKey = document.getElementById('archivo_key');

        // Limpiar validaciones previas
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

        inputsToValidate.forEach(input => {
            input.classList.remove('is-invalid');
            if (input.tomselect) input.tomselect.wrapper.classList.remove('is-invalid');

            if (!input.value.trim()) {
                isValid = false;
                input.classList.add('is-invalid');
                if (input.tomselect) input.tomselect.wrapper.classList.add('is-invalid');
            }
        });

        // Validar archivos solo si es un registro nuevo
        if (isNewRecord) {
            if (!fileCer.value) {
                isValid = false;
                fileCer.classList.add('is-invalid');
            }
            if (!fileKey.value) {
                isValid = false;
                fileKey.classList.add('is-invalid');
            }
        }

        return isValid;
    }
});
