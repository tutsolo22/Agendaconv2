<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid {{ $module->icono ?? 'fa-cube' }} fa-fw me-2"></i>
            {{ $module->nombre }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">¡Bienvenido al módulo {{ $module->nombre }}!</h5>
            <p class="card-text">
                Aquí se mostrará el contenido principal y las funcionalidades de este módulo.
            </p>
        </div>
    </div>
</x-layouts.app>