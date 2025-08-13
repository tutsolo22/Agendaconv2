/**
 * Módulo centralizado para cargar y cachear los catálogos del SAT.
 * Utiliza sessionStorage para mantener los datos durante la sesión del navegador,
 * evitando llamadas repetidas a la API.
 */

const CACHE_KEY = 'satCatalogosCache';

/**
 * Obtiene los catálogos del SAT, primero desde la caché de sesión y, si no existen,
 * los solicita a la API y los guarda en la caché.
 *
 * @returns {Promise<Object>} Una promesa que se resuelve con el objeto de catálogos.
 * @throws {Error} Si la API no responde correctamente.
 */
export async function getCatalogos() {
    // 1. Intentar obtener los datos desde la caché de sesión
    const cachedData = sessionStorage.getItem(CACHE_KEY);
    if (cachedData) {
        console.log("✅ [Cache HIT] Catálogos cargados desde la caché de sesión.");
        return JSON.parse(cachedData);
    }

    // 2. Si no están en caché, hacer la llamada a la API
    if (!window.apiUrls || !window.apiUrls.catalogos) {
        throw new Error('La URL de la API de catálogos no está definida en window.apiUrls.');
    }

    console.log("🔄 [API CALL] Realizando llamada a la API para obtener catálogos...");
    const response = await fetch(window.apiUrls.catalogos, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    });

    if (!response.ok) throw new Error('Error al cargar los catálogos del SAT desde la API.');

    const catalogos = await response.json();
    sessionStorage.setItem(CACHE_KEY, JSON.stringify(catalogos)); // 3. Guardar en caché para futuras peticiones
    return catalogos;
}