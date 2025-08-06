 <x-layouts.app>        
         @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="rfc" class="form-label">RFC</label>
                        <input type="text" name="rfc" id="rfc" class="form-control text-uppercase @error('rfc') is-invalid @enderror" value="{{ old('rfc', $datoFiscal->rfc) }}" required>
                        @error('rfc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="razon_social" class="form-label">Razón Social</label>
                        <input type="text" name="razon_social" id="razon_social" class="form-control @error('razon_social') is-invalid @enderror" value="{{ old('razon_social', $datoFiscal->razon_social) }}" required>
                        @error('razon_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="regimen_fiscal_clave" class="form-label">Régimen Fiscal (Clave)</label>
                        <input type="text" name="regimen_fiscal_clave" id="regimen_fiscal_clave" class="form-control @error('regimen_fiscal_clave') is-invalid @enderror" value="{{ old('regimen_fiscal_clave', $datoFiscal->regimen_fiscal_clave) }}" required placeholder="Ej: 601, 626">
                        @error('regimen_fiscal_clave') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="cp_fiscal" class="form-label">Código Postal Fiscal</label>
                        <input type="text" name="cp_fiscal" id="cp_fiscal" class="form-control @error('cp_fiscal') is-invalid @enderror" value="{{ old('cp_fiscal', $datoFiscal->cp_fiscal) }}" required>
                        @error('cp_fiscal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pac_id" class="form-label">Proveedor Autorizado (PAC)</label>
                        <select name="pac_id" id="pac_id" class="form-select @error('pac_id') is-invalid @enderror">
                            <option value="">Seleccionar PAC</option>
                            @foreach($pacs as $pac)
                                <option value="{{ $pac->id }}" {{ old('pac_id', $datoFiscal->pac_id) == $pac->id ? 'selected' : '' }}>
                                    {{ $pac->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('pac_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr>
                <h5 class="mb-3">Certificado de Sello Digital (CSD)</h5>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="archivo_cer" class="form-label">Archivo .CER (Certificado)</label>
                        <input class="form-control @error('archivo_cer') is-invalid @enderror" type="file" id="archivo_cer" name="archivo_cer" accept=".cer">
                        @if($datoFiscal->path_cer) <small class="text-muted">Archivo actual cargado. Seleccione uno nuevo para reemplazarlo.</small> @endif
                        @error('archivo_cer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="archivo_key" class="form-label">Archivo .KEY (Llave Privada)</label>
                        <input class="form-control @error('archivo_key') is-invalid @enderror" type="file" id="archivo_key" name="archivo_key" accept=".key">
                        @if($datoFiscal->path_key) <small class="text-muted">Archivo actual cargado. Seleccione uno nuevo para reemplazarlo.</small> @endif
                        @error('archivo_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="password_csd" class="form-label">Contraseña CSD</label>
                        <input type="password" name="password_csd" id="password_csd" class="form-control @error('password_csd') is-invalid @enderror" placeholder="Dejar en blanco para no cambiar">
                        @error('password_csd') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="en_pruebas" name="en_pruebas" value="1" {{ old('en_pruebas', $datoFiscal->en_pruebas) ? 'checked' : '' }}>
                            <label class="form-check-label" for="en_pruebas">
                                Usar PAC en modo de pruebas
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Guardar Datos Fiscales</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

