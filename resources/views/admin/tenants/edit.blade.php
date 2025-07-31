<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Tenant') }}: {{ $tenant->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}">
                @method('PUT')
                @include('admin.tenants._form', ['submitText' => __('Actualizar Tenant')])
            </form>
        </div>
    </div>
</x-layouts.app>