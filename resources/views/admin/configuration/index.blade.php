<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Configuración de Tenants') }}
        </h2>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Seleccionar Tenant</h5>
            <form action="{{ route('admin.configuration.index') }}" method="GET">
                <div class="input-group">
                    <select name="tenant_id" class="form-select">
                        <option value="">Seleccione un tenant para configurar...</option>
                        @foreach($tenants as $tenantOption)
                            <option value="{{ $tenantOption->id }}" @selected($selectedTenant && $selectedTenant->id == $tenantOption->id)>
                                {{ $tenantOption->name }} (ID: {{ $tenantOption->id }})
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-outline-secondary" type="submit">Cargar Configuración</button>
                </div>
            </form>
        </div>
    </div>

    @if($selectedTenant)
        <div class="card">
            <div class="card-header">
                Configuración para: <strong>{{ $selectedTenant->name }}</strong>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.configuration.update', $selectedTenant) }}" method="POST" enctype="multipart/form-data">
                    @include('partials.configuration_form')
                </form>
            </div>
        </div>
    @endif
</x-layouts.app>