/**
 * Módulo de ayuda con funciones para Tom-Select.
 */

/**
 * Rellena una instancia de TomSelect con datos de un catálogo.
 *
 * @param {object} tomSelectInstance - La instancia de TomSelect a poblar.
 * @param {Array} data - El array de datos para las opciones.
 * @param {object} [options={}] - Opciones de configuración.
 * @param {string} [options.valueField='id'] - El campo a usar como valor de la opción.
 * @param {string} [options.textField='texto'] - El campo a usar como texto de la opción.
 * @param {string} [options.placeholder='Seleccione una opción'] - El texto del placeholder.
 * @param {function} [options.textTemplate] - Una función para formatear el texto de la opción.
 */
export function populateSelect(tomSelectInstance, data, options = {}) {
    const config = {
        valueField: 'id',
        textField: 'texto',
        placeholder: 'Seleccione una opción',
        ...options
    };

    const defaultTemplate = (item) => `${item[config.valueField]} - ${item[config.textField]}`;
    const textTemplate = config.textTemplate || defaultTemplate;

    tomSelectInstance.clear();
    tomSelectInstance.clearOptions();
    tomSelectInstance.addOption({ value: '', text: config.placeholder });

    if (Array.isArray(data)) {
        data.forEach(item => tomSelectInstance.addOption({ value: item[config.valueField], text: textTemplate(item) }));
    }

    tomSelectInstance.enable();
}