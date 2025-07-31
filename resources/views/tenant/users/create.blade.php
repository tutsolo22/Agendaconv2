<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nuevo Usuario') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.users.store') }}">
                @include('tenant.users._form', ['submitText' => __('Crear Usuario')])
            </form>
        </div>
    </div>
</x-layouts.app>