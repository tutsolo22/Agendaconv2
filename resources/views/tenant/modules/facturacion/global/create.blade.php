<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-globe me-2"></i>
            Crear Factura Global
        </h2>
    </x-slot>

    <form action="{{ route('tenant.facturacion.store-global') }}" method="POST" id="global-invoice-form">
        @csrf
        <div class="card mb-4">
            <div class="card-header">
                <h5>1. Seleccione el Periodo a Facturar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="periodicidad" class="form-label">Periodicidad</label>
                        <select name="periodicidad" id="periodicidad" class="form-select" required>
                            @foreach($periodicidades as $clave => $valor)
                                <option value="{{ $clave }}">{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="meses" class="form-label">Mes</label>
                        <select name="meses" id="meses" class="form-select" required>
                            @foreach($meses as $clave => $valor)
                                <option value="{{ $clave }}" @selected(date('m') == $clave)>{{ $valor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" name="anio" id="anio" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                </div>
                <button type="button" id="search-ventas" class="btn btn-info">
                    <i class="fa-solid fa-search me-2"></i>Buscar Ventas no Facturadas
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>2. Seleccione las Ventas a Incluir</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all"></th>
                                <th>Folio Venta</th>
                                <th>Fecha</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody id="ventas-container">
                            {{-- Las ventas se cargarán aquí vía AJAX --}}
                        </tbody>
                    </table>
                </div>
                <div id="no-ventas" class="text-center text-muted p-4" style="display: none;">
                    No se encontraron ventas para el periodo seleccionado.
                </div>
            </div>
            <div class="card-footer">
                <div class="row">
                    <div class="col-md-6">
                        <label for="serie_folio_id" class="form-label">Serie para la Factura Global</label>
                        <select name="serie_folio_id" id="serie_folio_id" class="form-select" required>
                            @foreach($series as $serie)
                                <option value="{{ $serie->id }}">Serie {{ $serie->serie }} (Folio actual: {{ $serie->folio_actual }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5>Total a Facturar: <span id="total-facturar" class="fw-bold">$0.00</span></h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-end mt-4">
            <a href="{{ route('tenant.facturacion.cfdis.index') }}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary" id="submit-button" disabled>
                <i class="fa-solid fa-save me-2"></i>Crear Borrador Global
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchBtn = document.getElementById('search-ventas');
        const container = document.getElementById('ventas-container');
        const noVentasMsg = document.getElementById('no-ventas');
        const selectAllCheckbox = document.getElementById('select-all');
        const totalDisplay = document.getElementById('total-facturar');
        const submitButton = document.getElementById('submit-button');

        searchBtn.addEventListener('click', function () {
            const mes = document.getElementById('meses').value;
            const anio = document.getElementById('anio').value;
            searchBtn.disabled = true;
            searchBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Buscando...';

            fetch(`{{ route('tenant.facturacion.cfdis.search-ventas') }}?mes=${mes}&anio=${anio}`)
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.length > 0) {
                        noVentasMsg.style.display = 'none';
                        data.forEach(venta => {
                            const row = `
                                <tr>
                                    <td><input type="checkbox" class="venta-checkbox" name="ventas_ids[]" value="${venta.id}" data-total="${venta.total}"></td>
                                    <td>${venta.folio_venta}</td>
                                    <td>${new Date(venta.fecha + 'T00:00:00').toLocaleDateString('es-MX')}</td>
                                    <td class="text-end">$${parseFloat(venta.total).toFixed(2)}</td>
                                </tr>`;
                            container.insertAdjacentHTML('beforeend', row);
                        });
                    } else {
                        noVentasMsg.style.display = 'block';
                    }
                })
                .finally(() => {
                    searchBtn.disabled = false;
                    searchBtn.innerHTML = '<i class="fa-solid fa-search me-2"></i>Buscar Ventas no Facturadas';
                    updateTotal();
                });
        });

        selectAllCheckbox.addEventListener('change', function () {
            container.querySelectorAll('.venta-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotal();
        });

        container.addEventListener('change', function (e) {
            if (e.target.classList.contains('venta-checkbox')) {
                updateTotal();
            }
        });

        function updateTotal() {
            let total = 0;
            const checkedVentas = container.querySelectorAll('.venta-checkbox:checked');
            checkedVentas.forEach(checkbox => {
                total += parseFloat(checkbox.dataset.total);
            });
            totalDisplay.textContent = `$${total.toFixed(2)}`;
            submitButton.disabled = checkedVentas.length === 0;
        }
    });
    </script>
    @endpush
</x-layouts.app>