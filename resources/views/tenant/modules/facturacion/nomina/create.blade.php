<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Nuevo Recibo de Nómina
        </h2>
    </x-slot>

    <form action="{{-- route('tenant.facturacion.nomina.store') --}}" method="POST" id="create-nomina-form">
        @csrf

        {{-- Datos Generales --}}
        <div class="card mb-4">
            <div class="card-header">Datos Generales del Recibo</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label for="tipo_nomina">Tipo de Nómina</label>
                        <select class="form-select" id="tipo_nomina" name="tipo_nomina">
                            {{-- Opciones de catálogos del SAT --}}
                        </select>
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="fecha_pago">Fecha de Pago</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago">
                    </div>
                    <div class="col-md-4 form-group">
                        <label for="periodicidad_pago">Periodicidad de Pago</label>
                        <select class="form-select" id="periodicidad_pago" name="periodicidad_pago">
                            {{-- Opciones de catálogos del SAT --}}
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Empleado (Receptor) --}}
        <div class="card mb-4">
            <div class="card-header">Datos del Empleado</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <label for="empleado_id">Buscar Empleado</label>
                        <select id="empleado_id" name="empleado_id">
                            {{-- Búsqueda de empleados con TomSelect --}}
                        </select>
                    </div>
                </div>
                {{-- Aquí se podría mostrar info del empleado seleccionado --}}
            </div>
        </div>

        {{-- Percepciones --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Percepciones</span>
                <button type="button" class="btn btn-sm btn-success" id="add-percepcion">+ Agregar Percepción</button>
            </div>
            <div class="card-body">
                <table class="table" id="percepciones-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Clave</th>
                            <th>Concepto</th>
                            <th>Importe Gravado</th>
                            <th>Importe Exento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Filas dinámicas de percepciones --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Deducciones --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Deducciones</span>
                <button type="button" class="btn btn-sm btn-danger" id="add-deduccion">+ Agregar Deducción</button>
            </div>
            <div class="card-body">
                <table class="table" id="deducciones-table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Clave</th>
                            <th>Concepto</th>
                            <th>Importe</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Filas dinámicas de deducciones --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Otros Pagos --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                <span>Otros Pagos</span>
                <button type="button" class="btn btn-sm btn-info" id="add-otropago">+ Agregar Otro Pago</button>
            </div>
            <div class="card-body">
                <table class="table" id="otrospagos-table">
                     <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Clave</th>
                            <th>Concepto</th>
                            <th>Importe</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Filas dinámicas de otros pagos --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{-- route('tenant.facturacion.nomina.index') --}}" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-success">Timbrar Recibo</button>
        </div>

    </form>

    @push('scripts')
        <script>
            window.nominaApiUrls = {
                catalogs: "{{ route('tenant.facturacion.api.nomina.catalogs') }}",
                searchEmpleados: "{{ route('tenant.facturacion.api.nomina.search.empleados') }}"
            };
        </script>
        @vite(['resources/js/Modules/Facturacion/nomina/nomina.js'])
    @endpush

</x-layouts.app>
