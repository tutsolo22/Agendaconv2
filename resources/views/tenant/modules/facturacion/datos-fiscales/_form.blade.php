@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="razon_social" class="form-label">Razón Social</label>
        <input type="text" name="razon_social" id="razon_social" class="form-control" value="{{ old('razon_social', $datoFiscal->razon_social ?? '') }}" required>
    </div>
    <div class="col-md-4 mb-3">
        <label for="rfc" class="form-label">RFC</label>
        <input type="text" name="rfc" id="rfc" class="form-control" value="{{ old('rfc', $datoFiscal->rfc ?? '') }}" required maxlength="13" style="text-transform: uppercase;">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="regimen_fiscal_clave" class="form-label">Clave de Régimen Fiscal (del catálogo del SAT)</label>
        <input type="text" name="regimen_fiscal_clave" id="regimen_fiscal_clave" class="form-control" value="{{ old('regimen_fiscal_clave', $datoFiscal->regimen_fiscal_clave ?? '') }}" required maxlength="3" placeholder="Ej: 601">
    </div>
    <div class="col-md-6 mb-3">
        <label for="cp_fiscal" class="form-label">Código Postal Fiscal</label>
        <input type="text" name="cp_fiscal" id="cp_fiscal" class="form-control" value="{{ old('cp_fiscal', $datoFiscal->cp_fiscal ?? '') }}" required maxlength="5">
    </div>
</div>

<hr class="my-4">

<h5 class="mb-3">Certificados de Sello Digital (CSD)</h5>

@if(isset($datoFiscal))
<div class="alert alert-info">
    Deje los campos de archivos y contraseña en blanco si no desea actualizarlos.
</div>
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="cer_file" class="form-label">Archivo de Certificado (.cer)</label>
        <input type="file" name="cer_file" id="cer_file" class="form-control" accept=".cer">
        @if(isset($datoFiscal->path_cer_pem))
            <small class="form-text text-muted">Archivo actual cargado. Válido hasta: {{ \Carbon\Carbon::parse($datoFiscal->valido_hasta)->format('d/m/Y') }}</small>
        @endif
    </div>
    <div class="col-md-6 mb-3">
        <label for="key_file" class="form-label">Archivo de Llave Privada (.key)</label>
        <input type="file" name="key_file" id="key_file" class="form-control" accept=".key">
         @if(isset($datoFiscal->path_key_pem))
            <small class="form-text text-muted">Archivo actual cargado.</small>
        @endif
    </div>
</div>

<div class="mb-3">
    <label for="password_csd" class="form-label">Contraseña de la Llave Privada</label>
    <input type="password" name="password_csd" id="password_csd" class="form-control" placeholder="{{ isset($datoFiscal) ? 'Ingrese solo si desea cambiarla' : '' }}">
</div>

<hr class="my-4">

<h5 class="mb-3">Configuración del Proveedor de Timbrado (PAC)</h5>

<div class="row">
    <div class="col-md-8 mb-3">
        <label for="pac_id" class="form-label">Proveedor Activo</label>
        <select name="pac_id" id="pac_id" class="form-select">
            <option value="">-- Seleccione un PAC --</option>
            @foreach(\App\Modules\Facturacion\Models\Pac::where('is_active', true)->get() as $pac)
                <option value="{{ $pac->id }}" @selected(old('pac_id', $datoFiscal->pac_id ?? '') == $pac->id)>{{ $pac->nombre }} ({{ $pac->rfc }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3 d-flex align-items-end">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" role="switch" id="en_pruebas" name="en_pruebas" value="1" @checked(old('en_pruebas', $datoFiscal->en_pruebas ?? true))>
            <label class="form-check-label" for="en_pruebas">Modo Pruebas (Sandbox)</label>
        </div>
    </div>
</div>

<div class="text-end">
    <a href="{{ route('tenant.facturacion.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">
        <i class="fa-solid fa-save me-2"></i>Guardar Datos Fiscales
    </button>
</div>