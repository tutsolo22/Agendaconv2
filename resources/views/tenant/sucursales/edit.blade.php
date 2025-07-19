<x-layouts.admin>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Editar Sucursal') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('tenant.sucursales.update', $sucursal) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Nombre -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $sucursal->nombre) }}" required autofocus>
                    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Dirección -->
                <div class="mb-3">
                    <label for="direccion" class="form-label">{{ __('Dirección') }}</label>
                    <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $sucursal->direccion) }}">
                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Teléfono -->
                <div class="mb-3">
                    <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
                    <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $sucursal->telefono) }}">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <!-- Estado -->
                <div class="mb-3">
                    <label for="is_active" class="form-label">{{ __('Estado') }}</label>
                    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
                        <option value="1" {{ old('is_active', $sucursal->is_active) == 1 ? 'selected' : '' }}>{{ __('Activa') }}</option>
                        <option value="0" {{ old('is_active', $sucursal->is_active) == 0 ? 'selected' : '' }}>{{ __('Inactiva') }}</option>
                    </select>
                    @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('tenant.sucursales.index') }}" class="btn btn-secondary me-2">{{ __('Cancelar') }}</a>
                    <button type="submit" class="btn btn-primary">{{ __('Actualizar Sucursal') }}</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>