<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            @if(isset($facturaOriginal))
                <i class="fa-solid fa-file-invoice me-2"></i>
                {{ __('Crear Nota de Crédito (Egreso)') }}
            @else
                <i class="fa-solid fa-file-circle-plus me-2"></i>
                {{ __('Crear Nueva Factura (V4)') }}
            @endif
        </h2>
    </x-slot>

    @push('styles')
    {{-- Los estilos específicos de esta página ahora se cargan a través de Vite --}}
    @endpush

    <div class="card shadow-sm">
        <div class="card-body">
            <form id="cfdi-form"
                  method="POST"
                  action="{{ route('tenant.facturacion.cfdis.store') }}"
                  @if(isset($facturaOriginal))
                      data-factura-original="{{ json_encode($facturaOriginal->load('cliente', 'conceptos')) }}"
                  @endif
            >
                @csrf

                {{-- SECCIÓN DE DATOS GENERALES --}}
                <h5 class="card-title text-primary">1. Datos Generales</h5>
                <hr>

                {{-- SECCIÓN CFDI RELACIONADO (SOLO PARA NOTAS DE CRÉDITO) --}}
                @if(isset($facturaOriginal))
                    <div class="alert alert-secondary">
                        <i class="fa-solid fa-link me-2"></i>
                        Estás creando una nota de crédito relacionada a la factura con UUID: <strong>{{ $facturaOriginal->uuid_fiscal }}</strong>
                    </div>
                    <div class="row p-2 mb-3 border rounded mx-1">
                        <div class="col-md-6 mb-3"><label for="relation_type" class="form-label">Tipo de Relación <span class="text-danger">*</span></label><select id="relation_type" name="relation_type" class="form-select" required></select></div>
                        <div class="col-md-6 mb-3"><label for="related_uuid" class="form-label">UUID Relacionado</label><input type="text" id="related_uuid" name="related_uuid" class="form-control" value="{{ $facturaOriginal->uuid_fiscal }}" readonly></div>
                    </div>
                    <input type="hidden" name="tipo_comprobante" value="E"> {{-- E = Egreso --}}
                @endif

                <div class="row">
                    <div class="col-md-12 mb-3 ">
                        <label for="cliente_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                            <select id="cliente_id" name="cliente_id" required
                                    @if(isset($facturaOriginal)) disabled @endif
                            >
                                {{-- Tom-Select llenará este campo --}}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Columna Izquierda -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="serie" class="form-label">Serie y Folio <span class="text-danger">*</span></label>
                            <select id="serie" name="serie_folio_id" class="form-select" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="forma_pago" class="form-label">Forma de Pago <span class="text-danger">*</span></label>
                            <select id="forma_pago" name="forma_pago" class="form-select" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago <span class="text-danger">*</span></label>
                            <select id="metodo_pago" name="metodo_pago" class="form-select" required>
                                <option value="">Cargando...</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="uso_cfdi" class="form-label">Uso de CFDI <span class="text-danger">*</span></label>
                            <select id="uso_cfdi" name="uso_cfdi" class="form-select" required
                                    @if(isset($facturaOriginal)) disabled @endif
                            >
                                <option value="">Cargando...</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECCIÓN DE CONCEPTOS --}}
                <h5 class="card-title text-primary mt-4">2. Conceptos</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;">Cantidad <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Producto <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Descripción <span class="text-danger">*</span></th>
                                <th style="width: 20%;">Clave Prod/Serv <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Valor Unitario <span class="text-danger">*</span></th>
                                <th style="width: 15%;">Importe</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="conceptos-container">
                            {{-- Las filas de conceptos se agregarán aquí dinámicamente --}}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button type="button" id="add-concepto" class="btn btn-success btn-sm" @if(isset($facturaOriginal)) style="display: none;" @endif>
                                        <i class="fa-solid fa-plus me-1"></i> Agregar Concepto
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- SECCIÓN DE TOTALES --}}
                <div class="row justify-content-end mt-3">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <th class="text-end">Subtotal:</th>
                                    <td class="text-end" id="display-subtotal">$0.00</td>
                                </tr>
                                <tr>
                                    <th class="text-end">IVA (16%):</th>
                                    <td class="text-end" id="display-iva">$0.00</td>
                                </tr>
                                <tr>
                                    <th class="text-end fs-5">Total:</th>
                                    <td class="text-end fs-5 fw-bold" id="display-total">$0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- --- INICIO: Cambio de Botones --- --}}
                <div class="mt-4 text-end">
                    <a href="{{ route('tenant.facturacion.cfdis.index') }}" class="btn btn-danger text-white">
                        <i class="fa-solid fa-xmark me-1"></i> Cancelar
                    </a>
                    <button type="submit" name="action" value="guardar" class="btn btn-secondary text-white" title="Guardar sin timbrar">
                        <i class="fa-solid fa-save me-1"></i> Guardar
                    </button>
                    <button type="button" id="btn-preview" class="btn btn-secondary text-white" title="Generar una vista previa en PDF (próximamente)">
                        <i class="fa-solid fa-eye me-1"></i> Vista Previa
                    </button>
                    <button type="button" id="btn-addenda" class="btn btn-secondary text-white" title="Añadir complemento de Addenda (próximamente)" disabled>
                        <i class="fa-solid fa-puzzle-piece me-1"></i> Addenda
                    </button>
                    <button type="submit" name="action" value="timbrar" class="btn btn-primary" title="Guardar y timbrar el CFDI">
                        <i class="fa-solid fa-stamp me-1"></i> Timbrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Plantilla para una nueva fila de concepto --}}
    <template id="concepto-template">
        <tr class="concepto-row">
            <td>
                <input type="number" name="conceptos[ID][cantidad]" class="form-control concept-cantidad" value="1" min="0.000001" step="any" required>
            </td>
            <td>
                <input type="text" name="conceptos[ID][producto]" class="form-control concept-producto" placeholder="Nombre del producto local" required>
            </td>
            <td>
                <input type="text" name="conceptos[ID][descripcion]" class="form-control concept-descripcion" required>
            </td>
            <td>
                <select name="conceptos[ID][clave_prod_serv]" class="form-control concept-prod-serv" required></select>
            </td>
            <td>
                <input type="number" name="conceptos[ID][valor_unitario]" class="form-control concept-valor-unitario" value="0.00" min="0" step="any" required>
            </td>
            <td>
                <input type="text" name="conceptos[ID][importe]" class="form-control concept-importe" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm remove-concepto">
                    <i class="fa-solid fa-trash-alt"></i>
                </button>
            </td>
        </tr>
    </template>

    @push('scripts')
    {{-- Pasamos las rutas de la API de forma segura a JavaScript --}}
    <script>
        Object.assign(window.apiUrls, {
            catalogos: "{{ route('tenant.api.facturacion.catalogos') }}",
            series: "{{ route('tenant.api.facturacion.series') }}",
            searchClients: "{{ route('tenant.api.facturacion.clientes.search') }}",
            searchProductos: "{{ route('tenant.api.facturacion.productos-servicios.search') }}"
        });
        window.createSerieUrl = "{{ route('tenant.facturacion.configuracion.series-folios.create') }}";
        window.createClientUrl = "{{ route('tenant.clientes.create') }}";
        @if(isset($facturaOriginal))
            // Para notas de crédito, el Uso de CFDI es fijo.
            window.usoCfdiCreditNote = 'G02'; // G02 - Devoluciones, descuentos o bonificaciones
        @endif
    </script>
    @vite(['resources/js/Modules/Facturacion/cfdi40/cfdi.js'])
    @endpush
</x-layouts.app>
