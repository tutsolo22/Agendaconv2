<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            Nueva API Key para Aplicación: {{ $application->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Crear Nueva API Key
        </div>
        <div class="card-body">
            <p>Aquí se mostrará el formulario para crear una nueva API Key para la aplicación {{ $application->name }}.</p>
        </div>
    </div>
</x-layouts.app>
