<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            Nuevo Webhook para Aplicación: {{ $application->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Crear Nuevo Webhook
        </div>
        <div class="card-body">
            <p>Aquí se mostrará el formulario para crear un nuevo Webhook para la aplicación {{ $application->name }}.</p>
        </div>
    </div>
</x-layouts.app>
