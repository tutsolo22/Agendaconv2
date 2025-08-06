<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-invoice-dollar me-2"></i>
            Complementos de Pago
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('tenant.facturacion.pagos.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nuevo Complemento de Pago
                </a>
            </div>

            @include('partials.flash-messages')

           <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Serie y Folio</th>
                            <th>Cliente</th>
                            <th>Fecha de Pago</th>
                            <th>Monto</th>
                            <th>Status</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pagos as $pago)
                            <tr>
                                <td>{{ $pago->serie }}-{{ $pago->folio }}</td>
                                <td>{{ $pago->cliente->nombre_completo }}</td>
                                <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i:s') }}</td>
                                <td>${{ number_format($pago->monto, 2) }} {{ $pago->moneda }}</td>
                                <td>
                                    <span class="badge bg-{{ $pago->status == 'timbrado' ? 'success' : ($pago->status == 'cancelado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($pago->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('tenant.facturacion.pagos.show', $pago) }}" class="btn btn-sm btn-info" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                        @if ($pago->status == 'borrador')
                                            <a href="{{ route('tenant.facturacion.pagos.edit', $pago) }}" class="btn btn-sm btn-primary" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                            <form action="{{ route('tenant.facturacion.pagos.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este borrador?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        @endif
                                        @if ($pago->status == 'timbrado')
                                            <a href="{{ route('tenant.facturacion.pagos.download.xml', $pago) }}" class="btn btn-sm btn-dark" title="Descargar XML"><i class="fa-solid fa-file-code"></i></a>
                                            <a href="{{ route('tenant.facturacion.pagos.download.pdf', $pago) }}" class="btn btn-sm btn-danger" title="Descargar PDF"><i class="fa-solid fa-file-pdf"></i></a>
                                            <form action="{{ route('tenant.facturacion.pagos.cancelar', $pago) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas cancelar este complemento?');">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Cancelar CFDI"><i class="fa-solid fa-ban"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron complementos de pago.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
 {{ $pagos->links() }}
        </div>
    </div>
</x-layouts.app>