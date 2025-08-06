<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-cash-register me-2"></i>
                Registro de Ventas al Público
            </h2>
            <a href="{{ route('tenant.facturacion.ventas-publico.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Registrar Venta
            </a>
        </div>
    </x-slot>

    @include('partials.flash-messages')

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Folio Venta</th>
                            <th>Fecha</th>
                            <th class="text-end">Total</th>
                            <th>Factura Global</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ventas as $venta)
                            <tr>
                                <td>{{ $venta->folio_venta }}</td>
                                <td>{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</td>
                                <td class="text-end">${{ number_format($venta->total, 2) }}</td>
                                <td>
                                    @if($venta->cfdiGlobal)
                                        <a href="{{ route('tenant.facturacion.cfdis.show', $venta->cfdiGlobal) }}">
                                            {{ $venta->cfdiGlobal->serie }}-{{ $venta->cfdiGlobal->folio }}
                                        </a>
                                    @else
                                        <span class="text-muted">Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if(!$venta->cfdi_global_id)
                                        <a href="{{ route('tenant.facturacion.ventas-publico.edit', $venta) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fa-solid fa-pencil-alt"></i></a>
                                        <form action="{{ route('tenant.facturacion.ventas-publico.destroy', $venta) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta venta?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay ventas registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $ventas->links() }}
        </div>
    </div>
</x-layouts.app>