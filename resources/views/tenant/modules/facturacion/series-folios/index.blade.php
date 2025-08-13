<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                <i class="fa-solid fa-list-ol me-2"></i>
                Administrar Series y Folios
            </h2>
            {{-- La ruta correcta debe incluir el prefijo del grupo 'configuracion.' --}}
            <a href="{{ route('tenant.facturacion.configuracion.series-folios.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>Nueva Serie
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            @include('partials.flash-messages')
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Serie</th>
                            <th>Tipo Comprobante</th>
                            <th>Folio Actual</th>
                            <th>Sucursal Asignada</th>
                            <th>Activo</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($series as $serie)
                            <tr>
                                <td>{{ $serie->serie }}</td>
                                <td>{{ $serie->tipo_comprobante_texto }}</td>
                                <td>{{ $serie->folio_actual }}</td>
                                <td>{{ $serie->sucursal->nombre ?? 'Todas' }}</td>
                                <td>
                                    @if($serie->is_active)
                                        <span class="badge bg-success">Sí</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('tenant.facturacion.configuracion.series-folios.edit', $serie) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fa-solid fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('tenant.facturacion.configuracion.series-folios.destroy', $serie) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Está seguro de que desea eliminar esta serie?');">
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
                                <td colspan="6" class="text-center">No hay series y folios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($series->hasPages())
                <div class="mt-3">{{ $series->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>