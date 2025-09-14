<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-truck-ramp-box me-2"></i>
            Nueva Carta Porte
        </h2>
    </x-slot>

    <form action="{{ route('tenant.facturacion.cartaporte.store') }}" method="POST" id="create-cartaporte-form">
        @csrf

        <!-- Datos Generales -->
        <div class="card mb-4">
            <div class="card-header">
                Datos Generales
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="facturacion_cfdi_id">CFDI Relacionado</label>
                            <input type="text" class="form-control @error('facturacion_cfdi_id') is-invalid @enderror" id="facturacion_cfdi_id" name="facturacion_cfdi_id" value="{{ old('facturacion_cfdi_id') }}">
                            @error('facturacion_cfdi_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="version">Versión</label>
                            <input type="text" class="form-control" id="version" name="version" value="3.1" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transp_internac">Transporte Internacional</label>
                            <select class="form-select @error('transp_internac') is-invalid @enderror" id="transp_internac" name="transp_internac">
                                <option value="No" {{ old('transp_internac') == 'No' ? 'selected' : '' }}>No</option>
                                <option value="Sí" {{ old('transp_internac') == 'Sí' ? 'selected' : '' }}>Sí</option>
                            </select>
                            @error('transp_internac')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                        <input type="text" class="form-control @error('origen.rfc') is-invalid @enderror" id="origen_rfc" name="origen[rfc]" value="{{ old('origen.rfc', $datosFiscales->rfc ?? '') }}">
                        @error('origen.rfc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="origen_nombre">Nombre Remitente</label>
                        <input type="text" class="form-control @error('origen.nombre') is-invalid @enderror" id="origen_nombre" name="origen[nombre]" value="{{ old('origen.nombre', $datosFiscales->razon_social ?? '') }}">
                        @error('origen.nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="origen_fecha_hora_salida">Fecha y Hora de Salida</label>
                        <input type="datetime-local" class="form-control @error('origen.fecha_hora_salida') is-invalid @enderror" id="origen_fecha_hora_salida" name="origen[fecha_hora_salida]" value="{{ old('origen.fecha_hora_salida') }}">
                        @error('origen.fecha_hora_salida')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <h6 class="mt-3">Domicilio</h6>
                <div class="row">
                     <div class="col-md-4 form-group">
                        <label for="origen_codigo_postal">Código Postal</label>
                        <input type="text" class="form-control @error('origen.codigo_postal') is-invalid @enderror" id="origen_codigo_postal" name="origen[codigo_postal]" value="{{ old('origen.codigo_postal', $datosFiscales->cp_fiscal ?? '') }}">
                        @error('origen.codigo_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_estado">Estado</label>
                        <input type="text" class="form-control @error('origen.estado') is-invalid @enderror" id="origen_estado" name="origen[estado]" value="{{ old('origen.estado', $origenDomicilio['estado'] ?? '') }}" readonly>
                        @error('origen.estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_municipio">Municipio</label>
                        <input type="text" class="form-control @error('origen.municipio') is-invalid @enderror" id="origen_municipio" name="origen[municipio]" value="{{ old('origen.municipio', $origenDomicilio['municipio'] ?? '') }}" readonly>
                        @error('origen.municipio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-4 form-group">
                        <label for="origen_localidad">Localidad</label>
                        <input type="text" class="form-control @error('origen.localidad') is-invalid @enderror" id="origen_localidad" name="origen[localidad]" value="{{ old('origen.localidad', $origenDomicilio['localidad'] ?? '') }}" readonly>
                         @error('origen.localidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_colonia">Colonia</label>
                        <select id="origen_colonia" class="@error('origen.colonia') is-invalid @enderror" name="origen[colonia]">
                            <option value="">Seleccione una colonia</option>
                            @if(!empty($origenDomicilio['colonias']))
                                @foreach($origenDomicilio['colonias'] as $cveColonia => $nombreColonia)
                                    <option value="{{ $cveColonia }}" {{ old('origen.colonia') == $cveColonia ? 'selected' : '' }}>{{ $nombreColonia }}</option>
                                @endforeach
                            @endif
                        </select>
                        @error('origen.colonia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="origen_calle">Calle</label>
                        <input type="text" class="form-control @error('origen.calle') is-invalid @enderror" id="origen_calle" name="origen[calle]" value="{{ old('origen.calle') }}">
                        @error('origen.calle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="origen_numero_exterior">No. Ext.</label>
                        <input type="text" class="form-control @error('origen.numero_exterior') is-invalid @enderror" id="origen_numero_exterior" name="origen[numero_exterior]" value="{{ old('origen.numero_exterior') }}">
                        @error('origen.numero_exterior')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="origen_numero_interior">No. Int.</label>
                        <input type="text" class="form-control @error('origen.numero_interior') is-invalid @enderror" id="origen_numero_interior" name="origen[numero_interior]" value="{{ old('origen.numero_interior') }}">
                        @error('origen.numero_interior')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <!-- Destino -->
                <h5><i class="fa-solid fa-map-location-dot me-2 text-success"></i> Destino</h5>
                 <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="destino_rfc">RFC Destinatario</label>
                        <input type="text" class="form-control @error('destino.rfc') is-invalid @enderror" id="destino_rfc" name="destino[rfc]" value="{{ old('destino.rfc') }}">
                        @error('destino.rfc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="destino_nombre">Nombre Destinatario</label>
                        <input type="text" class="form-control @error('destino.nombre') is-invalid @enderror" id="destino_nombre" name="destino[nombre]" value="{{ old('destino.nombre') }}">
                        @error('destino.nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="destino_fecha_hora_llegada">Fecha y Hora de Llegada</label>
                        <input type="datetime-local" class="form-control @error('destino.fecha_hora_llegada') is-invalid @enderror" id="destino_fecha_hora_llegada" name="destino[fecha_hora_llegada]" value="{{ old('destino.fecha_hora_llegada') }}">
                        @error('destino.fecha_hora_llegada')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <h6 class="mt-3">Domicilio</h6>
                <div class="row">
                     <div class="col-md-4 form-group">
                        <label for="destino_codigo_postal">Código Postal</label>
                        <input type="text" class="form-control @error('destino.codigo_postal') is-invalid @enderror" id="destino_codigo_postal" name="destino[codigo_postal]" value="{{ old('destino.codigo_postal') }}">
                        @error('destino.codigo_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_estado">Estado</label>
                        <input type="text" class="form-control @error('destino.estado') is-invalid @enderror" id="destino_estado" name="destino[estado]" value="{{ old('destino.estado') }}" readonly>
                        @error('destino.estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_municipio">Municipio</label>
                        <input type="text" class="form-control @error('destino.municipio') is-invalid @enderror" id="destino_municipio" name="destino[municipio]" value="{{ old('destino.municipio') }}" readonly>
                        @error('destino.municipio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-4 form-group">
                        <label for="destino_localidad">Localidad</label>
                        <input type="text" class="form-control @error('destino.localidad') is-invalid @enderror" id="destino_localidad" name="destino[localidad]" value="{{ old('destino.localidad') }}" readonly>
                        @error('destino.localidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_colonia">Colonia</label>
                        <select id="destino_colonia" class="@error('destino.colonia') is-invalid @enderror" name="destino[colonia]"></select>
                        @error('destino.colonia')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="destino_calle">Calle</label>
                        <input type="text" class="form-control @error('destino.calle') is-invalid @enderror" id="destino_calle" name="destino[calle]" value="{{ old('destino.calle') }}">
                        @error('destino.calle')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="destino_numero_exterior">No. Ext.</label>
                        <input type="text" class="form-control @error('destino.numero_exterior') is-invalid @enderror" id="destino_numero_exterior" name="destino[numero_exterior]" value="{{ old('destino.numero_exterior') }}">
                        @error('destino.numero_exterior')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="destino_numero_interior">No. Int.</label>
                        <input type="text" class="form-control @error('destino.numero_interior') is-invalid @enderror" id="destino_numero_interior" name="destino[numero_interior]" value="{{ old('destino.numero_interior') }}">
                        @error('destino.numero_interior')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Mercancías -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    Mercancías
                    @error('mercancias')
                        <span class="text-danger ms-2">{{ $message }}</span>
                    @enderror
                </span>
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
                            <!-- JS will populate this, and also handle validation errors for rows -->
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
                        <select id="autotransporte_perm_sct" class="@error('autotransporte.perm_sct') is-invalid @enderror" name="autotransporte[perm_sct]"></select>
                        @error('autotransporte.perm_sct')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_num_permiso_sct">Número de Permiso SCT</label>
                        <input type="text" class="form-control @error('autotransporte.num_permiso_sct') is-invalid @enderror" id="autotransporte_num_permiso_sct" name="autotransporte[num_permiso_sct]" value="{{ old('autotransporte.num_permiso_sct') }}">
                        @error('autotransporte.num_permiso_sct')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_nombre_aseg">Aseguradora</label>
                        <input type="text" class="form-control @error('autotransporte.nombre_aseg') is-invalid @enderror" id="autotransporte_nombre_aseg" name="autotransporte[nombre_aseg]" value="{{ old('autotransporte.nombre_aseg') }}">
                        @error('autotransporte.nombre_aseg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_num_poliza_seguro">Póliza de Seguro</label>
                        <input type="text" class="form-control @error('autotransporte.num_poliza_seguro') is-invalid @enderror" id="autotransporte_num_poliza_seguro" name="autotransporte[num_poliza_seguro]" value="{{ old('autotransporte.num_poliza_seguro') }}">
                        @error('autotransporte.num_poliza_seguro')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_config_vehicular">Config. Vehicular</label>
                        <select id="autotransporte_config_vehicular" class="@error('autotransporte.config_vehicular') is-invalid @enderror" name="autotransporte[config_vehicular]"></select>
                        @error('autotransporte.config_vehicular')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_placa_vm">Placa Vehículo</label>
                        <input type="text" class="form-control @error('autotransporte.placa_vm') is-invalid @enderror" id="autotransporte_placa_vm" name="autotransporte[placa_vm]" value="{{ old('autotransporte.placa_vm') }}">
                        @error('autotransporte.placa_vm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="autotransporte_anio_modelo_vm">Año Modelo</label>
                        <input type="number" class="form-control @error('autotransporte.anio_modelo_vm') is-invalid @enderror" id="autotransporte_anio_modelo_vm" name="autotransporte[anio_modelo_vm]" min="1900" max="{{ date('Y') + 1 }}" value="{{ old('autotransporte.anio_modelo_vm') }}">
                        @error('autotransporte.anio_modelo_vm')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
                        <select id="figura_transporte_tipo_figura" class="@error('figura_transporte.tipo_figura') is-invalid @enderror" name="figura_transporte[tipo_figura]"></select>
                        @error('figura_transporte.tipo_figura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="figura_transporte_rfc_figura">RFC Figura</label>
                        <input type="text" class="form-control @error('figura_transporte.rfc_figura') is-invalid @enderror" id="figura_transporte_rfc_figura" name="figura_transporte[rfc_figura]" value="{{ old('figura_transporte.rfc_figura') }}">
                        @error('figura_transporte.rfc_figura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="figura_transporte_nombre_figura">Nombre Figura</label>
                        <input type="text" class="form-control @error('figura_transporte.nombre_figura') is-invalid @enderror" id="figura_transporte_nombre_figura" name="figura_transporte[nombre_figura]" value="{{ old('figura_transporte.nombre_figura') }}">
                        @error('figura_transporte.nombre_figura')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
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
            // Pasamos las URLs y datos de validación a JavaScript
            window.cartaPorteData = {
                apiUrls: {
                    cartaPorteCatalogos: "{{ route('tenant.facturacion.api.cartaporte.catalogs') }}",
                    searchableCatalogos: "{{ url('facturacion/api/sat-catalogs') }}/",
                    codigoPostalInfo: "{{ url('facturacion/api/codigopostal') }}/"
                },
                oldData: @json(old('mercancias') ?? []),
                errors: @json($errors->get('mercancias.*'))
            };
        </script>
        @vite(['resources/js/Modules/Facturacion/cartaporte/cartaporte.js'])
    @endpush

</x-layouts.app>