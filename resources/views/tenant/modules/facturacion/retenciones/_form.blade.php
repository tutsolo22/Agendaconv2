@csrf
<div class="row mb-3">
    <div class="col-md-6">
        <label for="cliente_id" class="form-label">Cliente</label>
        <select class="form-control" id="cliente_id" name="cliente_id" required data-search-url="{{ route('tenant.api.facturacion.clientes.search') }}">
            @if(isset($retencion) && $retencion->cliente)
                <option value="{{ $retencion->cliente->id }}" selected>{{ $retencion->cliente->nombre_completo }} ({{ $retencion->cliente->rfc }})</option>
            @endif
        </select>
    </div>
    <div class="col-md-3">
        <label for="serie_folio_id" class="form-label">Serie y Folio</label>
        <select class="form-select" id="serie_folio_id" name="serie_folio_id" required>
            @if(isset($retencion) && $retencion->serie_folio_id)
                <option value="{{ $retencion->serie_folio_id }}" selected>{{ $retencion->serie_folio->serie }}-{{ $retencion->serie_folio->folio }}</option>
            @endif
            {{-- Options will be loaded dynamically by TomSelect --}}
        </select>
    </div>
    <div class="col-md-3">
        <label for="fecha_exp" class="form-label">Fecha de Expedición</label>
        <input type="datetime-local" class="form-control" name="fecha_exp" value="{{ old('fecha_exp', isset($retencion) ? \Carbon\Carbon::parse($retencion->fecha_exp)->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}" required>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label for="cve_retenc" class="form-label">Clave de Retención</label>
        <select class="form-select" name="cve_retenc" required>
            <option value="">Seleccione una clave...</option>
            {{-- Opciones se cargarán desde el catálogo del SAT --}}
            <option value="14" @selected(old('cve_retenc', $retencion->cve_retenc ?? '') == '14')>14 - Dividendos o utilidades distribuidas</option>
            <option value="25" @selected(old('cve_retenc', $retencion->cve_retenc ?? '') == '25')>25 - Otro tipo de retenciones</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="desc_retenc" class="form-label">Descripción (si es "Otra retención")</label>
        <input type="text" class="form-control" name="desc_retenc" value="{{ old('desc_retenc', $retencion->desc_retenc ?? '') }}">
    </div>
</div>

<hr>

<h4>Totales</h4>
<div class="row mb-3">
    <div class="col-md-6">
        <label for="monto_total_operacion" class="form-label">Monto Total de la Operación</label>
        <input type="number" step="0.0001" class="form-control" name="monto_total_operacion" id="monto_total_operacion" value="{{ old('monto_total_operacion', $retencion->monto_total_operacion ?? '0.0000') }}" readonly>
    </div>
    <div class="col-md-6">
        <label for="monto_total_retenido" class="form-label">Monto Total Retenido</label>
        <input type="number" step="0.0001" class="form-control" name="monto_total_retenido" id="monto_total_retenido" value="{{ old('monto_total_retenido', $retencion->monto_total_retenido ?? '0.0000') }}" readonly>
    </div>
</div>

<hr>

<h4>Impuestos Retenidos</h4>
<div class="table-responsive mb-3" id="impuestos-container" data-initial-index="{{ (isset($retencion) && $retencion->impuestos) ? $retencion->impuestos->count() : 0 }}">
    <table class="table table-bordered" id="impuestos-table">
        <thead>
            <tr>
                <th>Base del Impuesto</th>
                <th>Impuesto</th>
                <th>Tipo de Pago</th>
                <th>Monto Retenido</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($retencion) && $retencion->impuestos->isNotEmpty())
                @foreach($retencion->impuestos as $index => $impuesto)
                    <tr data-index="{{ $index }}">
                        <td><input type="number" step="0.01" name="impuestos[{{ $index }}][base_ret]" class="form-control base-ret" value="{{ $impuesto->base_ret }}" required></td>
                        <td>
                            <select name="impuestos[{{ $index }}][impuesto]" class="form-select" required>
                                <option value="01" @selected($impuesto->impuesto == '01')>01 - ISR</option>
                                <option value="02" @selected($impuesto->impuesto == '02')>02 - IVA</option>
                                <option value="03" @selected($impuesto->impuesto == '03')>03 - IEPS</option>
                            </select>
                        </td>
                        <td>
                            <select name="impuestos[{{ $index }}][tipo_pago_ret]" class="form-select" required>
                                <option value="Pago provisional" @selected($impuesto->tipo_pago_ret == 'Pago provisional')>Pago provisional</option>
                                <option value="Pago definitivo" @selected($impuesto->tipo_pago_ret == 'Pago definitivo')>Pago definitivo</option>
                            </select>
                        </td>
                        <td><input type="number" step="0.01" name="impuestos[{{ $index }}][monto_ret]" class="form-control monto-ret" value="{{ $impuesto->monto_ret }}" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-impuesto-btn"><i class="fa-solid fa-trash"></i></button></td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<button type="button" class="btn btn-secondary" id="add-impuesto-btn">
    <i class="fa-solid fa-plus me-1"></i> Añadir Impuesto
</button>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">Guardar Borrador</button>
    <a href="{{ route('tenant.facturacion.retenciones.index') }}" class="btn btn-secondary">Cancelar</a>
</div>

<script type="module">
    import TomSelect from 'tom-select';
    import 'tom-select/dist/css/tom-select.bootstrap5.min.css';
    import { initClientSearchSelect2, loadSeriesAndFolios } from '../../js/Modules/Facturacion/shared/facturacion-common.js';

    document.addEventListener('DOMContentLoaded', function () {
        // Configuración de URLs para retenciones
        window.retencionesConfig = {
            urls: {
                searchClients: '{{ route("tenant.api.facturacion.clientes.search") }}',
                series: '{{ route("tenant.api.facturacion.series") }}',
                createSerieUrl: '{{ route("tenant.facturacion.configuracion.series-folios.create") }}'
            }
        };

        // Inicializar Select2 para clientes
        const clienteSelect = $('#cliente_id');
        initClientSearchSelect2(clienteSelect, window.retencionesConfig.urls.searchClients);

        // Inicializar TomSelect para series y folios
        const serieSelect = new TomSelect('#serie_folio_id', { placeholder: 'Cargando Series...' });
        loadSeriesAndFolios(serieSelect, window.retencionesConfig.urls.series, window.retencionesConfig.urls.createSerieUrl);

        // Lógica para añadir/eliminar impuestos (existente)
        const impuestosContainer = document.getElementById('impuestos-container');
        const addImpuestoBtn = document.getElementById('add-impuesto-btn');
        let impuestoIndex = parseInt(impuestosContainer.dataset.initialIndex || 0);

        addImpuestoBtn.addEventListener('click', addImpuestoRow);
        impuestosContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-impuesto-btn')) {
                e.target.closest('tr').remove();
            }
        });

        function addImpuestoRow() {
            const newRow = document.createElement('tr');
            newRow.dataset.index = impuestoIndex;
            newRow.innerHTML = `
                <td><input type="number" step="0.01" name="impuestos[${impuestoIndex}][base_ret]" class="form-control base-ret" value="0.00" required></td>
                <td>
                    <select name="impuestos[${impuestoIndex}][impuesto]" class="form-select" required>
                        <option value="01">01 - ISR</option>
                        <option value="02">02 - IVA</option>
                        <option value="03">03 - IEPS</option>
                    </select>
                </td>
                <td>
                    <select name="impuestos[${impuestoIndex}][tipo_pago_ret]" class="form-select" required>
                        <option value="Pago provisional">Pago provisional</option>
                        <option value="Pago definitivo">Pago definitivo</option>
                    </select>
                </td>
                <td><input type="number" step="0.01" name="impuestos[${impuestoIndex}][monto_ret]" class="form-control monto-ret" value="0.00" required></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-impuesto-btn"><i class="fa-solid fa-trash"></i></button></td>
            `;
            document.getElementById('impuestos-table').querySelector('tbody').appendChild(newRow);
            impuestoIndex++;
        }
    });
</script>
