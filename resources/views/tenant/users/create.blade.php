<x-layouts.admin>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Crear Nuevo Usuario') }}
        </h2>
    </x-slot>

    {{-- Alerta si se ha alcanzado el límite de usuarios --}}
    @if ($limitReached)
        <div class="alert alert-danger" role="alert">
            Ha alcanzado el límite máximo de <strong>{{ $maxUsers }}</strong> usuarios permitidos por su licencia. No puede crear nuevos usuarios.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            {{-- Deshabilita el formulario si se alcanzó el límite --}}
            <form action="{{ route('tenant.users.store') }}" method="POST">
                @csrf
                <fieldset {{ $limitReached ? 'disabled' : '' }}>
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Nombre') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('Correo Electrónico') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Contraseña') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('Confirmar Contraseña') }}</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary me-2">{{ __('Cancelar') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Crear Usuario') }}</button>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</x-layouts.admin>