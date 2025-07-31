<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Mis Licencias') }}
        </h2>
    </x-slot>

        <!-- Mensajes de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulario de Activación -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Activar Nueva Licencia') }}</h5>
            <p class="card-text text-muted">{{ __('Si ha adquirido una nueva licencia, puede activarla aquí para desbloquear nuevos módulos.') }}</p>
            <form action="{{ route('tenant.licencias.activar') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" id="codigo_licencia" name="codigo_licencia" class="form-control @error('codigo_licencia') is-invalid @enderror" placeholder="Pegue aquí su código de licencia" required>
                    <button class="btn btn-primary" type="submit"><i class="fa-solid fa-key me-2"></i>{{ __('Activar') }}</button>
                    @error('codigo_licencia')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
    </div>
    <!-- Lista de Licencias Adquiridas -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Módulos Licenciados') }}</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Módulo</th>
                            <th scope="col">Fecha de Expiración</th>
                            <th scope="col" class="text-center">Usuarios Permitidos</th>
                            <th scope="col" class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($licencias as $licencia)
                            <tr>
                                <td>
                                    <i class="fa-solid {{ $licencia->modulo->icono ?? 'fa-cube' }} fa-fw me-2 text-secondary"></i>
                                    {{ $licencia->modulo->nombre }}
                                </td>
                                <td>
                                    {{ $licencia->fecha_fin ? \Carbon\Carbon::parse($licencia->fecha_fin)->format('d/m/Y') : 'Permanente' }}
                                </td>
                                <td class="text-center">
                                    {{ $licencia->limite_usuarios }}
                                </td>
                                <td class="text-center">
                                    @if ($licencia->is_active && (is_null($licencia->fecha_fin) || \Carbon\Carbon::parse($licencia->fecha_fin)->isFuture()))
                                        <span class="badge bg-success">Activa</span>
                                    @else
                                        <span class="badge bg-danger">Inactiva / Expirada</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Aún no tiene licencias activas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>