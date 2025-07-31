<x-layouts.app>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 fw-bold">
                {{ __('Historial de Licencias') }}
            </h2>
            <a href="{{ route('tenant.licencias.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Volver a Mis Licencias') }}
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Mis Licencias Adquiridas') }}</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Módulo</th>
                            <th scope="col" class="text-center">Estado</th>
                            <th scope="col">Fecha de Activación</th>
                            <th scope="col">Fecha de Expiración</th>
                            <th scope="col" class="text-center">Usuarios</th>
                            <th scope="col">Código de Licencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($licencias as $licencia)
                            <tr>
                                <td>
                                    <i class="fa-solid {{ $licencia->modulo->icono ?? 'fa-cube' }} fa-fw me-2 text-secondary"></i>
                                    {{ $licencia->modulo->nombre }}
                                </td>
                                <td class="text-center">
                                    @if ($licencia->is_active && (is_null($licencia->fecha_fin) || \Carbon\Carbon::parse($licencia->fecha_fin)->isFuture()))
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-danger">Inactiva / Expirada</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $licencia->fecha_inicio ? \Carbon\Carbon::parse($licencia->fecha_inicio)->format('d/m/Y') : 'N/A' }}
                                </td>
                                <td>
                                    {{ $licencia->fecha_fin ? \Carbon\Carbon::parse($licencia->fecha_fin)->format('d/m/Y') : 'Permanente' }}
                                </td>
                                <td class="text-center">
                                    {{ $licencia->limite_usuarios ?? 'Ilimitados' }}
                                </td>
                                <td>
                                    <span class="font-monospace small">{{ $licencia->codigo_licencia }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No se ha adquirido ninguna licencia hasta el momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($licencias->hasPages())
                <div class="mt-3">{{ $licencias->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>