# Documentación del Proyecto - Agendacon v2

Este documento detalla el proceso de instalación, configuración y puesta en marcha del proyecto Agendacon v2, construido sobre Laravel.

> Para un registro detallado de todas las versiones, nuevas características y correcciones, consulta el archivo **[CHANGELOG.md](CHANGELOG.md)**.

---

## 1. PRINCIPIOS FUNDAMENTALES

*   **Framework Backend:** Laravel 11/12.
*   **Stack Frontend:** Laravel Breeze con Blade, **Bootstrap 5** y Vite.
*   **Arquitectura General:** Multi-Tenant, Multi-Módulo, Multi-Sucursal.
*   **Modelo de Base de Datos:** **BASE DE DATOS ÚNICA**. Toda la información de todos los tenants y módulos reside en una sola base de datos. Esta decisión simplifica el mantenimiento, los respaldos y la gestión general.
*   **Separación de Datos (Tenancy):** La separación de datos entre tenants se logra estrictamente a través de una columna `tenant_id` en cada tabla relevante.
*   **Licenciamiento de Módulos:** El acceso a los módulos se controla mediante una tabla `licencias` que vincula `tenants`, `modulos`, y define fechas de expiración, límites de usuarios, etc.

## 2. ESTRUCTURA DE LA BASE DE DATOS (REGLAS DE ORO)

Las tablas se clasifican en tres categorías principales:

**A) Tablas Centrales del Sistema:**
   - **Propósito:** Administran la aplicación en su conjunto. No pertenecen a ningún tenant.
   - **Ejemplos:** `tenants`, `modulos`, `licencias`, `licencia_historial`, `users` (para Super-Admins).
   - **Regla:** Estas tablas **NO DEBEN** tener una columna `tenant_id`.

**B) Tablas de Recursos del Tenant (Compartidas entre Módulos):**
   - **Propósito:** Contienen entidades que pertenecen a un tenant y pueden ser utilizadas por diferentes módulos de ese mismo tenant.
   - **Ejemplos:** `clientes` (del tenant), `pacientes` (del tenant), `proveedores`, `inventario_general`, `sucursales`.
   - **Regla:** Estas tablas **DEBEN** tener una columna `tenant_id`, pero **NO DEBEN** tener una columna `modulo_id`.
   - **Implementación Actual**: Se ha creado el modelo `Cliente` como el primer recurso compartido, unificando el concepto de "paciente" y "cliente" en una sola entidad.

**C) Tablas Transaccionales o de Configuración de Módulo Específico:**
*   **Propósito**: Contienen entidades que pertenecen a un tenant y son compartidas por todos sus módulos. Son el núcleo de la información del negocio del tenant.
*   **Ejemplos**: `clientes`, `productos`, `proveedores`.
*   **Regla**: Estas tablas **DEBEN** tener una columna `tenant_id`, pero **NO DEBEN** tener una columna `modulo_id`.
*   **Implementación Actual**: Se ha creado el modelo `Cliente` como el primer recurso compartido, unificando el concepto de "paciente" y "cliente" en una sola entidad.

**C) Tablas Transaccionales o de Configuración de Módulo Específico:**
   - **Propósito:** Contienen registros de operaciones o configuraciones que son exclusivas de un módulo específico dentro de un tenant.
   - **Sugerencia de Nomenclatura:** Prefijar el nombre de la tabla con el nombre del módulo para mayor claridad (ej: `restaurante_notas`, `medicas_citas`).
   - **Ejemplos:**
     - Módulo Restaurante: `restaurante_notas`, `restaurante_gastos`, `restaurante_menus`.
     - Módulo Facturación: `facturacion_cfdi`, `facturacion_pagos`, `facturacion_series_folios`, `facturacion_datos_fiscales`.
     - Módulo Citas Médicas: `medicas_citas`, `medicas_historiales_clinicos`.
   - **Regla:** Estas tablas **DEBEN** tener una columna `tenant_id`. La columna `modulo_id` es opcional pero recomendada si la lógica del módulo lo requiere.

**D) Tablas de Catálogos del Sistema (SAT):**
   - **Propósito:** Contienen la información de los catálogos oficiales del SAT, necesarios para la facturación y otros módulos fiscales. Son pobladas y actualizadas mediante *seeders* y consumidas a través de una API interna para optimizar el rendimiento.
   - **Ejemplos:** `sat_formas_pago`, `sat_usos_cfdi`, `sat_productos_servicios`, `sat_regimenes_fiscales`.
   - **Regla:** Estas tablas **NO DEBEN** tener una columna `tenant_id` ni `modulo_id`. Son recursos globales del sistema, compartidos por todos los tenants.

---

## 3. LÓGICA DE ACCESO A DATOS (AISLAMIENTO DE TENANTS)

*   **Global Scopes:** Para automatizar el aislamiento de datos, se implementó un **Global Scope** de Laravel.
    *   **Scope**: `app/Models/Scopes/TenantScope.php`. Su lógica añade automáticamente la condición `WHERE tenant_id = ?` a todas las consultas de los modelos que lo usan.
    *   **Trait**: `app/Traits/TenantScoped.php`. Este trait facilita la aplicación del `TenantScope` y asigna automáticamente el `tenant_id` del usuario autenticado al crear un nuevo registro.
    *   **Lógica Clave**: El scope se activa únicamente si el usuario autenticado tiene un `tenant_id`. Esto excluye automáticamente al `Super-Admin` (cuyo `tenant_id` es `NULL`) del filtrado.

*   **Resolución de Dependencia Circular (Decisión Arquitectónica Crítica)**:
    *   Durante el desarrollo, se detectó un error 500 recurrente causado por una **dependencia circular**: el sistema de autenticación intentaba cargar un usuario, lo que activaba el `TenantScope` en el modelo `User`, y este a su vez intentaba acceder al usuario que aún no estaba completamente cargado.
    *   **Solución Definitiva**: Se determinó que el modelo `User` **no debe** usar el `TenantScoped` trait. El modelo de usuario es la fuente de verdad para la autenticación y no debe ser filtrado por un scope que dependa de sí mismo.
    *   **Impacto**: El aislamiento de datos para los usuarios del tenant se realiza ahora **explícitamente en los controladores** (ej: `Tenant\UserController`) mediante cláusulas `where('tenant_id', ...)`. Esto rompe el ciclo y garantiza la estabilidad del sistema.
    *   **Aplicación del Scope**: El trait `TenantScoped` se aplica a todos los demás modelos que pertenecen a un tenant, como `Licencia`, `Sucursal`, etc., pero **no** a `User`.

## 4. ARQUITECTURA FRONTEND

*   **Iconografía:** Se utiliza **Font Awesome 6** para un set de iconos completo.
*   **Asset Bundling:** Se usa **Vite**, la herramienta por defecto en Laravel, para la compilación y optimización de assets (CSS, JS).
*   **Interactividad:** Se utiliza **Alpine.js** para añadir interactividad ligera en el frontend, como la generación automática de slugs en los formularios.
*   **Interfaces Dinámicas**: Para formularios complejos como el de facturación, se utiliza **JavaScript nativo (Fetch API)** para consumir una API interna y librerías como **Tom-Select** para crear buscadores dinámicos y mejorar la experiencia de usuario.

## 5. COMPONENTES CLAVE Y SUGERENCIAS DE IMPLEMENTACIÓN

*   **Roles y Permisos:**
    - **Paquete:** Se utiliza `spatie/laravel-permission`.
    - **Roles Definidos:** `Super-Admin`, `Tenant-Admin`, `Tenant-User` (y otros roles específicos de módulo si son necesarios).
    - **Autorización**: Se implementaron **Gates** de Laravel (ej: `manage-tenant-user` en `AuthServiceProvider`) para autorizar explícitamente que un `Tenant-Admin` solo pueda editar o eliminar usuarios de su propio tenant.

*   **Auditoría de Movimientos:**
    - **Sugerencia:** Utilizar el paquete `spatie/laravel-activitylog`.
    - **Configuración:** Se configurará para registrar eventos (created, updated, deleted) en los modelos críticos. Guardará `user_id`, `ip_address`, y los datos modificados.

*   **Generador de Licencias:**
    - **Lógica:** Un panel de Super-Admin permite crear registros en la tabla `licencias`. Se genera un `codigo_licencia` único (UUID) que el tenant usará para activar sus módulos.
    - **Flexibilidad:** La tabla `licencias` incluye campos para `fecha_fin`, `limite_usuarios`, etc., para permitir futuras ampliaciones del modelo de negocio.

---


*   **Reportes (Exportación y Gráficos):**
    - **Exportación:**
        - **Excel (xlsx, csv):** Paquete `maatwebsite/excel`.
        - **PDF:** Paquete `barryvdh/laravel-dompdf` o `spatie/laravel-pdf`.
    - **Gráficos:**
        - **Sugerencia:** Utilizar una librería de JavaScript en el frontend como `Chart.js` o `ApexCharts`. El backend en Laravel solo proveerá los datos a través de endpoints de API.

*   **Impresión:**
    - La "impresión fiscal" o a impresoras de tickets se maneja desde el navegador. El backend generará un HTML con el formato correcto (ticket) y el frontend invocará el diálogo de impresión del navegador (`window.print()`).

*   **Extensibilidad Futura:**
    - La arquitectura de `modulo_id` y tablas prefijadas está diseñada para que agregar un nuevo módulo (ej: `taller_mecanico`) solo requiera:
        1.  Añadir un registro en la tabla `modulos`.
        2.  Crear las nuevas migraciones para las tablas del módulo.
        3.  Desarrollar los controladores, vistas y rutas correspondientes.
        4.  Asignar la licencia del nuevo módulo a los tenants.

## 6. GUÍA DE DESARROLLO Y REFERENCIA

*   **Gestión de la Base de Datos:** Toda la creación y modificación del esquema de la base de datos se realizará **exclusivamente a través de migraciones de Laravel**.
*   **Seguridad:** Se seguirán las mejores prácticas de seguridad de Laravel por defecto (protección CSRF, XSS, validación estricta, ORM para prevenir inyección SQL).

---

## 7. CRUD DEL SUPER-ADMIN

Se implementaron las funcionalidades completas de Crear, Leer, Actualizar y Eliminar (CRUD) para los recursos centrales que gestiona el Super-Admin.

### 7.1. Gestión de Módulos

*   **Controlador**: `app/Http/Controllers/Admin/ModuloController.php`
*   **Rutas**: Se utilizó `Route::resource('modulos', ModuloController::class)` en `routes/web.php` dentro del grupo del admin.
*   **Vistas**: Se crearon las vistas Blade en `resources/views/admin/modulos/`.
*   **Funcionalidad**: Permite al Super-Admin definir los módulos que estarán disponibles en el sistema.
*   **Funcionalidad Avanzada**:
    *   Se añadieron los campos `slug` y `route_name` para permitir la generación de enlaces dinámicos en la navegación.
    *   El formulario de creación/edición utiliza **Alpine.js** para generar automáticamente el `slug` a partir del nombre del módulo.

### 7.2. Gestión de Licencias

*   **Controlador**: `app/Http/Controllers/Admin/LicenciaController.php`
*   **Rutas**: Se habilitó el CRUD completo para las licencias.
*   **Vistas**: Se crearon las vistas en `resources/views/admin/licencias/`.
*   **Funcionalidad**: Permite al Super-Admin asignar módulos a los tenants, definiendo reglas clave como la fecha de expiración, el número máximo de usuarios permitidos y si la licencia está activa.
*   **Validación Clave**: Se implementó una regla de validación única (`Rule::unique`) para impedir que se asigne más de una licencia para el mismo módulo a un mismo tenant.

---

## 8. ARQUITECTURA DE TENANT

Se implementaron los pilares para el funcionamiento del sistema multi-tenant.

### 8.1. Middleware de Verificación de Licencia

*   **Archivo**: `app/Http/Middleware/CheckTenantLicense.php`.
*   **Registro**: Se registró con el alias `tenant.license` en `bootstrap/app.php`.
*   **Funcionamiento**: Este middleware se aplica a las rutas del panel del tenant. Verifica si el tenant del usuario autenticado tiene **al menos una licencia activa y vigente**. Si no la tiene, aborta la petición con un error 403 (Acceso denegado), impidiendo el acceso al panel.

## 9. PANEL DEL TENANT-ADMIN

Se desarrolló el panel de administración para los usuarios con el rol `Tenant-Admin`, dándoles autonomía para gestionar sus propios recursos.

### 9.1. Dashboard y Navegación Dinámica

*   **Dashboard**: Se creó un controlador (`app/Http/Controllers/Tenant/DashboardController.php`) y una vista (`resources/views/tenant/dashboard.blade.php`) para servir como página de inicio del tenant.
*   **Redirección al Login**: Se modificó `app/Http/Controllers/Auth/AuthenticatedSessionController.php` y el middleware `RedirectIfAuthenticated` para redirigir a los usuarios a su dashboard correspondiente (`admin.dashboard` o `tenant.dashboard`) según su rol.
*   **Navegación Dinámica**: Para mostrar solo los módulos licenciados en la barra de navegación, se utilizó un **View Composer**.
    *   **Composer**: `app/Http/View/Composers/LicensedModulesComposer.php` se encarga de consultar las licencias activas del tenant y pasar a la vista no solo los módulos, sino también el objeto `$user` y variables booleanas como `$isSuperAdmin` y `$isTenantAdmin` para ser usadas en la navegación.
    *   **Service Provider**: `app/Providers/ViewServiceProvider.php` registra el composer para que se ejecute en todas las vistas, asegurando que las variables estén siempre disponibles.
    *   **Vista de Navegación Principal**: La barra de navegación principal de la aplicación se encuentra dentro del layout de componente `resources/views/components/layouts/app.blade.php`. Este archivo es el responsable de renderizar los menús tanto para el `Super-Admin` como para el `Tenant-Admin`. Utiliza las variables pasadas por el composer para mostrar dinámicamente los módulos licenciados y determinar el rol del usuario. (Nota: El archivo `resources/views/layouts/navigation.blade.php` es un remanente de la instalación inicial y está pendiente de ser eliminado).

### 9.2. Gestión de Usuarios (CRUD)

*   **Controlador**: `app/Http/Controllers/Tenant/UserController.php`.
*   **Rutas**: Se añadió `Route::resource('users', ...)` al grupo de rutas del tenant.
*   **Vistas**: Se crearon las vistas correspondientes en `resources/views/tenant/users/`.
*   **Funcionalidad Clave (Límite de Licencia)**:
    1.  **Regla de Validación**: Se creó la regla personalizada `app/Rules/UserLimitPerTenant.php`.
    2.  **Lógica**: Esta regla consulta la tabla `licencias` para sumar el `limite_usuarios` permitido para el tenant y lo compara con el número actual de usuarios.
    3.  **Implementación**: Se aplica en el método `store` del `UserController`. Si el límite se ha alcanzado, la validación falla.
    4.  **UI/UX**: Las vistas muestran un contador de usuarios (ej: `5 / 10`) y deshabilitan el botón de creación si se alcanza el límite.

### 9.3. Gestión de Sucursales (CRUD)
*   **Migración**: Se creó la migración para la tabla `sucursales` con su respectiva relación a `tenants`. Se actualizó la tabla `users` para incluir una relación `sucursal_id`.
*   **Modelo**: `app/Models/Sucursal.php`, utilizando el trait `TenantScoped` para el aislamiento automático de datos.
*   **Controlador**: `app/Http/Controllers/Tenant/SucursalController.php` para manejar la lógica del CRUD.
*   **Rutas**: Se añadió `Route::resource('sucursales', ...)` al grupo de rutas del tenant.
*   **Vistas**: Se crearon las vistas CRUD en `resources/views/tenant/sucursales/`.
*   **Funcionalidad**: Permite a cada `Tenant-Admin` gestionar sus propias sucursales y asignar usuarios a ellas desde el CRUD de usuarios.

### 9.4. Panel de Configuración
*   **Propósito**: Permitir la personalización de la aplicación por cada tenant.
*   **Controladores**:
    *   `Tenant\ConfigurationController.php`: Gestiona la configuración para el tenant autenticado.
    *   `Admin\ConfigurationController.php`: Permite al Super-Admin seleccionar cualquier tenant y modificar su configuración. Utiliza `tenancy()->initialize($tenant)` para cambiar temporalmente el contexto y acceder a los recursos del tenant seleccionado (como las sucursales).
*   **Vistas**:
    *   Se creó un formulario parcial reutilizable en `resources/views/partials/configuration_form.blade.php`.
    *   Este parcial es incluido tanto por la vista del tenant (`tenant/configuration/index.blade.php`) como por la del admin (`admin/configuration/index.blade.php`).
*   **Funcionalidad**: Permite configurar logos, eslóganes, datos para impresión, redes sociales y colores de la interfaz, con opciones tanto generales como específicas por sucursal.

---
### 9.5. Gestión de Clientes (CRUD)
*   **Propósito**: Centralizar la gestión de todas las personas o empresas con las que interactúa un tenant.
*   **Modelo Unificado**: Se creó el modelo `Cliente` para representar tanto a pacientes de un consultorio como a clientes de un restaurante, evitando la duplicidad de datos.
*   **Controlador**: `Tenant\ClienteController.php` maneja la lógica del CRUD, validaciones y una función de búsqueda.
*   **Vistas**: Se crearon las vistas CRUD en `resources/views/tenant/clientes/`, utilizando un formulario parcial `_form.blade.php` para reutilizar código.
*   **Funcionalidad**: Permite al `Tenant-Admin` crear, listar, buscar, editar y eliminar clientes. Incluye una regla de negocio que impide eliminar un cliente si tiene documentos asociados. Se añadió una vista de detalle (`show`) para listar todos los documentos de un cliente específico.

### 9.6. Gestor de Documentos Transversal
*   **Propósito**: Crear un sistema centralizado para subir y gestionar archivos asociados a clientes, que pueda ser utilizado por cualquier módulo.
*   **Arquitectura Clave**: Se unificó el concepto de "paciente" y "cliente" en un único modelo `Cliente` (`app/Models/Cliente.php`) para evitar la duplicidad de datos y simplificar la lógica.
*   **Controlador**: `Tenant\DocumentUploadController.php` maneja la lógica de subida y la búsqueda de clientes vía AJAX.
*   **Vistas**: Se creó una vista en `tenant/documents/upload.blade.php` con un buscador de clientes dinámico para facilitar la selección.
*   **Almacenamiento y Gestión**: Sigue la arquitectura definida en la sección 11.2, guardando los archivos en la ruta segura y organizada: `storage/app/public/{tenant_id}/{modulo_slug}/cliente_{cliente_id}`. Se implementó la funcionalidad para eliminar tanto el registro en la base de datos como el archivo físico del disco.
*   **Resultado**: Se obtiene un módulo de gestión de documentos robusto, seguro, escalable y reutilizable para toda la aplicación.

### 9.7. Componentes de UI Reutilizables
*   **Mensajes Flash**: Se creó un parcial de Blade en `resources/views/partials/flash-messages.blade.php` para mostrar de forma consistente los mensajes de sesión (éxito, error, advertencia) y los errores de validación en toda la aplicación. Esto centraliza la lógica de las notificaciones y mantiene las vistas más limpias.

---

## 10. ARQUITECTURA MODULAR

Para garantizar la escalabilidad y la organización del código, el proyecto adopta una arquitectura modular. Cada módulo funcional (Facturación, Citas Médicas, etc.) se trata como una mini-aplicación autocontenida dentro del directorio `app/Modules`.

*   **Estructura de Directorios**: Cada módulo reside en `app/Modules/{NombreDelModulo}/` y contiene sus propios Controladores, Modelos, Servicios y un archivo de rutas.
*   **Vistas**: Las vistas de cada módulo se encuentran en `resources/views/tenant/modules/{nombre-del-modulo}`.
*   **Service Providers**: Cada módulo tiene su propio `ServiceProvider` (ej: `FacturacionServiceProvider.php`). Este provider es el responsable de:
    1.  **Registrar las Rutas**: Envuelve las rutas del módulo (`app/Modules/{...}/Routes/web.php`) dentro de los middlewares (`web`, `auth`, `role:Tenant-Admin`) y prefijos (`/tenant`) necesarios. Esto mantiene el módulo autocontenido.
    2.  **Registrar las Vistas**: Carga las vistas del módulo (`resources/views/tenant/modules/{...}`) y les asigna un namespace (ej: `facturacion::`). Esto evita conflictos de nombres y clarifica de dónde viene cada vista.
*   **Registro**: El `ServiceProvider` de cada módulo se registra en la sección `providers` de `bootstrap/app.php` para que Laravel lo cargue al iniciar la aplicación.
*   **Registro**: El `ServiceProvider` de cada módulo se registra en el archivo `bootstrap/providers.php`. Este archivo es cargado automáticamente por la aplicación, manteniendo la configuración limpia y centralizada.
*   **Ventajas**: Esta separación facilita el desarrollo, la depuración y la posibilidad de activar o desactivar módulos completos simplemente comentando su `ServiceProvider`.

---
## 10.1. Sistema de Logging Modular
*   **Propósito**: Centralizar y organizar el registro de errores y eventos de la aplicación, separando los logs por cada módulo funcional.
*   **Arquitectura**:
    *   **Canales de Log**: En `config/logging.php`, se han definido "canales" de log específicos para cada módulo (ej: `facturacion`, `medico`). Cada canal apunta a su propio archivo de log (ej: `storage/logs/facturacion/facturacion.log`).
    *   **Servicio Centralizado**: Se creó el servicio `App\Modules\Services\ModuleLoggerService`. Este servicio actúa como una única puerta de entrada para registrar eventos, simplificando su uso en los controladores.
    *   **Uso**: En lugar de usar `Log::error()`, los controladores inyectan `ModuleLoggerService` y lo utilizan para registrar errores en el canal correspondiente (ej: `$logger->error('facturacion', 'Mensaje de error')`).
*   **Ventajas**:
    *   **Organización**: Facilita la depuración al tener los errores de cada módulo en archivos separados.
    *   **Mantenibilidad**: El código es más limpio y la lógica de logging está centralizada.
    *   **Robustez**: Si se intenta usar un canal no definido, el sistema no falla, sino que registra el error en el log principal por defecto.
---

## 10. MÓDULOS IMPLEMENTADOS

### 10.1. Módulo de Facturación V4
*   **Propósito**: Proporciona una solución completa para la emisión y gestión de Comprobantes Fiscales Digitales por Internet (CFDI) en su versión 4.0, incluyendo complementos de pago.
*   **Arquitectura**:
    *   **API-First**: El módulo se basa en un enfoque "API-First". El frontend (vistas de Blade) es ligero y consume una API interna para obtener todos los datos necesarios de forma asíncrona.
    *   **Servicio de Catálogos (Optimizado)**: Se creó `App\Modules\Facturacion\Services\SatCatalogService` para centralizar el acceso a los catálogos del SAT. **Este servicio fue refactorizado para consultar una base de datos dedicada (`sat_*` tables) en lugar de procesar archivos Excel en cada petición.** Esta optimización resulta en una carga casi instantánea de los catálogos en el frontend.
    *   **API de Catálogos**: El controlador `App\Modules\Facturacion\Http\Controllers\Api\CatalogosApiController` expone los catálogos del SAT y otras búsquedas (como clientes) a través de endpoints seguros y protegidos por el middleware del tenant.
    *   **Estructura de Controladores**: Para una mejor organización y escalabilidad, los controladores se han agrupado en subdirectorios temáticos: `app/Modules/Facturacion/Http/Controllers/Cfdi_40/` para la facturación general, `.../Pago/` para complementos de pago, y `.../Configuracion/` para la gestión de PACs, series y datos fiscales.
    *   **Frontend Dinámico**: La vista de creación de CFDI (`facturacion::cfdis.create`) utiliza JavaScript para llamar a la API y poblar dinámicamente los campos `<select>` (Formas de Pago, Métodos de Pago, Uso de CFDI, etc.) y para implementar buscadores avanzados con Tom-Select (para Clientes y Productos/Servicios).
    *   **Seguridad de Credenciales**: Los datos sensibles como los Certificados de Sello Digital (CSD) y las credenciales del PAC se gestionan en la sección de "Configuración" del módulo y se almacenan de forma segura.
*   **Funcionalidades Implementadas**:
    *   CRUD para Datos Fiscales del emisor (CSD).
    *   CRUD para Proveedores de Timbrado (PACs).
    *   CRUD para Series y Folios.
    *   Formulario de creación de CFDI con carga dinámica de catálogos y búsqueda de clientes.
    *   Menú de navegación reorganizado y específico para el módulo.
*   **Tablas de Base de Datos**: `facturacion_cfdi`, `facturacion_pagos`, `facturacion_series_folios`, `facturacion_datos_fiscales`, `facturacion_pacs`.

---

## 11. ARQUITECTURA DE ALMACENAMIENTO DE ARCHIVOS

La gestión de archivos se divide en dos categorías para mantener la organización, seguridad y escalabilidad.

### 11.1. Recursos Estáticos de Módulos
*   **Propósito**: Almacenar archivos necesarios para el funcionamiento de un módulo que no son generados por el usuario y no deben ser públicamente accesibles vía URL.
*   **Ejemplos**: Plantillas de documentos, archivos de configuración iniciales, etc. **Nota: Los catálogos del SAT en formato `.xlsx` han sido migrados a una base de datos y ya no se utilizan directamente desde el almacenamiento.**
*   **Ubicación**: `storage/app/modules/{nombre-del-modulo}/...`
*   **Acceso en Código**: Se utiliza el Facade `Storage` de Laravel. Ejemplo: `Storage::path('modules/facturacion/catalogs/c_UsoCFDI.xlsx');`

### 11.2. Archivos Dinámicos Subidos por Usuarios
*   **Propósito**: Almacenar archivos generados por la interacción del usuario con la aplicación.
*   **Ejemplos**: Documentos de pacientes (PDFs, imágenes de rayos X) subidos por un doctor en el módulo de Citas Médicas, fotos de productos en un módulo de inventario, etc.
*   **Ubicación**: `storage/app/public/{tenant_id}/{nombre-del-modulo}/...`
    *   La subcarpeta `{tenant_id}` es **crucial** para el aislamiento de datos entre tenants.
*   **Acceso Público**: Se debe ejecutar `php artisan storage:link` una vez para crear un enlace simbólico desde `public/storage` a `storage/app/public`.
*   **Acceso en Código**:
    *   **Guardado**: `Storage::disk('public')->put($ruta, $contenido);`
    *   **Generación de URL**: `Storage::url($ruta_guardada_en_db);`
