<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nuevo Tenant') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.tenants.store') }}">
                @include('admin.tenants._form', ['submitText' => __('Crear Tenant')])
            </form>
        </div>
    </div>
</x-layouts.app>