@csrf

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="nombre" class="form-label">{{ __('Nombre de la Sucursal') }}</label>
            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $sucursal->nombre ?? '') }}" required autofocus>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="telefono" class="form-label">{{ __('Teléfono') }}</label>
            <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $sucursal->telefono ?? '') }}">
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="direccion" class="form-label">{{ __('Dirección') }}</label>
    <textarea class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" rows="3">{{ old('direccion', $sucursal->direccion ?? '') }}</textarea>
    @error('direccion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mt-4">
    <a href="{{ route('tenant.sucursales.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
</div>