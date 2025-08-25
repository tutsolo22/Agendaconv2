@csrf

<div x-data="{
    nombre: '{{ old('nombre', $modulo->nombre ?? '') }}',
    slug: '{{ old('slug', $modulo->slug ?? '') }}',
    generateSlug() {
        this.slug = this.nombre.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
    }
}">
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="nombre" class="form-label">{{ __('Nombre del Módulo') }}</label>
                <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" x-model="nombre" @input="generateSlug" required autofocus>
                @error('nombre')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="slug" class="form-label">{{ __('Slug (URL amigable)') }}</label>
                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" x-model="slug" required>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="route_name" class="form-label">{{ __('Nombre de la Ruta Principal') }}</label>
            <input type="text" class="form-control @error('route_name') is-invalid @enderror" id="route_name" name="route_name" value="{{ old('route_name', $modulo->route_name ?? '') }}" placeholder="Ej: tenant.citas.index">
            <div class="form-text">Opcional. Nombre de la ruta principal del módulo para la navegación.</div>
            @error('route_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="icono" class="form-label">{{ __('Ícono (Font Awesome 6)') }}</label>
            <input type="text" class="form-control @error('icono') is-invalid @enderror" id="icono" name="icono" value="{{ old('icono', $modulo->icono ?? '') }}" placeholder="Ej: fa-solid fa-calendar-check">
            <div class="form-text">Opcional. Clases completas del ícono.</div>
            @error('icono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="descripcion" class="form-label">{{ __('Descripción') }}</label>
    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3">{{ old('descripcion', $modulo->descripcion ?? '') }}</textarea>
    @error('descripcion')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="submenu" class="form-label">{{ __('Submenú (JSON)') }}</label>
    <textarea class="form-control @error('submenu') is-invalid @enderror" id="submenu" name="submenu" rows="5">{{ old('submenu', json_encode($modulo->submenu, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?? '') }}</textarea>
    <div class="form-text">Opcional. Estructura del submenú en formato JSON.</div>
    @error('submenu')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-check form-switch mb-4">
    <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" @checked(old('is_active', $modulo->is_active ?? true))>
    <label class="form-check-label" for="is_active">{{ __('Módulo Activo') }}</label>
</div>

<div class="mt-4">
    <a href="{{ route('admin.modulos.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
</div>