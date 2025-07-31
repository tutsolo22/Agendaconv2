<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Usuario') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tenant.users.update', $user) }}">
                @method('PUT')
                @include('tenant.users._form', ['submitText' => __('Actualizar Usuario')])
            </form>
        </div>
    </div>
</x-layouts.app>