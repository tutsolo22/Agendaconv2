# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added
*   **Soporte para PAC "Formas Digitales"**: Se añadió el servicio `FormasDigitalesTimbradoService` para integrar este nuevo proveedor de timbrado, que opera a través de un **Servicio Web SOAP**.
*   **Seeder para PAC**: Se creó un seeder para registrar automáticamente la configuración base del PAC "Formas Digitales" en la base de datos.

### Changed
*   **Selección Dinámica de PAC**: El `FacturacionServiceProvider` ahora es capaz de instanciar el nuevo servicio de "Formas Digitales", demostrando la flexibilidad de la arquitectura para manejar tanto APIs REST como SOAP.
*   **Interfaz de Configuración de PACs**: Se actualizó el `PacController` y la vista del formulario para manejar correctamente las credenciales de `usuario` y `contraseña` del nuevo PAC.

### Fixed
# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [0.1.4] - 2025-08-21

### Added
*   **Módulo de Notificaciones por Correo**: Se ha creado un módulo para el envío de comprobantes fiscales por correo electrónico. Incluye un `ComprobanteEmailService`, una clase `Mailable` (`ComprobanteMail`) y un `PdfService` para la generación de PDFs.
*   **Integración con EDICOM para Retenciones**: Se ha completado la integración con el PAC EDICOM para el timbrado de comprobantes de retenciones, reemplazando la lógica de simulación por una implementación real del servicio web SOAP.

### Changed
*   **Servicio de Facturación**: Se ha actualizado `FacturacionService` para que utilice los nuevos servicios de correo y PDF, permitiendo el envío automático de comprobantes después del timbrado.
*   **Controlador de CFDI**: Se ha refactorizado el método `downloadPdf` en `CfdiController` para utilizar el nuevo `PdfService`, centralizando la lógica de generación de PDFs.

---

## [Unreleased]

### Added
*   **Soporte para PAC "Formas Digitales"**: Se añadió el servicio `FormasDigitalesTimbradoService` para integrar este nuevo proveedor de timbrado, que opera a través de un **Servicio Web SOAP**.
*   **Seeder para PAC**: Se creó un seeder para registrar automáticamente la configuración base del PAC "Formas Digitales" en la base de datos.

### Changed
*   **Selección Dinámica de PAC**: El `FacturacionServiceProvider` ahora es capaz de instanciar el nuevo servicio de "Formas Digitales", demostrando la flexibilidad de la arquitectura para manejar tanto APIs REST como SOAP.
*   **Interfaz de Configuración de PACs**: Se actualizó el `PacController` y la vista del formulario para manejar correctamente las credenciales de `usuario` y `contraseña` del nuevo PAC.

### Fixed
*   **Error de Namespace en `tenant()`**: Se corrigió un error crítico `Undefined function 'tenant'` en el `FacturacionServiceProvider`. El problema se resolvió anteponiendo una barra invertida (`\tenant()`) para indicar a PHP que debe usar la función del espacio de nombres global de Laravel.

---

## [0.1.3] - 2024-05-20

### Added

### Changed

### Fixed

---

## [0.1.3] - 2024-05-20

### Changed
*   **Refactorización del Servicio de Facturación**: Se rediseñó por completo el servicio de facturación para seguir los principios de Inversión de Dependencias y Responsabilidad Única. La lógica de comunicación con el PAC (timbrado) se extrajo a un servicio dedicado (`SWTimbradoService`) que implementa una interfaz (`TimbradoServiceInterface`). Esto desacopla la lógica de negocio del proveedor específico, mejorando drásticamente la mantenibilidad, la capacidad de prueba y la flexibilidad para cambiar de PAC en el futuro.
*   **Validaciones CFDI 4.0**: Se robusteció el `StoreCfdiRequest` para incluir validaciones estrictas requeridas por el CFDI 4.0, como el formato del RFC del receptor, el requisito de mayúsculas para la razón social y la validación de existencia de catálogos para el régimen fiscal y código postal.

### Added
*   **Regla de Validación de RFC**: Se creó una regla de validación personalizada (`App\Modules\Facturacion\Rules\Rfc`) para verificar el formato correcto de los RFC de personas físicas y morales.
*   **Utilidad de Catálogos SAT**: Se añadió un helper (`App\Modules\Facturacion\Utils\SatCatalogs`) que incluye un método para limpiar automáticamente la razón social de los clientes, eliminando sufijos como "S.A. de C.V." antes del timbrado, como lo exige el SAT.

### Fixed


## [0.1.2] - 2024-05-19

### Added
*   **CRUD de Proveedores (PACs)**: Se implementó la funcionalidad completa para que los tenants puedan registrar y gestionar los diferentes Proveedores Autorizados de Certificación que utilizarán para el timbrado.
*   **CRUD de Series y Folios**: Se implementó la gestión completa de series y folios, permitiendo crear secuencias distintas para Facturas (Ingreso), Notas de Crédito (Egreso) y Complementos de Pago (Pago).
*   **Funcionalidad de Complementos de Pago**: Se completó el CRUD para la creación y gestión de borradores de Complementos de Recepción de Pagos. El formulario ahora busca dinámicamente facturas con saldo pendiente de un cliente a través de una llamada AJAX.

### Changed
*   **Refactorización de JavaScript**: Se extrajo todo el código JavaScript de las vistas de creación/edición de pagos a un archivo dedicado (`pagos.js`) utilizando Vite, mejorando la mantenibilidad y limpieza del código.

### Fixed
*   **Definición de Rutas**: Se corrigió un error `Route [...] not defined` al reorganizar el archivo de rutas del módulo de Facturación, asegurando que las rutas de búsqueda AJAX no heredaran prefijos de nombre incorrectos.
*   **Esquema de Base de Datos**: Se solucionó un error `Unknown column 'tipo_comprobante'` mediante la creación y ejecución de una migración para añadir la columna faltante a la tabla `facturacion_series_folios`.

---

## [0.1.1] - 2024-05-18

### Added
*   **CRUD de Datos Fiscales**: Se implementó la funcionalidad completa para que los tenants gestionen sus datos fiscales (RFC, Razón Social, CSD, etc.), incluyendo la subida de archivos de certificado y llave.
*   **Validación Inteligente**: El formulario de Datos Fiscales ahora distingue entre creación (donde los CSD son obligatorios) y edición (donde son opcionales).

### Changed
*   **Formulario Dinámico**: El campo "Régimen Fiscal" ahora se carga de forma asíncrona desde una API interna utilizando JavaScript y Tom-Select, mejorando la experiencia de usuario y la mantenibilidad.

### Fixed
*   **Carga de Catálogos**: Se corrigió un error crítico de JavaScript que impedía la ejecución del script de carga de catálogos en el formulario de Datos Fiscales. El problema se debía a un selector de formulario (`document.querySelector`) que no era compatible con las URLs generadas por Laravel. Se solucionó asignando un `id` único al formulario.

### Fixed

---

## [0.1.0] - 2024-05-17

### Added

*   **Arquitectura Base**:
    *   Configuración inicial del proyecto con Laravel 11, Breeze y Bootstrap 5.
    *   Implementación de arquitectura **Multi-Tenant** con el paquete `stancl/tenancy-for-laravel` y estrategia de base de datos única.
    *   Implementación de **Arquitectura Modular** bajo el directorio `app/Modules/`.
    *   Sistema de **Logging Modular** con `ModuleLoggerService` para registrar errores en archivos específicos por módulo.

*   **Panel del Super-Admin**:
    *   CRUD completo para la gestión de `Tenants`.
    *   CRUD completo para la gestión de `Módulos` del sistema.
    *   CRUD completo para la gestión de `Licencias`, permitiendo asignar módulos a tenants con límites y fechas de expiración.

*   **Panel del Tenant**:
    *   Dashboard con navegación dinámica que muestra solo los módulos licenciados.
    *   CRUD para la gestión de `Usuarios` del tenant, con validación de límite según la licencia.
    *   CRUD para la gestión de `Sucursales`.
    *   Sistema centralizado para la gestión de `Clientes`.
    *   Módulo transversal para la subida y gestión de `Documentos` asociados a clientes.

*   **Módulo de Facturación (CFDI 4.0)**:
    *   CRUDs para la configuración de `Datos Fiscales`, `PACs` y `Series y Folios`.
    *   Formulario de creación de facturas con una arquitectura API-First para una experiencia de usuario dinámica.
    *   Funcionalidad completa para crear y gestionar **Complementos de Recepción de Pagos**.

### Changed

*   **Optimización de Catálogos SAT**: Se refactorizó el `SatCatalogService` para leer los catálogos desde una base de datos dedicada en lugar de procesar archivos Excel, resultando en una mejora drástica de rendimiento.
*   **Reorganización de Controladores**: Los controladores del módulo de Facturación se reestructuraron en subdirectorios temáticos (`Cfdi_40`, `Configuracion`, `Pago`) para mejorar la claridad y escalabilidad.

### Fixed

*   **Error Crítico de Tenancy**: Se solucionó el error `Class "Tenancy" not found` instalando y configurando correctamente el paquete `stancl/tenancy-for-laravel`.
*   **Carga de Catálogos**: Se corrigió un problema que impedía la carga del catálogo de "Formas de Pago" debido a una caché corrupta y un filtro de vigencia demasiado estricto.
*   **Configuración de Logging**: Se corrigió un error en `config/logging.php` donde múltiples canales de log apuntaban al mismo archivo, asegurando que cada módulo registre sus errores de forma independiente.


---

## [0.1.3] - 2024-05-20

### Added

### Changed

### Fixed

---

## [0.1.3] - 2024-05-20

### Changed
*   **Refactorización del Servicio de Facturación**: Se rediseñó por completo el servicio de facturación para seguir los principios de Inversión de Dependencias y Responsabilidad Única. La lógica de comunicación con el PAC (timbrado) se extrajo a un servicio dedicado (`SWTimbradoService`) que implementa una interfaz (`TimbradoServiceInterface`). Esto desacopla la lógica de negocio del proveedor específico, mejorando drásticamente la mantenibilidad, la capacidad de prueba y la flexibilidad para cambiar de PAC en el futuro.
*   **Validaciones CFDI 4.0**: Se robusteció el `StoreCfdiRequest` para incluir validaciones estrictas requeridas por el CFDI 4.0, como el formato del RFC del receptor, el requisito de mayúsculas para la razón social y la validación de existencia de catálogos para el régimen fiscal y código postal.

### Added
*   **Regla de Validación de RFC**: Se creó una regla de validación personalizada (`App\Modules\Facturacion\Rules\Rfc`) para verificar el formato correcto de los RFC de personas físicas y morales.
*   **Utilidad de Catálogos SAT**: Se añadió un helper (`App\Modules\Facturacion\Utils\SatCatalogs`) que incluye un método para limpiar automáticamente la razón social de los clientes, eliminando sufijos como "S.A. de C.V." antes del timbrado, como lo exige el SAT.

### Fixed


## [0.1.2] - 2024-05-19

### Added
*   **CRUD de Proveedores (PACs)**: Se implementó la funcionalidad completa para que los tenants puedan registrar y gestionar los diferentes Proveedores Autorizados de Certificación que utilizarán para el timbrado.
*   **CRUD de Series y Folios**: Se implementó la gestión completa de series y folios, permitiendo crear secuencias distintas para Facturas (Ingreso), Notas de Crédito (Egreso) y Complementos de Pago (Pago).
*   **Funcionalidad de Complementos de Pago**: Se completó el CRUD para la creación y gestión de borradores de Complementos de Recepción de Pagos. El formulario ahora busca dinámicamente facturas con saldo pendiente de un cliente a través de una llamada AJAX.

### Changed
*   **Refactorización de JavaScript**: Se extrajo todo el código JavaScript de las vistas de creación/edición de pagos a un archivo dedicado (`pagos.js`) utilizando Vite, mejorando la mantenibilidad y limpieza del código.

### Fixed
*   **Definición de Rutas**: Se corrigió un error `Route [...] not defined` al reorganizar el archivo de rutas del módulo de Facturación, asegurando que las rutas de búsqueda AJAX no heredaran prefijos de nombre incorrectos.
*   **Esquema de Base de Datos**: Se solucionó un error `Unknown column 'tipo_comprobante'` mediante la creación y ejecución de una migración para añadir la columna faltante a la tabla `facturacion_series_folios`.

---

## [0.1.1] - 2024-05-18

### Added
*   **CRUD de Datos Fiscales**: Se implementó la funcionalidad completa para que los tenants gestionen sus datos fiscales (RFC, Razón Social, CSD, etc.), incluyendo la subida de archivos de certificado y llave.
*   **Validación Inteligente**: El formulario de Datos Fiscales ahora distingue entre creación (donde los CSD son obligatorios) y edición (donde son opcionales).

### Changed
*   **Formulario Dinámico**: El campo "Régimen Fiscal" ahora se carga de forma asíncrona desde una API interna utilizando JavaScript y Tom-Select, mejorando la experiencia de usuario y la mantenibilidad.

### Fixed
*   **Carga de Catálogos**: Se corrigió un error crítico de JavaScript que impedía la ejecución del script de carga de catálogos en el formulario de Datos Fiscales. El problema se debía a un selector de formulario (`document.querySelector`) que no era compatible con las URLs generadas por Laravel. Se solucionó asignando un `id` único al formulario.

### Fixed

---

## [0.1.0] - 2024-05-17

### Added

*   **Arquitectura Base**:
    *   Configuración inicial del proyecto con Laravel 11, Breeze y Bootstrap 5.
    *   Implementación de arquitectura **Multi-Tenant** con el paquete `stancl/tenancy-for-laravel` y estrategia de base de datos única.
    *   Implementación de **Arquitectura Modular** bajo el directorio `app/Modules/`.
    *   Sistema de **Logging Modular** con `ModuleLoggerService` para registrar errores en archivos específicos por módulo.

*   **Panel del Super-Admin**:
    *   CRUD completo para la gestión de `Tenants`.
    *   CRUD completo para la gestión de `Módulos` del sistema.
    *   CRUD completo para la gestión de `Licencias`, permitiendo asignar módulos a tenants con límites y fechas de expiración.

*   **Panel del Tenant**:
    *   Dashboard con navegación dinámica que muestra solo los módulos licenciados.
    *   CRUD para la gestión de `Usuarios` del tenant, con validación de límite según la licencia.
    *   CRUD para la gestión de `Sucursales`.
    *   Sistema centralizado para la gestión de `Clientes`.
    *   Módulo transversal para la subida y gestión de `Documentos` asociados a clientes.

*   **Módulo de Facturación (CFDI 4.0)**:
    *   CRUDs para la configuración de `Datos Fiscales`, `PACs` y `Series y Folios`.
    *   Formulario de creación de facturas con una arquitectura API-First para una experiencia de usuario dinámica.
    *   Funcionalidad completa para crear y gestionar **Complementos de Recepción de Pagos**.

### Changed

*   **Optimización de Catálogos SAT**: Se refactorizó el `SatCatalogService` para leer los catálogos desde una base de datos dedicada en lugar de procesar archivos Excel, resultando en una mejora drástica de rendimiento.
*   **Reorganización de Controladores**: Los controladores del módulo de Facturación se reestructuraron en subdirectorios temáticos (`Cfdi_40`, `Configuracion`, `Pago`) para mejorar la claridad y escalabilidad.

### Fixed

*   **Error Crítico de Tenancy**: Se solucionó el error `Class "Tenancy" not found` instalando y configurando correctamente el paquete `stancl/tenancy-for-laravel`.
*   **Carga de Catálogos**: Se corrigió un problema que impedía la carga del catálogo de "Formas de Pago" debido a una caché corrupta y un filtro de vigencia demasiado estricto.
*   **Configuración de Logging**: Se corrigió un error en `config/logging.php` donde múltiples canales de log apuntaban al mismo archivo, asegurando que cada módulo registre sus errores de forma independiente.
