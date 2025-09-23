# Documentación de API - Módulo de Facturación

Esta documentación describe los endpoints de la API pública para el módulo de Facturación. Todos los endpoints requieren autenticación mediante un token de API.

## Autenticación

Todas las peticiones a la API deben incluir un encabezado de autorización con un token de tipo Bearer.

**Encabezado Requerido:**
```
Authorization: Bearer {TU_TOKEN_DE_API}
```

---

## Endpoints

### 1. Buscar Clientes

Permite buscar clientes por nombre o RFC. La búsqueda es sensible al `tenant_id` asociado al token de API.

- **Método:** `GET`
- **URL:** `/api/facturacion/clientes/search`
- **Parámetros de Query:**
    - `q` (string, requerido): El término de búsqueda.
- **Respuesta Exitosa (200 OK):**

    ```json
    [
        {
            "id": 123,
            "text": "Juan Perez (PEPJ800101ABC)"
        },
        {
            "id": 456,
            "text": "Maria Garcia (GAMM900202XYZ)"
        }
    ]
    ```
