<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            API Keys para Aplicación: {{ $application->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Listado de API Keys
        </div>
        <div class="card-body">
            <p>Aquí se mostrará el listado de API Keys para la aplicación {{ $application->name }}.</p>
        </div>
    </div>
</x-layouts.app>
