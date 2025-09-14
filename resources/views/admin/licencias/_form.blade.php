@csrf

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="tenant_id" class="form-label">{{ __('Tenant') }}</label>
            <select class="form-select @error('tenant_id') is-invalid @enderror" id="tenant_id" name="tenant_id" required>
                <option value="" disabled selected>{{ __('Seleccione un Tenant') }}</option>
                @foreach($tenants as $tenant)
                    <option value="{{ $tenant->id }}" @selected(old('tenant_id', $licencia->tenant_id ?? request('tenant_id')) == $tenant->id)>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
            @error('tenant_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="modulo_id" class="form-label">{{ __('Módulo') }}</label>
            <select class="form-select @error('modulo_id') is-invalid @enderror" id="modulo_id" name="modulo_id" required>
                <option value="" disabled selected>{{ __('Seleccione un módulo...') }}</option>
                @foreach($modulos as $modulo)
                    <option value="{{ $modulo->id }}" @selected(old('modulo_id', $licencia->modulo_id ?? '') == $modulo->id)>
                        {{ $modulo->nombre }}
                    </option>
                @endforeach
            </select>
            @error('modulo_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="fecha_fin" class="form-label">{{ __('Fecha de Expiración') }}</label>
            <input type="date" class="form-control @error('fecha_fin') is-invalid @enderror" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin', isset($licencia) ? \Carbon\Carbon::parse($licencia->fecha_fin)->format('Y-m-d') : '') }}" required>
            @error('fecha_fin')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="limite_usuarios" class="form-label">{{ __('Límite de Usuarios') }}</label>
            <input type="number" class="form-control @error('limite_usuarios') is-invalid @enderror" id="limite_usuarios" name="limite_usuarios" value="{{ old('limite_usuarios', $licencia->limite_usuarios ?? 1) }}" required min="1">
            @error('limite_usuarios')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $licencia->is_active ?? true))>
    <label class="form-check-label" for="is_active">{{ __('Licencia Activa') }}</label>
</div>

<div class="mt-4">
    <a href="{{ route('admin.licencias.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
</div>