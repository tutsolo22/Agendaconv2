<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-hashtag me-2"></i>
                Gestión de Series y Folios
            </h2>
            <a href="{{ route('tenant.facturacion.series-folios.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Crear Serie
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
                            <th>Serie</th>
                            <th>Folio Actual</th>
                            <th>Sucursal Asignada</th>
                            <th>Estatus</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($series as $serie)
                            <tr>
                                <td>{{ $serie->serie }}</td>
                                <td>{{ $serie->folio_actual }}</td>
                                <td>{{ $serie->sucursal->nombre ?? 'General' }}</td>
                                <td>
                                    @if($serie->is_active)
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-danger">Inactiva</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.facturacion.series-folios.edit', $serie) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil-alt"></i>
                                    </a>
                                    <form action="{{ route('tenant.facturacion.series-folios.destroy', $serie) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar esta serie?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No hay series configuradas. <a href="{{ route('tenant.facturacion.series-folios.create') }}">Crea la primera</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($series->hasPages())
            <div class="card-footer">
                {{ $series->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>