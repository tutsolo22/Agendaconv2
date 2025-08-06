@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="nombre_completo" class="form-label">Nombre Completo o Razón Social</label>
        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control @error('nombre_completo') is-invalid @enderror" value="{{ old('nombre_completo', $cliente->nombre_completo) }}" required>
        @error('nombre_completo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="rfc" class="form-label">RFC</label>
        <input type="text" name="rfc" id="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc', $cliente->rfc) }}" maxlength="13" style="text-transform:uppercase">
        @error('rfc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $cliente->email) }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="telefono" class="form-label">Teléfono</label>
        <input type="tel" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $cliente->telefono) }}">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-3">
    <label for="direccion_fiscal" class="form-label">Dirección Fiscal</label>
    <textarea name="direccion_fiscal" id="direccion_fiscal" class="form-control @error('direccion_fiscal') is-invalid @enderror" rows="3">{{ old('direccion_fiscal', $cliente->direccion_fiscal) }}</textarea>
    @error('direccion_fiscal')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="tipo" class="form-label">Tipo</label>
    <select name="tipo" id="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
        <option value="persona" @selected(old('tipo', $cliente->tipo) == 'persona')>Persona Física</option>
        <option value="empresa" @selected(old('tipo', $cliente->tipo) == 'empresa')>Empresa (Persona Moral)</option>
    </select>
    @error('tipo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="text-end">
    <a href="{{ route('tenant.clientes.index') }}" class="btn btn-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
</div>