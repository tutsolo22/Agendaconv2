<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Tenant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.tenants.store') }}">
                        @csrf

                        <!-- Tenant Details -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Detalles del Tenant</h3>
                            <div class="mt-4">
                                <x-input-label for="name" :value="__('Nombre del Tenant')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Administrator Details -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">Administrador del Tenant</h3>
                            <p class="mt-1 text-sm text-gray-600">Este será el usuario principal para gestionar el tenant.</p>

                            <div class="mt-4">
                                <x-input-label for="admin_name" :value="__('Nombre del Administrador')" />
                                <x-text-input id="admin_name" class="block mt-1 w-full" type="text" name="admin_name" :value="old('admin_name')" required />
                                <x-input-error :messages="$errors->get('admin_name')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="admin_email" :value="__('Email del Administrador')" />
                                <x-text-input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email" :value="old('admin_email')" required />
                                <x-input-error :messages="$errors->get('admin_email')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="admin_password" :value="__('Contraseña')" />
                                <x-text-input id="admin_password" class="block mt-1 w-full" type="password" name="admin_password" required />
                                <x-input-error :messages="$errors->get('admin_password')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="admin_password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input id="admin_password_confirmation" class="block mt-1 w-full" type="password" name="admin_password_confirmation" required />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tenants.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <x-primary-button>
                                {{ __('Crear Tenant') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>