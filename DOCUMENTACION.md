---

## 14. MÓDULO DE CARTA PORTE

### 14.1. Propósito y Alcance
El módulo de Carta Porte permite la generación, gestión y timbrado de Comprobantes de Traslado con Complemento Carta Porte, conforme a las regulaciones del SAT. Su objetivo es facilitar el cumplimiento fiscal para el transporte de mercancías.

### 14.2. Funcionalidades Implementadas
*   **Guardado de Borradores**: Los usuarios pueden guardar una Carta Porte en progreso en cualquier momento, permitiendo continuar su edición posteriormente sin necesidad de timbrado inmediato.
*   **Edición de Borradores**: Las Cartas Porte guardadas como borrador pueden ser recuperadas y modificadas. Se ha implementado lógica para asegurar que solo los borradores sean editables.
*   **Timbrado con PAC Edicom**: Integración completa con el Proveedor Autorizado de Certificación (PAC) Edicom para el timbrado oficial de las Cartas Porte, asegurando la validez fiscal del documento.

### 14.3. Componentes Clave
*   **Modelo `CartaPorte`**: Representa la entidad principal de la Carta Porte. Se ha añadido un campo `status` (`borrador`, `timbrado`, `cancelado`) para gestionar el ciclo de vida del documento.
*   **Controlador `CartaPorteController` (`app/Modules/Facturacion/Http/Controllers/CartaPorteController.php`)**:
    *   `storeAsDraft`: Nuevo método para guardar borradores de Carta Porte.
    *   `edit`: Permite la edición de Cartas Porte en estado de borrador.
    *   `update`: Actualiza los datos de una Carta Porte existente en estado de borrador.
*   **Servicio `CartaPorteService` (`app/Modules/Facturacion/Services/CartaPorteService.php`)**:
    *   `buildCartaPorteXml`: Implementación detallada para la construcción del XML de la Carta Porte, siguiendo el esquema XSD del SAT.
    *   `callPacService`: Lógica para la comunicación con el servicio web de Edicom para el timbrado.
    *   `generateAndStamp`: Orquesta la generación del XML, la llamada al PAC y el registro del CFDI timbrado.
*   **Validación (`StoreCartaPorteRequest`)**: Se han ampliado las reglas de validación para cubrir todos los campos del complemento Carta Porte, incluyendo ubicaciones, mercancías, autotransporte y figura de transporte.
*   **Rutas (`app/Modules/Facturacion/Routes/carta_porte.php`)**:
    *   Se añadió una ruta específica para `storeAsDraft`.
    *   Se habilitaron las rutas `edit` y `update` para el recurso `cartaporte`.
*   **Vistas (`resources/views/tenant/modules/facturacion/cartaporte/`)**:
    *   `create.blade.php`: Formulario de creación con opción de guardar como borrador o timbrar.
    *   `edit.blade.php`: Formulario para la edición de borradores, pre-llenado con los datos existentes.
    *   `index.blade.php`: Listado de Cartas Porte que muestra el estado (`Borrador`, `Timbrado`) y permite la edición de borradores.

---

## 15. Estructura JSON para Submenús de Módulos

Para la correcta visualización de los submenús en el apartado "Módulos" del dashboard del tenant, el campo `submenu` en la tabla `modulos` debe contener una estructura JSON específica.

**Formato Esperado:**

El campo `submenu` debe ser un array de objetos, donde cada objeto representa un grupo de submenús o un enlace directo. Si un elemento tiene sub-elementos, debe contener una clave `"submenu"` que a su vez sea un array de esos sub-elementos.

**Ejemplo de Estructura JSON Correcta:**

```json
[
    {
        "nombre": "CFDIs",
        "submenu": [
            {
                "nombre": "Facturas",
                "route": "tenant.facturacion.cfdis.index"
            },
            {
                "nombre": "Notas de Crédito",
                "route": "tenant.facturacion.cfdis.index"
            }
        ]
    },
    {
        "nombre": "Complementos",
        "submenu": [
            {
                "nombre": "Recepción de Pagos",
                "route": "tenant.facturacion.pagos.index"
            },
            {
                "nombre": "Retenciones",
                "route": "tenant.facturacion.retenciones.index"
            },
            {
                "nombre": "Carta Porte",
                "route": "tenant.facturacion.cartaporte.index"
            }
        ]
    },
    {
        "nombre": "Configuración",
        "submenu": [
            {
                "nombre": "Datos Fiscales",
                "route": "tenant.facturacion.configuracion.datos-fiscales.index"
            },
            {
                "nombre": "Series y Folios",
                "route": "tenant.facturacion.configuracion.series-folios.index"
            },
            {
                "nombre": "Proveedores (PAC)",
                "route": "tenant.facturacion.configuracion.pacs.index"
            }
        ]
    }
]
```

**Consideraciones Importantes:**

*   **Clave de Ruta:** Utilizar la clave `"route"` para especificar el nombre de la ruta de Laravel.
*   **Anidamiento:** La plantilla Blade está diseñada para manejar hasta dos niveles de anidamiento (un grupo principal con un `submenu` que contiene enlaces directos).
*   **Validación:** Asegurarse de que el JSON sea sintácticamente correcto para evitar errores en la aplicación.
