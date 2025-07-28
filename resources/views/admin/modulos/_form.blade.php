<div x-data="{
    nombre: '{{ old('nombre', $modulo->nombre) }}',
    slug: '{{ old('slug', $modulo->slug) }}',
    slugManuallyEdited: {{ old('slug') || $modulo->exists ? 'true' : 'false' }},
    generateSlug() {
        if (!this.slugManuallyEdited) {
            this.slug = slugify(this.nombre);
        }
    }
}">
    @csrf

    <!-- Nombre -->
    <div>
        <x-input-label for="nombre" :value="__('Nombre del Módulo')" />
        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" x-model="nombre" @input="generateSlug()" required autofocus />
        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
    </div>

    <!-- Slug -->
    <div class="mt-4">
        <x-input-label for="slug" :value="__('Slug (Identificador URL)')" />
        <x-text-input id="slug" class="block mt-1 w-full" type="text" name="slug" x-model="slug" @input="slugManuallyEdited = true" required />
        <p class="text-sm text-gray-500 mt-1">Solo letras minúsculas, números y guiones (ej: citas-medicas).</p>
        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
    </div>

    <!-- Descripción -->
    <div class="mt-4">
        <x-input-label for="descripcion" :value="__('Descripción')" />
        <textarea id="descripcion" name="descripcion" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('descripcion', $modulo->descripcion) }}</textarea>
        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
    </div>

    <!-- Icono -->
    <div class="mt-4">
        <x-input-label for="icono" :value="__('Icono (Clase de Font Awesome)')" />
        <x-text-input id="icono" class="block mt-1 w-full" type="text" name="icono" :value="old('icono', $modulo->icono)" />
        <p class="text-sm text-gray-500 mt-1">Ejemplo: `fa-solid fa-stethoscope`.</p>
        <x-input-error :messages="$errors->get('icono')" class="mt-2" />
    </div>

    <!-- Estado -->
    <div class="mt-4">
        <label for="is_active" class="inline-flex items-center">
            <input id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="is_active" value="1" @checked(old('is_active', $modulo->is_active))>
            <span class="ms-2 text-sm text-gray-600">{{ __('Activo') }}</span>
        </label>
    </div>

    <div class="flex items-center justify-end mt-6">
        <a href="{{ route('admin.modulos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
        <x-primary-button>
            {{ $submitText ?? 'Guardar Módulo' }}
        </x-primary-button>
    </div>
</div>