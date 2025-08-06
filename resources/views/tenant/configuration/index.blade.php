<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Panel de Configuración') }}
        </h2>
    </x-slot>

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.configuration.update') }}" method="POST" enctype="multipart/form-data">
                @include('partials.configuration_form')
            </form>
        </div>
    </div>
</x-layouts.app>