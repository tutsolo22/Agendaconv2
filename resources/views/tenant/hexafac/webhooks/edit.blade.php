<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            Editar Webhook para Aplicación: {{ $application->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Editar Webhook
        </div>
        <div class="card-body">
            <p>Aquí se mostrará el formulario para editar un Webhook para la aplicación {{ $application->name }}.</p>
        </div>
    </div>
</x-layouts.app>
