<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Scripts & Styles via Vite -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
            <div class="container">
                {{-- El logo/nombre de la app redirige al dashboard correspondiente --}}
                <a class="navbar-brand" href="{{ $isSuperAdmin ? route('admin.dashboard') : route('tenant.dashboard') }}">
                    <i class="fa-solid fa-calendar-check me-2"></i>{{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Lado Izquierdo de la Barra de Navegación -->
                    <ul class="navbar-nav me-auto">
                        @if($isSuperAdmin)
                            {{-- =============================== --}}
                            {{-- MENÚ PARA EL SUPER-ADMINISTRADOR --}}
                            {{-- =============================== --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.tenants.*', 'admin.modulos.*', 'admin.licencias.*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Administración
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">{{ __('Tenants') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.modulos.*') ? 'active' : '' }}" href="{{ route('admin.modulos.index') }}">{{ __('Módulos') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.licencias.*') ? 'active' : '' }}" href="{{ route('admin.licencias.index') }}">{{ __('Licencias') }}</a></li>
                                </ul>
                            </li>

                        @elseif($isTenantAdmin)
                            {{-- ============================ --}}
                            {{-- MENÚ PARA EL TENANT-ADMIN --}}
                            {{-- ============================ --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('tenant.users.*', 'tenant.sucursales.*', 'tenant.licencias.*', 'tenant.configuration.*') ? 'active' : '' }}" href="#" id="tenantAdminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Administración
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="tenantAdminDropdown">
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.users.*') ? 'active' : '' }}" href="{{ route('tenant.users.index') }}">{{ __('Usuarios') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.sucursales.*') ? 'active' : '' }}" href="{{ route('tenant.sucursales.index') }}">{{ __('Sucursales') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.licencias.*') ? 'active' : '' }}" href="{{ route('tenant.licencias.index') }}">{{ __('Mis Licencias') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.configuration.*') ? 'active' : '' }}" href="{{ route('tenant.configuration.index') }}">{{ __('Configuración') }}</a></li>
                                </ul>
                            </li>

                            {{-- Menú desplegable para Módulos Licenciados --}}
                            @if($licensedModules->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Módulos
                                    </a>
                                    <ul class="dropdown-menu">
                                        @foreach ($licensedModules as $modulo)
                                            @if($modulo->route_name && Route::has($modulo->route_name))
                                                <li>
                                                    <a class="dropdown-item {{ request()->routeIs(Str::before($modulo->route_name, '.').'.*') ? 'active' : '' }}" href="{{ route($modulo->route_name) }}">
                                                        <i class="fa-solid {{ $modulo->icono }} fa-fw me-2"></i>{{ $modulo->nombre }}
                                                    </a>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                            @endif
                        @endif
                    </ul>

                    <!-- Lado Derecho de la Barra de Navegación -->
                    <ul class="navbar-nav ms-auto">
                        @if($user)
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ $user->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fa-solid fa-user-pen fa-fw me-2"></i>{{ __('Profile') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fa-solid fa-right-from-bracket fa-fw me-2"></i>{{ __('Log Out') }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if (isset($header))
                    <header class="mb-4">
                        <div class="p-4 bg-white border shadow-sm rounded-lg">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{ $slot }}
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>