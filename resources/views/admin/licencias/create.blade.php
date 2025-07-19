<x-layouts.admin>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nueva Licencia') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.licencias.store') }}" method="POST">
                @csrf

                <!-- Tenant -->
                <div class="mb-3">
                    <label for="tenant_id" class="form-label">{{ __('Tenant') }}</label>
                    <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" required>
                        <option selected disabled>{{ __('Seleccione un Tenant') }}</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                {{ $tenant->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tenant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Módulo -->
                <div class="mb-3">
                    <label for="modulo_id" class="form-label">{{ __('Módulo') }}</label>
                    <select class="form-select @error('modulo_id') is-invalid @enderror" id="modulo_id" name="modulo_id" required>
                        <option selected disabled>{{ __('Seleccione un Módulo') }}</option>
                        @foreach ($modulos as $modulo)
                            <option value="{{ $modulo->id }}" {{ old('modulo_id') == $modulo->id ? 'selected' : '' }}>
                                {{ $modulo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('modulo_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fecha de Expiración -->
                <div class="mb-3">
                    <label for="fecha_expiracion" class="form-label">{{ __('Fecha de Expiración') }}</label>
                    <input type="date" class="form-control @error('fecha_expiracion') is-invalid @enderror" id="fecha_expiracion" name="fecha_expiracion" value="{{ old('fecha_expiracion') }}" required>
                    @error('fecha_expiracion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Máximo de Usuarios -->
                <div class="mb-3">
                    <label for="max_usuarios" class="form-label">{{ __('Máximo de Usuarios') }}</label>
                    <input type="number" class="form-control @error('max_usuarios') is-invalid @enderror" id="max_usuarios" name="max_usuarios" value="{{ old('max_usuarios', 1) }}" min="1" required>
                    @error('max_usuarios')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Estado -->
                <div class="mb-3">
                    <label for="is_active" class="form-label">{{ __('Estado') }}</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>{{ __('Activa') }}</option>
                        <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>{{ __('Inactiva') }}</option>
                    </select>
                    @error('is_active')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.licencias.index') }}" class="btn btn-secondary me-2">{{ __('Cancelar') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Crear Licencia') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>