/**
 * HexaFacClient.js
 * 
 * SDK de JavaScript para interactuar con la API de HexaFac.
 * Proporciona métodos para consumir fácilmente los endpoints de facturación,
 * clientes, y otros recursos de la API.
 *
 * @version 1.0.0
 * @author Gemini
 */

class HexaFacClient {
    /**
     * @param {string} apiKey - La clave API para autenticación.
     * @param {string} baseUrl - La URL base de la API de HexaFac.
     */
    constructor(apiKey, baseUrl = '/api/hexafac/v1') {
        if (!apiKey) {
            throw new Error('La API Key es requerida para inicializar el cliente de HexaFac.');
        }
        this.apiKey = apiKey;
        this.baseUrl = baseUrl;
        this.headers = {
            'Authorization': `Bearer ${this.apiKey}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        };
    }

    /**
     * Realiza una petición a la API.
     * @param {string} endpoint - El endpoint al que se llamará.
     * @param {string} method - El método HTTP (GET, POST, PUT, DELETE).
     * @param {object} [body=null] - El cuerpo de la petición para métodos POST/PUT.
     * @returns {Promise<object>} - La respuesta de la API en formato JSON.
     * @private
     */
    async _request(endpoint, method, body = null) {
        const url = `${this.baseUrl}${endpoint}`;
        const options = {
            method,
            headers: this.headers,
        };

        if (body) {
            options.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(url, options);
            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(`Error en la petición a la API: ${response.status} ${response.statusText} - ${errorData.message || ''}`);
            }
            return await response.json();
        } catch (error) {
            console.error('HexaFacClient Error:', error);
            throw error;
        }
    }

    // --- Métodos de la API ---

    /**
     * Crea una nueva factura.
     * @param {object} facturaData - Los datos de la factura a crear.
     * @returns {Promise<object>} - La respuesta de la API.
     */
    crearFactura(facturaData) {
        return this._request('/facturas', 'POST', facturaData);
    }

    /**
     * Obtiene los detalles de una factura por su UUID.
     * @param {string} uuid - El UUID de la factura.
     * @returns {Promise<object>} - La respuesta de la API.
     */
    obtenerFactura(uuid) {
        return this._request(`/facturas/${uuid}`, 'GET');
    }

    /**
     * Cancela una factura.
     * @param {string} uuid - El UUID de la factura a cancelar.
     * @param {object} cancelacionData - Los datos para la cancelación (ej. motivo).
     * @returns {Promise<object>} - La respuesta de la API.
     */
    cancelarFactura(uuid, cancelacionData) {
        return this._request(`/facturas/${uuid}/cancelar`, 'POST', cancelacionData);
    }

    /**
     * Crea un nuevo cliente.
     * @param {object} clienteData - Los datos del cliente a crear.
     * @returns {Promise<object>} - La respuesta de la API.
     */
    crearCliente(clienteData) {
        return this._request('/clientes', 'POST', clienteData);
    }
}

// Ejemplo de uso (esto se podría poner en otro archivo que importe la clase)
/*
document.addEventListener('DOMContentLoaded', () => {
    const apiKey = 'TU_API_KEY_AQUI'; // Esta clave se debería obtener de forma segura
    const hexaFac = new HexaFacClient(apiKey);

    // Ejemplo: Crear una factura
    const nuevaFactura = {
        // ... datos de la factura
    };

    hexaFac.crearFactura(nuevaFactura)
        .then(respuesta => {
            console.log('Factura creada:', respuesta);
        })
        .catch(error => {
            console.error('Error al crear la factura:', error);
        });
});
*/

export default HexaFacClient;
