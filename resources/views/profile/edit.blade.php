<x-layouts.admin>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Editar Tenant') }}
            </h2>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> {{ __('Volver al Listado') }}
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            Formulario de Edición de Tenant
        </div>
        <div class="card-body">
            {{-- El método es POST, pero usamos @method('PUT') para que Laravel lo trate como una petición PUT/PATCH --}}
            <form action="{{ route('admin.tenants.update', $tenant) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nombre del Tenant</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="id" class="form-label">ID (Subdominio)</label>
                    <input type="text" class="form-control @error('id') is-invalid @enderror" id="id" name="id" value="{{ old('id', $tenant->id) }}" required>
                    @error('id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Este será el subdominio para el tenant (ej: `mi-empresa`.sudominio.com). Solo letras minúsculas, números y guiones.
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> {{ __('Actualizar Tenant') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>