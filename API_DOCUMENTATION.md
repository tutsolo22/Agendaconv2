# Proyecto HexaFac: Plataforma de Facturación Centralizada

## 1. Introducción

Este documento describe la arquitectura, funcionalidades y especificaciones técnicas para el desarrollo de **HexaFac**, una plataforma de **Facturación como Servicio (Billing as a Service)**. El objetivo de HexaFac es centralizar y simplificar por completo la emisión de CFDI 4.0, permitiendo que otras aplicaciones deleguen esta complejidad a través de una API robusta, segura y escalable.

---

## 2. Propuesta de Valor

HexaFac ofrecerá a sus aplicaciones cliente los siguientes beneficios clave:

-   **Centralización:** Gestionar clientes, productos y facturas de múltiples sistemas desde un único panel de control.
-   **Abstracción:** Ocultar toda la complejidad del timbrado con PACs, cancelaciones, notas de crédito y normativas del SAT. Las aplicaciones cliente solo se preocupan por enviar datos de negocio.
-   **Seguridad:** Proporcionar un acceso seguro y con permisos granulares a través de un sistema de API Keys con ámbitos (scopes) definidos.
-   **Escalabilidad y Robustez:** Diseñado para manejar un alto volumen de transacciones de manera asíncrona, garantizando que el rendimiento de las aplicaciones cliente no se vea afectado por los tiempos del proceso de timbrado.

---

## 3. Identidad Visual y Paleta de Colores

El panel de administración de HexaFac y toda su identidad visual seguirán una línea profesional y tecnológica, utilizando la siguiente paleta de colores:

-   **Colores Principales:**
    -   **Negro (#000000):** Aporta elegancia y seriedad.
    -   **Dorado (#DAA520):** Representa valor y calidad premium, manteniendo la conexión con Hexalux.
-   **Colores de Acento:**
    -   **Gris Metálico (#808080):** Sugiere solidez, tecnología y estructura.
    -   **Azul Marino (#000080):** Un color clásico en el sector financiero que transmite confianza, estabilidad y seguridad.

El logotipo se integrará una vez que sea proporcionado.

---

## 4. Arquitectura y Componentes Clave

El ecosistema de HexaFac se compondrá de los siguientes elementos:

1.  **Núcleo de Facturación (Core - Laravel):**
    -   El motor principal de la plataforma, basado en la aplicación Laravel existente. Contiene toda la lógica de negocio para la generación de comprobantes, conexión con el PAC, envío de correos y gestión de la base de datos.

2.  **Pasarela API (API Gateway):**
    -   El punto de entrada público y versionado (`/api/hexafac/v1`) para todas las aplicaciones externas.
    -   Sus responsabilidades son: autenticar solicitudes, verificar permisos (scopes), validar datos y encolar trabajos para su procesamiento asíncrono.

3.  **Panel de Administración de HexaFac:**
    -   Una interfaz web para la autogestión de la plataforma, permitiendo:
        -   **Gestionar Aplicaciones Cliente:** Registrar y nombrar las aplicaciones que se conectarán.
        -   **Administrar API Keys:** Generar claves únicas por aplicación y asignarles permisos específicos.
        -   **Configurar Webhooks:** Registrar las URLs de las aplicaciones cliente donde recibirán notificaciones asíncronas.
        -   **Monitorear Actividad:** Visualizar logs de uso de la API, estadísticas y errores.

4.  **Mecanismo de Webhooks y Cola de Trabajos:**
    -   La base de la robustez del sistema. Todas las operaciones que puedan tardar (timbrado, cancelación) se procesarán en segundo plano mediante la cola de trabajos de Laravel.
    -   Al finalizar un trabajo, el sistema notificará el resultado (éxito o error) a la aplicación cliente de forma proactiva a través de una petición POST a su webhook registrado.

---

## 5. Flujo de Trabajo Detallado: Creación de una Factura

1.  **Petición Inicial (App Cliente -> HexaFac):**
    -   La aplicación cliente envía una petición `POST` a `/api/hexafac/v1/facturas` con los datos del cliente y los conceptos en formato JSON, incluyendo su API Key en los headers (`Authorization: Bearer <API_KEY>`).

2.  **Respuesta Inmediata (HexaFac -> App Cliente):**
    -   HexaFac valida la API Key, los permisos y la estructura del JSON.
    -   Si todo es correcto, encola un trabajo y responde **inmediatamente** con un código `202 Accepted` y un ID de transacción para seguimiento.
        ```json
        {
          "status": "peticion_recibida",
          "mensaje": "La factura ha sido encolada para su procesamiento.",
          "transaccion_id": "uuid-para-seguimiento-1234"
        }
        ```

3.  **Procesamiento Asíncrono (En HexaFac):**
    -   Un proceso en segundo plano (worker) toma el trabajo, realiza la lógica de negocio: busca/crea el cliente, genera el XML, se comunica con el PAC para el timbrado y prepara la respuesta.

4.  **Notificación Final (HexaFac -> App Cliente vía Webhook):**
    -   Al terminar el proceso, HexaFac envía una petición `POST` al webhook de la aplicación cliente con el resultado final.
    -   **En caso de éxito:**
        ```json
        {
          "evento": "factura.timbrada",
          "transaccion_id": "uuid-para-seguimiento-1234",
          "data": {
            "status": "timbrada",
            "uuid_fiscal": "ABC-DEF-GHI-JKL",
            "folio_factura": "F-1234",
            "url_pdf": "https://hexafac.com/descargas/uuid/pdf",
            "url_xml": "https://hexafac.com/descargas/uuid/xml"
          }
        }
        ```
    -   **En caso de error:**
        ```json
        {
          "evento": "factura.error",
          "transaccion_id": "uuid-para-seguimiento-1234",
          "data": {
            "status": "error",
            "codigo_error": "PAC-003",
            "mensaje_error": "El RFC del receptor no se encuentra en la lista de LCO del SAT."
          }
        }
        ```

---

## 6. Modelo de Seguridad: API Keys y Permisos (Scopes)

Se implementará un sistema de permisos granulares basado en "scopes" asignados a cada API Key, utilizando **Laravel Sanctum**.

-   **API Keys:** Claves únicas generadas desde el panel de HexaFac para cada aplicación cliente.
-   **Scopes Propuestos:**
    -   `clientes:crear`, `clientes:leer`
    -   `facturas:crear`, `facturas:cancelar`, `facturas:leer_status`
    -   `pagos:crear`
    -   `notas_credito:crear`

---

## 7. Formato de Intercambio de Datos (API - JSON)

**Ejemplo de Petición para Crear Factura (`POST /facturas`):**
```json
{
  "cliente": {
    "id_externo": "CLI-5678",
    "crear_si_no_existe": true,
    "rfc": "XAXX010101000",
    "razon_social": "PÚBLICO EN GENERAL",
    "cfdi_uso": "S01",
    "regimen_fiscal": "616",
    "domicilio": {
      "codigo_postal": "12345"
    }
  },
  "conceptos": [
    {
      "clave_sat": "50211503",
      "id_externo": "PROD-001",
      "descripcion": "Consulta Médica General",
      "cantidad": 1,
      "valor_unitario": 500.00,
      "clave_unidad": "E48",
      "impuestos": {
        "iva_tasa": 0.16
      }
    }
  ],
  "opciones": {
    "tipo_comprobante": "I",
    "moneda": "MXN",
    "forma_pago": "01",
    "metodo_pago": "PUE"
  }
}
```

---

## 8. Entorno de Pruebas (Sandbox)

Se habilitará un entorno de **Sandbox** completo para que los desarrolladores puedan integrar y probar la API sin generar comprobantes fiscales reales.

-   Utilizará un subdominio o prefijo de URL distinto (ej. `sandbox.hexafac.com`).
-   Tendrá su propio juego de API Keys de prueba.
-   Las operaciones de timbrado no se conectarán al PAC real, sino que simularán una respuesta exitosa o de error.

---

## 9. Documentación de la API

Se generará una documentación de API de alta calidad, interactiva y fácil de usar, utilizando el estándar **OpenAPI 3 (anteriormente Swagger)**. Esta documentación incluirá:

-   Descripción detallada de cada endpoint.
-   Modelos de datos para peticiones y respuestas.
-   Ejemplos de código.
-   Una consola interactiva para realizar llamadas de prueba directamente desde el navegador.

---

## 10. Kit de Desarrollo (SDK)

Para acelerar y simplificar la integración por parte de las aplicaciones cliente, se desarrollará un Kit de Desarrollo (SDK) oficial.

-   **Plataforma Inicial:** **Node.js**.
-   **Distribución:** Se publicará como un paquete oficial en **NPM** (`@hexafac/sdk`).
-   **Funcionalidad:** Proveerá métodos simples y claros para realizar todas las operaciones de la API (ej. `hexafac.facturas.crear(...)`), manejando internamente la autenticación y las llamadas HTTP.