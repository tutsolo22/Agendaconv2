@csrf

<!-- Tenant -->
<div>
    <x-input-label for="tenant_id" :value="__('Tenant (Empresa)')" />
    <select id="tenant_id" name="tenant_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
        <option value="">Sin Asignar (Generar solo código)</option>
        @foreach($tenants as $tenant)
            <option value="{{ $tenant->id }}" @selected(old('tenant_id', $licencia->tenant_id) == $tenant->id)>
                {{ $tenant->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('tenant_id')" class="mt-2" />
</div>

<!-- Módulo -->
<div class="mt-4">
    <x-input-label for="modulo_id" :value="__('Módulo')" />
    <select id="modulo_id" name="modulo_id" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
        <option value="">Seleccione un Módulo</option>
        @foreach($modulos as $modulo)
            <option value="{{ $modulo->id }}" @selected(old('modulo_id', $licencia->modulo_id) == $modulo->id)>
                {{ $modulo->nombre }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('modulo_id')" class="mt-2" />
</div>

<!-- Fecha de Expiración -->
<div class="mt-4">
    <x-input-label for="fecha_fin" :value="__('Fecha de Expiración')" />
    <x-text-input id="fecha_fin" class="block mt-1 w-full" type="date" name="fecha_fin" :value="old('fecha_fin', $licencia->fecha_fin ? \Carbon\Carbon::parse($licencia->fecha_fin)->format('Y-m-d') : '')" required />
    <x-input-error :messages="$errors->get('fecha_fin')" class="mt-2" />
</div>

<!-- Máximo de Usuarios -->
<div class="mt-4">
    <x-input-label for="limite_usuarios" :value="__('Límite de Usuarios Permitidos')" />
    <x-text-input id="limite_usuarios" class="block mt-1 w-full" type="number" name="limite_usuarios" :value="old('limite_usuarios', $licencia->limite_usuarios ?? 1)" required min="1" />
    <x-input-error :messages="$errors->get('limite_usuarios')" class="mt-2" />
</div>

<!-- Estado -->
<div class="mt-4">
    <label for="is_active" class="inline-flex items-center">
        {{-- Usamos un valor por defecto de 'true' para nuevas licencias --}}
        <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" @checked(old('is_active', $licencia->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-600">{{ __('Licencia Activa') }}</span>
    </label>
</div>

<div class="flex items-center justify-end mt-6">
    <a href="{{ route('admin.licencias.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
    <x-primary-button>
        {{ $submitText ?? 'Guardar Licencia' }}
    </x-primary-button>
</div>