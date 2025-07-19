# Documentación del Proyecto - Agendacon v2

Este documento detalla el proceso de instalación, configuración y puesta en marcha del proyecto Agendacon v2, construido sobre Laravel 12.

---

## 1. Configuración del Entorno de Desarrollo (Windows)

Para poder ejecutar herramientas de desarrollo como `npm` en la terminal de Windows (PowerShell), es necesario ajustar la política de ejecución de scripts.

1.  **Abrir PowerShell como Administrador**:
    *   Buscar "PowerShell" en el menú de inicio, clic derecho y "Ejecutar como administrador".

2.  **Establecer la Política de Ejecución**:
    *   Ejecutar el siguiente comando para permitir la ejecución de scripts locales firmados remotamente.

    ```powershell
    Set-ExecutionPolicy RemoteSigned
    ```
    *   Confirmar la acción presionando `Y` (o `S`) y Enter.

---

## 2. Instalación y Configuración del Proyecto

### 2.1. Instalación de Laravel

El proyecto se inicializó utilizando Composer con la última versión de Laravel (v12).

```shell
composer create-project laravel/laravel Agendaconv2
```

### 2.2. Configuración del Archivo `.env`

Se realizaron ajustes clave en el archivo de entorno `.env` para personalizar la aplicación:

*   **Nombre de la Aplicación**: Se cambió el nombre por defecto a "Agendacon".
    ```dotenv
    APP_NAME=Agendacon
    ```

*   **Localización en Español**: Se configuró la aplicación para que utilice el idioma español en sus componentes.
    ```dotenv
    APP_LOCALE=es
    APP_FALLBACK_LOCALE=es
    APP_FAKER_LOCALE=es_ES
    ```

*   **Conexión a la Base de Datos**: Se definieron los parámetros para la conexión con la base de datos local en XAMPP.
    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=agendaconv2_db
    DB_USERNAME=root
    DB_PASSWORD=
    ```

### 2.3. Creación de la Base de Datos

Se creó la base de datos `agendaconv2_db` manualmente a través de **phpMyAdmin**, utilizando el cotejamiento `utf8mb4_unicode_ci` para una correcta compatibilidad de caracteres.

---

## 3. Configuración del Frontend (Vite + Bootstrap 5)

Se reemplazó la configuración por defecto de Tailwind CSS por Bootstrap 5 y Font Awesome 6.

### 3.1. Instalación de Dependencias

Se instalaron las librerías necesarias a través de `npm`:

```shell
# Instalar Bootstrap y su dependencia Popper.js
npm install bootstrap @popperjs/core

# Instalar SASS (para compilar los estilos de Bootstrap) y Font Awesome
npm install --save-dev sass @fortawesome/fontawesome-free
```

### 3.2. Configuración de Archivos de Assets

1.  **`vite.config.js`**: Se actualizó el archivo de configuración de Vite para que apunte a los nuevos archivos de assets.

2.  **`resources/scss/app.scss`**: Se creó este archivo para importar los estilos de Bootstrap y Font Awesome.

3.  **`resources/js/app.js`**: Se modificó para importar el JavaScript de Bootstrap, necesario para sus componentes interactivos.

### 3.3. Actualización de la Vista Principal

Se modificó la vista `resources/views/welcome.blade.php` para:
1.  Incluir la directiva `@vite()` que carga los assets compilados.
2.  Añadir un código de prueba con clases de Bootstrap y un icono de Font Awesome para verificar que la configuración era correcta.

---

## 4. Ejecución del Proyecto

Para trabajar en el proyecto, se deben ejecutar dos servidores de desarrollo en terminales separadas:

1.  **Servidor de Backend (Laravel)**:
    ```shell
    php artisan serve
    ```

2.  **Servidor de Frontend (Vite)**:
    ```shell
    npm run dev
    ```

La aplicación es accesible en la URL proporcionada por `php artisan serve` (generalmente `http://127.0.0.1:8000`).

---

## 5. Control de Versiones y Dependencias Adicionales

### 5.1. Inicialización de Git

Se inicializó un repositorio de Git para el control de versiones del proyecto y se realizó el primer commit para guardar el estado inicial de la configuración.

```shell
# Inicializar el repositorio
git init

# Añadir todos los archivos
git add .

# Realizar el primer commit
git commit -m "Initial commit: Laravel 12, DB, and frontend setup"
```

### 5.2. Instalación de Paquetes de Spatie

Se instalaron dos paquetes clave de Spatie para funcionalidades avanzadas:

```shell
# Para la gestión de Roles y Permisos
composer require spatie/laravel-permission

# Para el registro de actividad y auditoría
composer require spatie/laravel-activitylog
```
## 6. Estructura de la Base de Datos (Fase 1)

Se crearon los archivos de migración para definir las tablas centrales del sistema, siguiendo el plan de arquitectura.

*   `create_tenants_table`: Para las empresas o clientes.
*   `create_modulos_table`: Para los módulos del sistema (Restaurante, Facturación, etc.).
*   `add_custom_fields_to_users_table`: Para añadir la relación con `tenants` y el rol de Super-Admin.
*   `create_licencias_table`: Para gestionar las licencias de los tenants.
*   `create_licencia_historial_table`: Para auditar los cambios en las licencias.

Finalmente, se ejecutó el comando `php artisan migrate:fresh` para eliminar cualquier tabla preexistente y crear toda la estructura de la base de datos de forma limpia y ordenada, incluyendo las tablas por defecto de Laravel (`users`, `sessions`, etc.) y las nuevas tablas del sistema.

---

## 7. Implementación de Roles y Permisos

Para gestionar el acceso de los usuarios a las diferentes partes del sistema, se configuró el paquete `spatie/laravel-permission`.

1.  **Publicar Archivos del Paquete**: Se publicó el archivo de migración y el archivo de configuración del paquete.
    ```shell
    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    ```
2.  **Ejecutar Migración**: Se ejecutó la nueva migración para crear las tablas `roles`, `permissions`, y las tablas pivote correspondientes.
    ```shell
    php artisan migrate
    ```
3.  **Configurar Modelo User**: Se añadió el trait `HasRoles` al modelo `app/Models/User.php` para conectarlo con el sistema de permisos. Este paso se realizó durante la creación inicial de los modelos.
---

## 8. CRUD del Super-Admin

Se implementaron las funcionalidades completas de Crear, Leer, Actualizar y Eliminar (CRUD) para los recursos centrales que gestiona el Super-Admin.

### 8.1. Gestión de Módulos

*   **Controlador**: `app/Http/Controllers/Admin/ModuloController.php`
*   **Rutas**: Se utilizó `Route::resource('modulos', ModuloController::class)` en `routes/web.php` dentro del grupo del admin.
*   **Vistas**: Se crearon las vistas Blade en `resources/views/admin/modulos/` para el listado (`index`), creación (`create`) y edición (`edit`) de módulos.
*   **Funcionalidad**: Permite al Super-Admin definir los módulos que estarán disponibles en el sistema, especificando su nombre, descripción, icono y estado.

### 8.2. Gestión de Licencias

*   **Controlador**: `app/Http/Controllers/Admin/LicenciaController.php`
*   **Rutas**: Se habilitó el CRUD completo para las licencias.
*   **Vistas**: Se crearon las vistas en `resources/views/admin/licencias/` para listar, crear y editar las licencias.
*   **Funcionalidad**: Permite al Super-Admin asignar módulos a los tenants, definiendo reglas clave como la fecha de expiración, el número máximo de usuarios permitidos y si la licencia está activa.
*   **Validación Clave**: Se implementó una regla de validación única (`Rule::unique`) para impedir que se asigne más de una licencia para el mismo módulo a un mismo tenant.

---

## 9. Arquitectura de Tenant (Fase 2)

Se implementaron los pilares para el funcionamiento del sistema multi-tenant, garantizando la seguridad y el aislamiento de los datos.

### 9.1. Middleware de Verificación de Licencia

*   **Archivo**: `app/Http/Middleware/CheckTenantLicense.php`
*   **Registro**: Se registró con el alias `tenant.license` en `bootstrap/app.php`.
*   **Funcionamiento**:
    1.  Intercepta las peticiones a rutas de módulos específicos (ej: `/tenant/module/Citas Medicas`).
    2.  Verifica en la tabla `licencias` si el tenant del usuario autenticado tiene una licencia para ese módulo.
    3.  Valida que la licencia esté activa y que la fecha de expiración no haya pasado.
    4.  Si alguna condición falla, aborta la petición con un error 403 (Acceso denegado). De lo contrario, permite el acceso.
*   **Uso**: Se aplica a las rutas de los módulos de la siguiente manera:
    ```php
    Route::get('/module/{moduleSlug}', ...)->middleware('tenant.license:{moduleSlug}');
    ```

### 9.2. Aislamiento Automático de Datos (Global Scope)

Para garantizar que un tenant solo pueda acceder a sus propios datos, se implementó un Global Scope.

*   **Scope**: `app/Models/Scopes/TenantScope.php`. Su lógica añade automáticamente la condición `WHERE tenant_id = ?` a todas las consultas de los modelos que lo usan, excepto para el Super-Admin.
*   **Trait**: `app/Traits/TenantScoped.php`. Este trait facilita la aplicación del `TenantScope` y, muy importante, **asigna automáticamente el `tenant_id` del usuario autenticado al crear un nuevo registro**.
*   **Aplicación**: Se añadió el trait `TenantScoped` a los modelos que pertenecen a un tenant, como `User`, `Sucursal` y `Licencia`.
    ```php
    use HasFactory, Notifiable, HasRoles, TenantScoped;
    ```

---

## 10. Panel del Tenant-Admin

Se desarrolló el panel de administración para los usuarios con el rol `Tenant-Admin`, dándoles autonomía para gestionar sus propios recursos.

### 10.1. Dashboard y Navegación Dinámica

*   **Dashboard**: Se creó un controlador (`app/Http/Controllers/Tenant/DashboardController.php`) y una vista (`resources/views/tenant/dashboard.blade.php`) para servir como página de inicio del tenant.
*   **Redirección al Login**: Se modificó `app/Http/Controllers/Auth/AuthenticatedSessionController.php` para redirigir a los usuarios con el rol `Tenant-Admin` a su dashboard (`/tenant/dashboard`) después de iniciar sesión.
*   **Navegación Dinámica**: Para mostrar solo los módulos licenciados en la barra de navegación, se utilizó un **View Composer**.
    *   **Composer**: `app/Http/View/Composers/LicensedModulesComposer.php` se encarga de consultar las licencias activas del tenant y pasar los módulos correspondientes a la vista.
    *   **Service Provider**: `app/Providers/ViewServiceProvider.php` registra el composer para que se ejecute cada vez que se renderiza la vista de navegación.
    *   **Vista**: `resources/views/components/layouts/admin-navigation.blade.php` fue actualizada para iterar sobre la variable `$licensedModules` y generar los enlaces del menú.

### 10.2. Gestión de Usuarios (CRUD)

*   **Controlador**: `app/Http/Controllers/Tenant/UserController.php`.
*   **Rutas**: Se añadió `Route::resource('users', TenantUserController::class)` al grupo de rutas del tenant.
*   **Vistas**: Se crearon las vistas correspondientes en `resources/views/tenant/users/`.
*   **Funcionalidad Clave (Límite de Licencia)**:
    1.  **Regla de Validación**: Se creó la regla personalizada `app/Rules/UserLimitPerTenant.php`.
    2.  **Lógica**: Esta regla consulta la tabla `licencias` para encontrar el `max_usuarios` permitido para el tenant y lo compara con el número actual de usuarios.
    3.  **Implementación**: Se aplica en el método `store` del `UserController`. Si el límite se ha alcanzado, la validación falla y se impide la creación del nuevo usuario.
    4.  **UI/UX**: Las vistas se modificaron para mostrar un contador de usuarios (ej: `5 / 10`) y deshabilitar el botón de creación si se alcanza el límite.

### 10.3. Gestión de Sucursales (CRUD)

*   **Migración**: Se creó la migración para la tabla `sucursales` con su respectiva relación a `tenants`.
*   **Modelo**: `app/Models/Sucursal.php`, utilizando el trait `TenantScoped` para el aislamiento de datos.
*   **Controlador**: `app/Http/Controllers/Tenant/SucursalController.php`.
*   **Rutas**: Se añadió `Route::resource('sucursales', SucursalController::class)` al grupo de rutas del tenant.
*   **Vistas**: Se crearon las vistas CRUD en `resources/views/tenant/sucursales/`.
*   **Funcionalidad**: Permite a cada `Tenant-Admin` gestionar sus propias ubicaciones o sucursales de forma segura y aislada.

