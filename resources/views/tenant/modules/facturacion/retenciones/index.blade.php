<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-file-signature me-2"></i>
            Retenciones y Pagos
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('tenant.facturacion.retenciones.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nueva Retención
                </a>
            </div>

            @include('partials.flash-messages')

           <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Serie y Folio</th>
                            <th>Cliente</th>
                            <th>Fecha de Exp.</th>
                            <th>Monto Retenido</th>
                            <th>Status</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($retenciones as $retencion)
                            <tr>
                                <td>{{ $retencion->serie }}-{{ $retencion->folio }}</td>
                                <td>{{ $retencion->cliente->nombre_completo }}</td>
                                <td>{{ \Carbon\Carbon::parse($retencion->fecha_exp)->format('d/m/Y') }}</td>
                                <td>${{ number_format($retencion->monto_total_retenido, 2) }}</td>
                                <td>
                                    <span class="badge bg-{{ $retencion->status == 'timbrado' ? 'success' : ($retencion->status == 'cancelado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($retencion->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('tenant.facturacion.retenciones.show', $retencion) }}" class="btn btn-sm btn-info" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No se encontraron retenciones.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $retenciones->links() }}
        </div>
    </div>
</x-layouts.app>