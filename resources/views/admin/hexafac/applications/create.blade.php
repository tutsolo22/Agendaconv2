<x-layouts.app>
    <x-slot name="header">
        <h2 class="h4 fw-bold">
            <i class="fa-solid fa-plus me-2"></i>
            {{ __('Crear Nueva Aplicación Cliente de HexaFac') }}
        </h2>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fa-solid fa-pen-to-square me-2"></i>
                Detalles de la Aplicación
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.hexafac.applications.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre de la Aplicación <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <a href="{{ route('admin.hexafac.applications.index') }}" class="btn btn-danger">
                        <i class="fa-solid fa-xmark me-1"></i>
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-1"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>