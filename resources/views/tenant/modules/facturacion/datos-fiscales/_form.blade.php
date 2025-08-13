@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="razon_social" class="form-label">Razón Social <span class="text-danger">*</span></label>
        <input type="text" name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $datoFiscal->razon_social ?? '') }}" required>
        @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="rfc" class="form-label">RFC <span class="text-danger">*</span></label>
        <input type="text" name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc', $datoFiscal->rfc ?? '') }}" required maxlength="13">
        @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="regimen_fiscal_clave" class="form-label">Régimen Fiscal <span class="text-danger">*</span></label>
        <select name="regimen_fiscal_clave" id="regimen_fiscal_clave" class="form-select @error('regimen_fiscal_clave') is-invalid @enderror" required>
            <option value="">Cargando regímenes...</option>
        </select>
        @error('regimen_fiscal_clave') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="cp_fiscal" class="form-label">Código Postal Fiscal <span class="text-danger">*</span></label>
        <input type="text" name="cp_fiscal" id="cp_fiscal" class="form-control @error('cp_fiscal') is-invalid @enderror" value="{{ old('cp_fiscal', $datoFiscal->cp_fiscal ?? '') }}" required maxlength="5">
        @error('cp_fiscal') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">

<h5 class="mb-3">Certificados de Sello Digital (CSD)</h5>

{{-- Mensaje informativo para el modo de edición --}}
@if($datoFiscal->exists)
<div class="alert alert-info">
    <i class="fa-solid fa-circle-info me-2"></i>
    Deje los campos de archivos y contraseña en blanco si no desea actualizarlos.
</div>
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="archivo_cer" class="form-label">Archivo de Certificado (.cer) @if(!$datoFiscal->exists)<span class="text-danger">*</span>@endif</label>
        <input type="file" name="archivo_cer" id="archivo_cer" class="form-control @error('archivo_cer') is-invalid @enderror" accept=".cer" @if(!$datoFiscal->exists) required @endif>
        @if($datoFiscal->path_cer)
            <small class="form-text text-success">Archivo .cer cargado. @if($datoFiscal->valido_hasta) Válido hasta: {{ \Carbon\Carbon::parse($datoFiscal->valido_hasta)->format('d/m/Y') }} @endif</small>
        @endif
        @error('archivo_cer') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-6 mb-3">
        <label for="archivo_key" class="form-label">Archivo de Llave Privada (.key) @if(!$datoFiscal->exists)<span class="text-danger">*</span>@endif</label>
        <input type="file" name="archivo_key" id="archivo_key" class="form-control @error('archivo_key') is-invalid @enderror" accept=".key" @if(!$datoFiscal->exists) required @endif>
         @if($datoFiscal->path_key)
            <small class="form-text text-success">Archivo .key cargado.</small>
        @endif
        @error('archivo_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="password_csd" class="form-label">Contraseña de la Llave Privada @if(!$datoFiscal->exists)<span class="text-danger">*</span>@endif</label>
    <input type="password" name="password_csd" id="password_csd" class="form-control @error('password_csd') is-invalid @enderror" placeholder="{{ $datoFiscal->exists ? 'Ingrese solo si desea cambiarla' : '' }}" @if(!$datoFiscal->exists) required @endif>
    @error('password_csd') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<hr class="my-4">

<h5 class="mb-3">Configuración del Proveedor de Timbrado (PAC)</h5>

<div class="row">
    <div class="col-md-8 mb-3">
        <label for="pac_id" class="form-label">Proveedor Activo</label>
        <select name="pac_id" id="pac_id" class="form-select @error('pac_id') is-invalid @enderror">
            <option value="">-- Seleccione un PAC --</option>
            @foreach($pacs as $pac)
                <option value="{{ $pac->id }}" @selected(old('pac_id', $datoFiscal->pac_id ?? '') == $pac->id)>{{ $pac->nombre }} ({{ $pac->rfc }})</option>
            @endforeach
        </select>
        @error('pac_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="en_pruebas" name="en_pruebas" value="1" @checked(old('en_pruebas', $datoFiscal->en_pruebas ?? false))>
            <label class="form-check-label" for="en_pruebas">Modo Pruebas (Sandbox)</label>
        </div>
    </div>
</div>

<div class="text-end">
    <a href="{{ route('tenant.facturacion.configuracion.datos-fiscales.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-save me-2"></i>Guardar Datos Fiscales
    </button>
</div>