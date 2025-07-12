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