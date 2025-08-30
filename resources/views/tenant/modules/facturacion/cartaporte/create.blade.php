<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-truck-ramp-box me-2"></i>
            Nueva Carta Porte
        </h2>
    </x-slot>

    <form action="{{ route('tenant.facturacion.cartaporte.store') }}" method="POST" id="create-cartaporte-form">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Datos Generales -->
        <div class="card mb-4">
            <div class="card-header">
                Datos Generales
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="facturacion_cfdi_id">CFDI Relacionado (Opcional):</label>
                            <input type="text" class="form-control" id="facturacion_cfdi_id" name="facturacion_cfdi_id" value="{{ old('facturacion_cfdi_id') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="version">Versión:</label>
                            <input type="text" class="form-control" id="version" name="version" value="{{ old('version', '3.0') }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transp_internac">Transporte Internacional:</label>
                            <select class="form-select" id="transp_internac" name="transp_internac" required>
                                <option value="No" {{ old('transp_internac') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Sí" {{ old('transp_internac') == 'Sí' ? 'selected' : '' }}>Sí</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubicaciones -->
        <div class="card mb-4">
            <div class="card-header">
                Ubicaciones
            </div>
            <div class="card-body">
                <!-- Origen -->
                <h5><i class="fa-solid fa-location-dot me-2 text-primary"></i> Origen</h5>
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="origen_rfc">RFC Remitente</label>
                        <input type="text" class="form-control" id="origen_rfc" name="origen[rfc]">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="origen_nombre">Nombre Remitente</label>
                        <input type="text" class="form-control" id="origen_nombre" name="origen[nombre]">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="origen_fecha_hora_salida">Fecha y Hora de Salida</label>
                        <input type="datetime-local" class="form-control" id="origen_fecha_hora_salida" name="origen[fecha_hora_salida]">
                    </div>
                </div>
                <h6>Domicilio</h6>
                <div class="row">
                     <div class="col-md-4 form-group">
                        <label for="origen_codigo_postal">Código Postal</label>
                        <input type="text" class="form-control" id="origen_codigo_postal" name="origen[codigo_postal]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_estado">Estado</label>
                        <input type="text" class="form-control" id="origen_estado" name="origen[estado]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_municipio">Municipio</label>
                        <input type="text" class="form-control" id="origen_municipio" name="origen[municipio]">
                    </div>
                     <div class="col-md-4 form-group">
                        <label for="origen_localidad">Localidad</label>
                        <input type="text" class="form-control" id="origen_localidad" name="origen[localidad]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_colonia">Colonia</label>
                        <select id="origen_colonia" name="origen[colonia]"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_calle">Calle</label>
                        <input type="text" class="form-control" id="origen_calle" name="origen[calle]">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="origen_numero_exterior">No. Ext.</label>
                        <input type="text" class="form-control" id="origen_numero_exterior" name="origen[numero_exterior]">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="origen_numero_interior">No. Int.</label>
                        <input type="text" class="form-control" id="origen_numero_interior" name="origen[numero_interior]">
                    </div>
                </div>

                <hr class="my-4">

                <!-- Destino -->
                <h5><i class="fa-solid fa-map-location-dot me-2 text-success"></i> Destino</h5>
                 <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="destino_rfc">RFC Destinatario</label>
                        <input type="text" class="form-control" id="destino_rfc" name="destino[rfc]">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="destino_nombre">Nombre Destinatario</label>
                        <input type="text" class="form-control" id="destino_nombre" name="destino[nombre]">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="destino_fecha_hora_llegada">Fecha y Hora de Llegada</label>
                        <input type="datetime-local" class="form-control" id="destino_fecha_hora_llegada" name="destino[fecha_hora_llegada]">
                    </div>
                </div>
                <h6>Domicilio</h6>
                <div class="row">
                     <div class="col-md-4 form-group">
                        <label for="destino_codigo_postal">Código Postal</label>
                        <input type="text" class="form-control" id="destino_codigo_postal" name="destino[codigo_postal]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_estado">Estado</label>
                        <input type="text" class="form-control" id="destino_estado" name="destino[estado]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_municipio">Municipio</label>
                        <input type="text" class="form-control" id="destino_municipio" name="destino[municipio]">
                    </div>
                     <div class="col-md-4 form-group">
                        <label for="destino_localidad">Localidad</label>
                        <input type="text" class="form-control" id="destino_localidad" name="destino[localidad]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_colonia">Colonia</label>
                        <select id="destino_colonia" name="destino[colonia]"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_calle">Calle</label>
                        <input type="text" class="form-control" id="destino_calle" name="destino[calle]">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="destino_numero_exterior">No. Ext.</label>
                        <input type="text" class="form-control" id="destino_numero_exterior" name="destino[numero_exterior]">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="destino_numero_interior">No. Int.</label>
                        <input type="text" class="form-control" id="destino_numero_interior" name="destino[numero_interior]">
                    </div>
                </div>
            </div>
        </div>

        <!-- Mercancías -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                Mercancías
                <button type="button" class="btn btn-primary btn-sm" id="add-mercancia">
                    <i class="fa-solid fa-plus me-1"></i> Agregar Mercancía
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="mercancias-table">
                        <thead>
                            <tr>
                                <th>Bienes Transp.</th>
                                <th>Descripción</th>
                                <th>Cantidad</th>
                                <th>Clave Unidad</th>
                                <th>Peso (Kg)</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Autotransporte -->
        <div class="card mb-4">
            <div class="card-header">
                Autotransporte
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_perm_sct">Permiso SCT</label>
                        <select id="autotransporte_perm_sct" name="autotransporte[perm_sct]"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_num_permiso_sct">Número de Permiso SCT</label>
                        <input type="text" class="form-control" id="autotransporte_num_permiso_sct" name="autotransporte[num_permiso_sct]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_nombre_aseg">Aseguradora</label>
                        <input type="text" class="form-control" id="autotransporte_nombre_aseg" name="autotransporte[nombre_aseg]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_num_poliza_seguro">Póliza de Seguro</label>
                        <input type="text" class="form-control" id="autotransporte_num_poliza_seguro" name="autotransporte[num_poliza_seguro]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_config_vehicular">Config. Vehicular</label>
                        <select id="autotransporte_config_vehicular" name="autotransporte[config_vehicular]"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_placa_vm">Placa Vehículo</label>
                        <input type="text" class="form-control" id="autotransporte_placa_vm" name="autotransporte[placa_vm]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_anio_modelo_vm">Año Modelo</label>
                        <input type="number" class="form-control" id="autotransporte_anio_modelo_vm" name="autotransporte[anio_modelo_vm]" min="1900" max="{{ date('Y') + 1 }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Figura Transporte -->
        <div class="card mb-4">
            <div class="card-header">
                Figura Transporte
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="figura_transporte_tipo_figura">Tipo de Figura</label>
                        <select id="figura_transporte_tipo_figura" name="figura_transporte[tipo_figura]"></select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="figura_transporte_rfc_figura">RFC Figura</label>
                        <input type="text" class="form-control" id="figura_transporte_rfc_figura" name="figura_transporte[rfc_figura]">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="figura_transporte_nombre_figura">Nombre Figura</label>
                        <input type="text" class="form-control" id="figura_transporte_nombre_figura" name="figura_transporte[nombre_figura]">
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('tenant.facturacion.cartaporte.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="button" class="btn btn-info" id="save-draft-btn">Guardar Borrador</button>
            <button type="submit" class="btn btn-success">Timbrar Carta Porte</button>
        </div>
    </form>

    <form id="draft-form" action="{{ route('tenant.facturacion.cartaporte.storeAsDraft') }}" method="POST" style="display: none;">
        @csrf
        <!-- Campos del formulario principal se copiarán aquí con JS -->
    </form>

    @push('scripts')
        <script>
            // Pasamos la URL base de la API de catálogos a JavaScript
            window.apiUrls = {
                satCatalogos: "{{ url('facturacion/api/sat-catalogs') }}/"
            };
        </script>
        @vite(['resources/js/Modules/Facturacion/cartaporte/cartaporte.js'])
    @endpush

</x-layouts.app>

