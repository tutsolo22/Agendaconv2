@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="nombre" class="form-label">Nombre del Proveedor <span class="text-danger">*</span></label>
        <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $pac->nombre ?? '') }}" required>
        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="rfc" class="form-label">RFC del Proveedor <span class="text-danger">*</span></label>
        <input type="text" name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc', $pac->rfc ?? '') }}" required maxlength="13" style="text-transform: uppercase;">
        @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="url_produccion" class="form-label">URL de Producción <span class="text-danger">*</span></label>
        <input type="url" name="url_produccion" id="url_produccion" class="form-control @error('url_produccion') is-invalid @enderror" value="{{ old('url_produccion', $pac->url_produccion ?? '') }}" required placeholder="https://api.pac.com/v1/stamp">
        @error('url_produccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="url_pruebas" class="form-label">URL de Pruebas (Sandbox)</label>
        <input type="url" name="url_pruebas" id="url_pruebas" class="form-control @error('url_pruebas') is-invalid @enderror" value="{{ old('url_pruebas', $pac->url_pruebas ?? '') }}" placeholder="https://api-sandbox.pac.com/v1/stamp">
        @error('url_pruebas') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="usuario" class="form-label">Usuario</label>
        <input type="text" name="usuario" id="usuario" class="form-control @error('usuario') is-invalid @enderror" value="{{ old('usuario', $pac->usuario ?? '') }}">
        @error('usuario') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Contraseña @if(!$pac->exists)<span class="text-danger">*</span>@endif</label>
        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="{{ $pac->exists ? 'Dejar en blanco para no cambiar' : '' }}" @if(!$pac->exists) required @endif>
        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $pac->is_active ?? true))>
    <label class="form-check-label" for="is_active">Activo</label>
</div>

<div class="text-end">
    <a href="{{ route('tenant.facturacion.configuracion.pacs.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-save me-2"></i>Guardar Proveedor
    </button>
</div>