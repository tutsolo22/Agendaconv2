<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i>
                Recibos de Nómina
            </h2>
            <a href="{{ route('tenant.facturacion.nomina.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-1"></i>
                Nuevo Recibo
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Folio</th>
                            <th>Empleado</th>
                            <th>Fecha de Pago</th>
                            <th>Total</th>
                            <th>UUID</th>
                            <th>Status</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Aquí se iterará sobre los recibos de nómina --}}
                        <tr>
                            <td colspan="7" class="text-center">No hay recibos de nómina para mostrar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
