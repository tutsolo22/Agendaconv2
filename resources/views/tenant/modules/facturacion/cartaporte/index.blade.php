<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-truck-ramp-box me-2"></i>
            Cartas Porte
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('tenant.facturacion.cartaporte.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>
                    Nueva Carta Porte
                </a>
            </div>

            @include('partials.flash-messages')

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>CFDI Relacionado</th>
                            <th>Versión</th>
                            <th>Transporte Internacional</th>
                            <th>ID CCP</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cartaportes as $cartaporte)
                            <tr>
                                <td>{{ $cartaporte->id }}</td>
                                <td>{{ $cartaporte->facturacion_cfdi_id ?? 'N/A' }}</td>
                                <td>{{ $cartaporte->version }}</td>
                                <td>{{ $cartaporte->transp_internac }}</td>
                                <td>{{ $cartaporte->id_ccp }}</td>
                                <td>
                                    <span class="badge bg-{{ $cartaporte->status == 'timbrado' ? 'success' : ($cartaporte->status == 'cancelado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($cartaporte->status) }}
                                    </span>
                                </td>
                                <td>{{ $cartaporte->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('tenant.facturacion.cartaporte.show', $cartaporte->id) }}" class="btn btn-sm btn-info" title="Ver"><i class="fa-solid fa-eye"></i></a>
                                        @if ($cartaporte->status == 'borrador')
                                            <a href="{{ route('tenant.facturacion.cartaporte.edit', $cartaporte->id) }}" class="btn btn-sm btn-primary" title="Editar"><i class="fa-solid fa-pencil"></i></a>
                                            <form action="{{ route('tenant.facturacion.cartaporte.destroy', $cartaporte->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este borrador?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        @endif
                                        {{-- Add download/cancel buttons for 'timbrado' status if needed --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No se encontraron cartas porte.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- {{ $cartaportes->links() }} --}}
        </div>
    </div>
</x-layouts.app>
