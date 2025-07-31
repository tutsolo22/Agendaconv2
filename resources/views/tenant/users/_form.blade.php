@csrf

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Nombre Completo') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Contraseña') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" @if(!isset($user)) required @endif>
            @if(isset($user))
                <div class="form-text">{{ __('Dejar en blanco para no cambiar la contraseña.') }}</div>
            @endif
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirmar Contraseña') }}</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" @if(!isset($user)) required @endif>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="role" class="form-label">{{ __('Rol') }}</label>
            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                <option value="" disabled selected>{{ __('Seleccione un rol...') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role', $user->roles->first()->name ?? '') == $role->name)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="sucursal_id" class="form-label">{{ __('Sucursal') }}</label>
            <select class="form-select @error('sucursal_id') is-invalid @enderror" id="sucursal_id" name="sucursal_id">
                <option value="">{{ __('Sin Asignar') }}</option>
                @foreach($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected(old('sucursal_id', $user->sucursal_id ?? '') == $sucursal->id)>
                        {{ $sucursal->nombre }}
                    </option>
                @endforeach
            </select>
            @error('sucursal_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('tenant.users.index') }}" class="btn btn-secondary">{{ __('Cancelar') }}</a>
    <button type="submit" class="btn btn-primary">{{ $submitText }}</button>
</div>