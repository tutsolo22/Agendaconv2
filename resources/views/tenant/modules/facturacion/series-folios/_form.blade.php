@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="serie" class="form-label">Serie <span class="text-danger">*</span></label>
        <input type="text" name="serie" id="serie" class="form-control @error('serie') is-invalid @enderror" value="{{ old('serie', $serie->serie) }}" required maxlength="10" style="text-transform: uppercase;">
        @error('serie') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="folio_actual" class="form-label">Folio Inicial <span class="text-danger">*</span></label>
        <input type="number" name="folio_actual" id="folio_actual" class="form-control @error('folio_actual') is-invalid @enderror" value="{{ old('folio_actual', $serie->folio_actual) }}" required min="0">
        @error('folio_actual') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="tipo_comprobante" class="form-label">Tipo de Comprobante <span class="text-danger">*</span></label>
    <select name="tipo_comprobante" id="tipo_comprobante" class="form-select @error('tipo_comprobante') is-invalid @enderror" required>
        <option value="I" @selected(old('tipo_comprobante', $serie->tipo_comprobante) == 'I')>Ingreso (Facturas)</option>
        <option value="E" @selected(old('tipo_comprobante', $serie->tipo_comprobante) == 'E')>Egreso (Notas de Crédito)</option>
        <option value="P" @selected(old('tipo_comprobante', $serie->tipo_comprobante) == 'P')>Recepción de Pagos</option>
    </select>
    @error('tipo_comprobante') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="sucursal_id" class="form-label">Asignar a Sucursal (Opcional)</label>
    <select name="sucursal_id" id="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror">
        <option value="">General (sin sucursal específica)</option>
        @foreach($sucursales as $sucursal)
            <option value="{{ $sucursal->id }}" @selected(old('sucursal_id', $serie->sucursal_id) == $sucursal->id)>
                {{ $sucursal->nombre }}
            </option>
        @endforeach
    </select>
    @error('sucursal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $serie->is_active))>
    <label class="form-check-label" for="is_active">Activa</label>
</div>

<div class="text-end">
    <a href="{{ route('tenant.facturacion.configuracion.series-folios.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-save me-2"></i>Guardar
    </button>
</div>