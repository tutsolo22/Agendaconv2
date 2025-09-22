# Plan de Avance: Módulo de Salud

A continuación se presenta el plan de acción detallado para la creación del módulo. Se pueden marcar las casillas para llevar un registro del progreso.

## Fase 1: Estructura y Configuración del Módulo

- [x] **Crear Directorios Base** (Realizado por el usuario)
  - [x] Directorio de Backend: `app/Modules/Salud/` con subcarpetas `Http/Controllers`, `Models`, `Services`, `Routes`.
  - [x] Directorio de Vistas: `resources/views/tenant/modules/salud`.
  - [x] Directorio de Scripts: `resources/js/Modules/Salud`.
- [x] **Crear Service Provider**
  - [x] Crear el archivo `app/Modules/Salud/SaludServiceProvider.php`.
  - [x] Configurar el provider para que registre las rutas del módulo (con prefijos y middlewares) y las vistas (con el namespace `salud::`).
- [x] **Registrar Service Provider**
  - [x] Añadir la clase `App\Modules\Salud\SaludServiceProvider::class` al archivo `bootstrap/providers.php`.
- [x] **Configurar Logging**
  - [x] Añadir un nuevo canal de log llamado `salud` en `config/logging.php` que apunte al archivo `storage/logs/salud/salud.log`.

## Fase 2: Base de Datos y Migraciones

- [ ] **Sub-módulo: Citas y Consultas**
  - [ ] Crear migración para `salud_profesionistas`.
  - [ ] Crear migración para `salud_servicios`.
  - [ ] Crear migración para `salud_horarios`.
  - [ ] Crear migración para `salud_citas`.
- [ ] **Sub-módulo: Historial Clínico**
  - [ ] Crear migración para `salud_historiales_clinicos`.
  - [ ] Crear migración para `salud_historial_entradas`.
  - [ ] Crear migración para `salud_signos_vitales`.
- [ ] **Sub-módulo: Hospitalización**
  - [ ] Crear migración para `salud_cuartos`.
  - [ ] Crear migración para `salud_camas`.
  - [ ] Crear migración para `salud_hospitalizaciones`.

## Fase 3: Lógica de Backend

- [ ] **Crear Modelos Eloquent**
  - [ ] Crear modelos para todas las tablas de la Fase 2 en `app/Modules/Salud/Models/`.
  - [ ] Asegurar que todos los modelos usen el trait `TenantScoped`.
- [ ] **Implementar Servicios de Negocio**
  - [ ] Crear `AgendaService` para la lógica de citas.
  - [ ] Crear `HistorialService` para la lógica del historial clínico.
  - [ ] Crear `HospitalizacionService` para la lógica de camas e ingresos.
- [ ] **Implementar Controladores y CRUDs**
  - [ ] Crear controladores para cada entidad (Profesionistas, Citas, Historial, etc.).
  - [ ] Implementar métodos CRUD y validación con Form Requests.
- [ ] **Implementar Licenciamiento de Hospitalización**
  - [ ] Añadir lógica al `SaludServiceProvider` o a un nuevo middleware para verificar la licencia de "hospitalizacion".
  - [ ] Asegurar que las rutas y vistas de hospitalización no se registren/muestren si la licencia no está activa.

## Fase 4: Lógica de Frontend

- [ ] **Desarrollar Vistas CRUD**
  - [ ] Crear vistas Blade para la gestión de Profesionistas, Servicios, Horarios.
- [ ] **Implementar Calendario de Citas**
  - [ ] Crear API interna para servir los datos de las citas.
  - [ ] Desarrollar la vista con un calendario interactivo (FullCalendar).
- [ ] **Implementar Interfaz de Historial Clínico**
  - [ ] Desarrollar la vista para la visualización del historial del paciente.
  - [ ] Crear el formulario para añadir nuevas entradas y registrar signos vitales.
- [ ] **Implementar Panel de Hospitalización**
  - [ ] Crear API interna para servir el estado de ocupación.
  - [ ] Desarrollar el dashboard visual de cuartos y camas.
  - [ ] Crear las vistas para gestionar ingresos y egresos.

## Fase 5: Funcionalidades Transversales y Despliegue

- [ ] **Definir Roles y Permisos**
  - [ ] Configurar roles (`Doctor`, `Recepcionista`, etc.) con `spatie/laravel-permission`.
  - [ ] Proteger rutas y acciones con los permisos adecuados.
- [ ] **Integrar Gestor de Archivos**
  - [ ] Conectar la subida de documentos del paciente con el módulo de Salud.
- [ ] **Configurar Auditoría**
  - [ ] Habilitar `spatie/laravel-activitylog` para los modelos del módulo.
- [ ] **Pruebas y Refinamiento**
  - [ ] Realizar pruebas funcionales de todos los sub-módulos.
- [ ] **Migración de Datos (Opcional)**
  - [ ] Si es necesario, crear y ejecutar scripts para migrar datos del sistema anterior.

# Módulo de Facturación - Carta Porte

## Resumen de Tareas (21 de Septiembre de 2025)

- **Implementación de la funcionalidad de "Guardar Borrador"**: Se añadió el código JavaScript necesario para que el botón "Guardar Borrador" en el formulario de creación de Carta Porte funcione correctamente. Esto permite a los usuarios guardar su progreso sin necesidad de validar o timbrar el documento.
- **Compilación de assets**: Se ejecutó el comando `npm run build` para compilar los archivos JavaScript y CSS, asegurando que los cambios se aplicaran en la aplicación.
- **Eliminación de un archivo innecesario**: Se borró el archivo `create-global.blade.php` de la raíz del proyecto, ya que no era necesario.
- **Corrección del seeder de Carta Porte**: El seeder (`CartaPorteSeeder`) estaba fallando por dos razones:
    - **Lógica incorrecta**: La lógica para crear las mercancías era incorrecta. Se corrigió para que se ajuste a la estructura de la base de datos (CartaPorte -> Mercancias -> Mercancia).
    - **Longitud de columna incorrecta**: El valor para el campo `perm_sct` era demasiado largo para la columna en la base de datos. Se creó y aplicó una nueva migración para aumentar la longitud de esta columna en la tabla `cp_autotransporte`.
- **Ejecución de migraciones y seeders**: Se aplicaron los cambios en la base de datos ejecutando las migraciones y luego se ejecutó el seeder de Carta Porte para poblar la base de datos con los datos iniciales.
- **Confirmación de los cambios (commits)**: Se guardaron todos los cambios en el repositorio de Git a través de dos commits, uno para la nueva funcionalidad y otro para la corrección del seeder.