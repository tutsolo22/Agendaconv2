# Changelog

Todos los cambios notables en este proyecto serán documentados en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto se adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

---

## [Unreleased]

### Added

### Changed

### Fixed

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
