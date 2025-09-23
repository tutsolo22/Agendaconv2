<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Administración de HexaFac') }}
        </h2>
    </x-slot>

    <style>
        /* Paleta de Colores HexaFac */
        :root {
            --hexafac-black: #000000;
            --hexafac-gold: #DAA520;
            --hexafac-gray: #808080;
            --hexafac-navy: #000080;
            --hexafac-light-gray: #f3f4f6; /* From tailwind gray-100 */
            --hexafac-white: #ffffff;
        }

        .bg-hexafac-navy {
            background-color: var(--hexafac-navy);
        }

        .text-hexafac-gold {
            color: var(--hexafac-gold);
        }
        
        .border-hexafac-gold {
            border-color: var(--hexafac-gold);
        }

        .card {
            background-color: var(--hexafac-white);
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            padding: 1.5rem;
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
             transform: translateY(-5px);
             box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -2px rgb(0 0 0 / 0.1);
        }

        .card-header {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--hexafac-navy);
            border-bottom: 1px solid var(--hexafac-light-gray);
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Card: Administrar Aplicaciones -->
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-mobile-alt mr-2"></i>Administrar Aplicaciones
                            </div>
                            <p class="text-gray-600">
                                Registra y gestiona las aplicaciones cliente que se conectarán a la API de HexaFac.
                            </p>
                            <div class="mt-4">
                                <a href="#" class="text-white bg-hexafac-navy hover:opacity-90 font-bold py-2 px-4 rounded">
                                    Gestionar
                                </a>
                            </div>
                        </div>

                        <!-- Card: API Keys -->
                        <div class="card">
                            <div class="card-header">
                               <i class="fas fa-key mr-2"></i>API Keys y Seguridad
                            </div>
                            <p class="text-gray-600">
                                Genera y administra las claves de API para tus aplicaciones. Define permisos y entorno (Sandbox/Producción).
                            </p>
                             <div class="mt-4">
                                <a href="#" class="text-white bg-hexafac-navy hover:opacity-90 font-bold py-2 px-4 rounded">
                                    Administrar Claves
                                </a>
                            </div>
                        </div>

                        <!-- Card: Webhooks -->
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-satellite-dish mr-2"></i>Webhooks
                            </div>
                            <p class="text-gray-600">
                                Configura los endpoints de webhook para recibir notificaciones en tiempo real sobre el estado de tus facturas.
                            </p>
                             <div class="mt-4">
                                <a href="#" class="text-white bg-hexafac-navy hover:opacity-90 font-bold py-2 px-4 rounded">
                                    Configurar
                                </a>
                            </div>
                        </div>

                        <!-- Card: Documentación -->
                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-book-open mr-2"></i>Documentación de API
                            </div>
                            <p class="text-gray-600">
                                Accede a la documentación interactiva de Swagger para explorar y probar los endpoints de la API.
                            </p>
                             <div class="mt-4">
                                <a href="/api/documentation" target="_blank" class="text-white bg-hexafac-navy hover:opacity-90 font-bold py-2 px-4 rounded">
                                    Ver Documentación
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
