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
