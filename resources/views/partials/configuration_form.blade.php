@csrf

<!-- Navegación de Pestañas -->
<ul class="nav nav-tabs" id="configTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">General y Sucursales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="impresion-tab" data-bs-toggle="tab" data-bs-target="#impresion" type="button" role="tab" aria-controls="impresion" aria-selected="false">Impresión</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab" aria-controls="social" aria-selected="false">Redes Sociales</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="apariencia-tab" data-bs-toggle="tab" data-bs-target="#apariencia" type="button" role="tab" aria-controls="apariencia" aria-selected="false">Apariencia</button>
    </li>
</ul>

<!-- Contenido de las Pestañas -->
<div class="tab-content pt-4" id="configTabsContent">

    <!-- Pestaña General y Sucursales -->
    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
        <h5 class="mb-4">Configuración General</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="main_logo" class="form-label">Logo Principal de la Empresa</label>
                <input class="form-control" type="file" id="main_logo" name="main_logo" accept="image/*">
                <div class="form-text">Sube el logo que aparecerá en las facturas y reportes.</div>
                @if(isset($settings['main_logo']))
                    <div class="mt-2">
                        <img src="{{ Storage::url($settings['main_logo']->value) }}" alt="Logo Actual" style="max-height: 80px; border: 1px solid #ddd; padding: 5px;">
                    </div>
                @endif
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3">Configuración por Sucursal</h5>
        @forelse($sucursales as $sucursal)
            <div class="p-3 mb-3 border rounded">
                <h6><i class="fa-solid fa-store me-2"></i>{{ $sucursal->nombre }}</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="slogan_sucursal_{{ $sucursal->id }}" class="form-label">Eslogan de la Sucursal</label>
                        <input type="text" class="form-control" id="slogan_sucursal_{{ $sucursal->id }}" name="slogan_sucursal_{{ $sucursal->id }}" value="{{ $settings['slogan_sucursal_' . $sucursal->id]->value ?? '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="logo_sucursal_{{ $sucursal->id }}" class="form-label">Logo Específico de la Sucursal (Opcional)</label>
                        <input class="form-control" type="file" id="logo_sucursal_{{ $sucursal->id }}" name="logo_sucursal_{{ $sucursal->id }}" accept="image/*">
                        @if(isset($settings['logo_sucursal_' . $sucursal->id]))
                            <div class="mt-2">
                                <img src="{{ Storage::url($settings['logo_sucursal_' . $sucursal->id]->value) }}" alt="Logo Sucursal" style="max-height: 60px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">No hay sucursales registradas para este tenant.</p>
        @endforelse
    </div>

    <!-- Pestaña Impresión -->
    <div class="tab-pane fade" id="impresion" role="tabpanel" aria-labelledby="impresion-tab">
        <h5 class="mb-4">Configuración para Impresiones (Tickets, Recetas, Notas)</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="print_rfc" class="form-label">RFC de la Matriz (para tickets)</label>
                <input type="text" class="form-control" id="print_rfc" name="print_rfc" value="{{ $settings['print_rfc']->value ?? '' }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="print_doctor_cedula" class="form-label">Cédula Profesional (para recetas médicas)</label>
                <input type="text" class="form-control" id="print_doctor_cedula" name="print_doctor_cedula" value="{{ $settings['print_doctor_cedula']->value ?? '' }}">
                <div class="form-text">Opcional. Dejar en blanco si no aplica.</div>
            </div>
        </div>
        <div class="mb-3">
            <label for="print_footer_text" class="form-label">Leyenda al pie de la impresión</label>
            <textarea class="form-control" id="print_footer_text" name="print_footer_text" rows="3">{{ $settings['print_footer_text']->value ?? 'Gracias por su preferencia.' }}</textarea>
            <div class="form-text">Ej: "Gracias por su visita", "Precios incluyen IVA", etc.</div>
        </div>
         <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="print_show_address" name="print_show_address" value="1" @checked(old('print_show_address', $settings['print_show_address']->value ?? true))>
            <label class="form-check-label" for="print_show_address">Mostrar dirección de la sucursal en la impresión</label>
        </div>
    </div>

    <!-- Pestaña Redes Sociales -->
    <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
        <h5 class="mb-4">Redes Sociales y Contacto</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="social_facebook" class="form-label">Página de Facebook</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-brands fa-facebook"></i></span>
                    <input type="url" class="form-control" id="social_facebook" name="social_facebook" placeholder="https://facebook.com/suempresa" value="{{ $settings['social_facebook']->value ?? '' }}">
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="social_instagram" class="form-label">Perfil de Instagram</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-brands fa-instagram"></i></span>
                    <input type="url" class="form-control" id="social_instagram" name="social_instagram" placeholder="https://instagram.com/suempresa" value="{{ $settings['social_instagram']->value ?? '' }}">
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="contact_whatsapp" class="form-label">Número de WhatsApp (con código de país)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-brands fa-whatsapp"></i></span>
                    <input type="tel" class="form-control" id="contact_whatsapp" name="contact_whatsapp" placeholder="Ej: 5215512345678" value="{{ $settings['contact_whatsapp']->value ?? '' }}">
                </div>
            </div>
        </div>
    </div>

    <!-- Pestaña Apariencia -->
    <div class="tab-pane fade" id="apariencia" role="tabpanel" aria-labelledby="apariencia-tab">
        <h5 class="mb-4">Personalización de la Apariencia</h5>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="primary_color" class="form-label">Color Primario</label>
                <input type="color" class="form-control form-control-color" id="primary_color" name="primary_color" value="{{ $settings['primary_color']->value ?? '#4f46e5' }}" title="Elija su color primario">
            </div>
            <div class="col-md-4 mb-3">
                <label for="secondary_color" class="form-label">Color Secundario</label>
                <input type="color" class="form-control form-control-color" id="secondary_color" name="secondary_color" value="{{ $settings['secondary_color']->value ?? '#6b7280' }}" title="Elija su color secundario">
            </div>
        </div>
        <p class="text-muted">Esta funcionalidad estará disponible en futuras actualizaciones.</p>
    </div>

</div>

<div class="mt-4 text-end">
    <button type="submit" class="btn btn-primary px-4">{{ __('Guardar Configuración') }}</button>
</div>