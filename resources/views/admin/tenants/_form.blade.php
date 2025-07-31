@csrf

{{-- Tenant Details --}}
<h5 class="card-title mb-3">Datos del Tenant</h5>
<div class="row">
    <div class="col-md-12">
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nombre del Tenant') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $tenant->name ?? '') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr class="my-4">

{{-- Admin User Details --}}
<h5 class="card-title mb-3">Datos del Administrador</h5>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="admin_name" class="form-label">{{ __('Nombre del Administrador') }}</label>
            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" id="admin_name" name="admin_name" value="{{ old('admin_name', $admin->name ?? '') }}" required>
            @error('admin_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="admin_email" class="form-label">{{ __('Email del Administrador') }}</label>
            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" id="admin_email" name="admin_email" value="{{ old('admin_email', $admin->email ?? '') }}" required>
            @error('admin_email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="admin_password" class="form-label">{{ __('Contraseña') }}</label>
            <input type="password" class="form-control @error('admin_password') is-invalid @enderror" id="admin_password" name="admin_password" @if(!isset($tenant)) required @endif>
            @if(isset($tenant))
                <div class="form-text">{{ __('Dejar en blanco para no cambiar la contraseña.') }}</div>
            @endif
            @error('admin_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="admin_password_confirmation" class="form-label">{{ __('Confirmar Contraseña') }}</label>
            <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" @if(!isset($tenant)) required @endif>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
</div>