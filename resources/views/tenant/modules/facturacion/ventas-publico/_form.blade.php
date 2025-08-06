@csrf
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="folio_venta" class="form-label">Folio de Venta / Ticket</label>
        <input type="text" name="folio_venta" id="folio_venta" class="form-control @error('folio_venta') is-invalid @enderror" value="{{ old('folio_venta', $venta->folio_venta ?? null) }}" required>
        @error('folio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="fecha" class="form-label">Fecha de la Venta</label>
        <input type="date" name="fecha" id="fecha" class="form-control @error('fecha') is-invalid @enderror" value="{{ old('fecha', $venta->fecha ?? now()->format('Y-m-d')) }}" required>
        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
<div class="mb-3">
    <label for="total" class="form-label">Total de la Venta (IVA incluido)</label>
    <input type="number" name="total" id="total" class="form-control @error('total') is-invalid @enderror" value="{{ old('total', $venta->total ?? null) }}" step="0.01" required>
    @error('total') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="text-end">
    <a href="{{ route('tenant.facturacion.ventas-publico.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar Venta</button>
</div>