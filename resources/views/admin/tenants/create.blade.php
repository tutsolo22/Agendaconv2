<x-layouts.admin>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            {{ __('Nuevo Tenant') }}
        </h2>
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Información del Tenant y Administrador</span>
            <a href="{{ route('admin.tenants.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Volver al listado
            </a>
        </div>
        <div class="card-body">
            {{-- Mostraremos los errores de validación aquí si los hubiera --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.tenants.store') }}" method="POST">
                @csrf
                
                <h5 class="card-title mb-3 border-bottom pb-2">Datos del Tenant</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Nombre de la Empresa <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="domain" class="form-label">Dominio (subdominio) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('domain') is-invalid @enderror" id="domain" name="domain" value="{{ old('domain') }}" required>
                            <span class="input-group-text">.{{ config('app.domain', 'localhost') }}</span>
                        </div>
                        @error('domain')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Solo letras minúsculas, números y guiones. Sin espacios.</div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="card-title mb-3 border-bottom pb-2">Datos del Usuario Administrador del Tenant</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="user_name" class="form-label">Nombre del Administrador <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="user_email" class="form-label">Email del Administrador <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('user_email') is-invalid @enderror" id="user_email" name="user_email" value="{{ old('user_email') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Guardar Tenant</button>
                    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>