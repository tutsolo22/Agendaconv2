<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# Agendacon v2

**Agendacon v2** es una moderna plataforma de software como servicio (SaaS) construida con Laravel 11. Su diseño se basa en una arquitectura robusta y escalable que es **Multi-Tenant**, **Multi-Módulo** y **Multi-Sucursal**, utilizando una única base de datos para simplificar la gestión y el mantenimiento.

Este proyecto sirve como una base sólida para desarrollar una amplia gama de aplicaciones de gestión empresarial, donde cada cliente (tenant) puede suscribirse a diferentes módulos funcionales.

> Para una descripción técnica detallada, consulta el archivo **[DOCUMENTACION.md](DOCUMENTACION.md)**.
> Para un historial completo de versiones y cambios, consulta el **[CHANGELOG.md](CHANGELOG.md)**.

---

## Principios Arquitectónicos Clave

*   **Multi-Tenancy**: Implementado con el paquete `stancl/tenancy-for-laravel`, utilizando una estrategia de base de datos única con aislamiento de datos a nivel de aplicación mediante Global Scopes.
*   **Arquitectura Modular**: El código está organizado en módulos autocontenidos (ej. Facturación, Citas Médicas) ubicados en `app/Modules/`. Cada módulo tiene sus propias rutas, controladores y servicios, y se registra a través de su propio `ServiceProvider`.
*   **API-Driven Frontend**: Para interfaces complejas como la creación de facturas, el frontend (Blade, Bootstrap, JavaScript) consume una API interna segura. Esto garantiza una experiencia de usuario rápida y desacopla la lógica de la presentación.
*   **Logging Centralizado**: Un servicio personalizado (`ModuleLoggerService`) dirige los logs a archivos específicos por módulo (ej. `storage/logs/facturacion/facturacion.log`), facilitando enormemente la depuración.

---
## Características Implementadas
### Panel del Super-Admin
*   **Gestión de Tenants**: CRUD completo para administrar los clientes de la plataforma.
*   **Gestión de Módulos**: CRUD para definir los módulos que se pueden licenciar.
*   **Gestión de Licencias**: Asignación de módulos a tenants, con control de fechas de expiración y límites (ej. número de usuarios).
*   **Configuración Centralizada**: Capacidad para ver y modificar la configuración de cualquier tenant.

### Panel del Tenant
*   **Dashboard y Navegación Dinámica**: El menú de navegación se genera automáticamente mostrando solo los módulos a los que el tenant tiene licencia.
*   **Gestión de Usuarios y Sucursales**: CRUD para usuarios y sucursales, respetando los límites definidos en la licencia.
*   **Gestión de Clientes**: Un sistema centralizado para gestionar clientes, que puede ser utilizado por cualquier módulo.
*   **Gestor de Documentos**: Un sistema transversal para subir y asociar archivos a los clientes de forma segura.

### Módulo de Facturación (CFDI 4.0)
*   **Formulario de Creación API-Driven**: Un formulario dinámico para crear facturas (CFDI 4.0) que consume catálogos del SAT y busca clientes/productos a través de una API interna.
*   **Catálogos del SAT Optimizados**: Los catálogos del SAT se almacenan y consultan desde la base de datos, eliminando la necesidad de procesar archivos pesados en cada petición.
*   **Complemento de Pago**: Funcionalidad completa para generar y administrar Complementos de Recepción de Pagos.
*   **Configuración del Módulo**: CRUDs para gestionar Datos Fiscales (CSD), PACs (Proveedores de Timbrado) y Series/Folios.

---

## Primeros Pasos
1.  Clonar el repositorio: `git clone ...`
2.  Instalar dependencias: `composer install`
3.  Crear y configurar el archivo `.env` a partir de `.env.example`.
4.  Generar la clave de la aplicación: `php artisan key:generate`
5.  Ejecutar las migraciones y los seeders: `php artisan migrate:fresh --seed`
6.  Crear el enlace simbólico para el almacenamiento: `php artisan storage:link`
7.  Compilar los assets: `npm install && npm run dev`
8.  Iniciar el servidor: `php artisan serve`

---

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
