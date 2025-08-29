# Documentación de la API de Facturación

Esta es la documentación para la API del módulo de Facturación. La API proporciona acceso a los catálogos del SAT y a otras funcionalidades necesarias para la creación de CFDI y Carta Porte.

## Endpoints

### Obtener todos los catálogos estáticos

*   **Método:** `GET`
*   **URL:** `/api/facturacion/catalogos`
*   **Descripción:** Devuelve una colección de todos los catálogos estáticos del SAT necesarios para los formularios de facturación. Es la forma más eficiente de obtener todos los catálogos en una sola llamada.
*   **Parámetros de consulta:** Ninguno.
*   **Respuesta Exitosa (200 OK):**
    ```json
    {
        "usosCfdi": [
            { "id": "G01", "texto": "Adquisición de mercancías" },
            ...
        ],
        "metodosPago": [
            { "id": "PUE", "texto": "Pago en una sola exhibición" },
            ...
        ],
        "formasPago": [
            { "id": "01", "texto": "Efectivo" },
            ...
        ],
        "monedas": [
            { "id": "MXN", "texto": "Peso Mexicano" },
            ...
        ],
        "tiposComprobante": [
            { "id": "I", "texto": "Ingreso" },
            ...
        ],
        "objetosImpuesto": [
            { "id": "01", "texto": "No objeto de impuesto" },
            ...
        ],
        "regimenesFiscales": [
            { "id": "601", "texto": "General de Ley Personas Morales" },
            ...
        ],
        "clavesUnidad": [
            { "id": "H87", "texto": "Pieza" },
            ...
        ],
        "periodicidades": [
            { "id": "01", "texto": "Diario" },
            ...
        ],
        "meses": [
            { "id": "01", "texto": "Enero" },
            ...
        ],
        "tiposRelacion": [
            { "id": "01", "texto": "Nota de crédito de los documentos relacionados" },
            ...
        ],
        "retenciones": [
            { "id": "01", "texto": "IVA" },
            ...
        ]
    }
    ```

### Obtener Series y Folios

*   **Método:** `GET`
*   **URL:** `/api/facturacion/series`
*   **Descripción:** Devuelve las series y folios de tipo "Ingreso" que están activas para el tenant.
*   **Parámetros de consulta:** Ninguno.
*   **Respuesta Exitosa (200 OK):**
    ```json
    [
        {
            "id": 1,
            "serie": "F",
            "folio_actual": 123
        },
        {
            "id": 2,
            "serie": "A",
            "folio_actual": 456
        }
    ]
    ```

### Buscar Clientes

*   **Método:** `GET`
*   **URL:** `/api/facturacion/clientes/search`
*   **Descripción:** Busca clientes por nombre o RFC. La búsqueda se activa con un mínimo de 2 caracteres.
*   **Parámetros de consulta:**
    *   `q` (string, requerido): El término de búsqueda.
*   **Respuesta Exitosa (200 OK):**
    ```json
    [
        {
            "id": 1,
            "nombre_completo": "Cliente de Prueba S.A. de C.V.",
            "rfc": "XAXX010101000",
            "regimen_fiscal_receptor": "601",
            "codigo_postal_receptor": "12345"
        }
    ]
    ```

### Buscar Productos o Servicios (Catálogo SAT)

*   **Método:** `GET`
*   **URL:** `/api/facturacion/productos-servicios/search`
*   **Descripción:** Busca productos o servicios en el catálogo del SAT por clave o descripción.
*   **Parámetros de consulta:**
    *   `q` (string, requerido): El término de búsqueda.
*   **Respuesta Exitosa (200 OK):**
    ```json
    [
        {
            "id": "01010101",
            "texto": "01010101 - No existe en el catálogo"
        },
        {
            "id": "84111506",
            "texto": "84111506 - Servicios de facturación"
        }
    ]
    ```

### Buscar CFDI

*   **Método:** `GET`
*   **URL:** `/api/facturacion/search-cfdis`
*   **Descripción:** Busca CFDI por folio o UUID. La búsqueda se activa con un mínimo de 2 caracteres.
*   **Parámetros de consulta:**
    *   `q` (string, requerido): El término de búsqueda.
*   **Respuesta Exitosa (200 OK):**
    ```json
    [
        {
            "id": 1,
            "serie": "F",
            "folio": 123,
            "total": "1160.00",
            "uuid_fiscal": "a1b2c3d4-e5f6-4a7b-8c9d-0e1f2a3b4c5d"
        }
    ]
    ```

### Obtener un Catálogo Específico del SAT

*   **Método:** `GET`
*   **URL:** `/api/facturacion/sat-catalogs/{catalogName}`
*   **Descripción:** Devuelve un catálogo específico del SAT. El nombre del catálogo se pasa como parte de la URL.
*   **Parámetros de URL:**
    *   `catalogName` (string, requerido): El nombre del catálogo a obtener. El nombre debe coincidir con el nombre de un método en `SatCatalogService` (sin el prefijo "get"). Ejemplos: `formasPago`, `metodosPago`, `usosCfdi`, `monedas`, `tiposDeComprobante`, `objetosImpuesto`, `regimenesFiscales`, `clavesUnidad`, `periodicidades`, `meses`, `tiposRelacion`, `retenciones`, y todos los catálogos de Carta Porte (`autorizacionesNavieroCcp31`, `clavesUnidadesCcp31`, etc.).
*   **Parámetros de consulta:**
    *   `q` (string, opcional): Un término de búsqueda para filtrar los resultados (si el método del servicio lo soporta).
*   **Respuesta Exitosa (200 OK):**
    ```json
    [
        { "id": "01", "texto": "Efectivo" },
        { "id": "02", "texto": "Cheque nominativo" },
        ...
    ]
    ```
*   **Respuesta de Error (404 Not Found):**
    ```json
    {
        "error": "Catálogo no encontrado o método no implementado."
    }
    ```
