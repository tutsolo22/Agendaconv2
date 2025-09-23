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
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.tenants.*', 'admin.modulos.*', 'admin.licencias.*', 'admin.configuration.*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Administración
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">{{ __('Tenants') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.modulos.*') ? 'active' : '' }}" href="{{ route('admin.modulos.index') }}">{{ __('Módulos') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.licencias.*') ? 'active' : '' }}" href="{{ route('admin.licencias.index') }}">{{ __('Licencias') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.configuration.*') ? 'active' : '' }}" href="{{ route('admin.configuration.index') }}">{{ __('Configuración de Tenants') }}</a></li>
                                </ul>
                            </li>

                            {{-- Dropdown para HexaFac --}}
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.hexafac.*') ? 'active' : '' }}" href="#" id="hexafacDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa-solid fa-fire-flame-curved me-2"></i>HexaFac
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="hexafacDropdown">
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.hexafac.dashboard') ? 'active' : '' }}" href="{{ route('admin.hexafac.dashboard') }}">{{ __('Panel de Control') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('admin.hexafac.applications.*') ? 'active' : '' }}" href="{{ route('admin.hexafac.applications.index') }}">{{ __('Aplicaciones') }}</a></li>
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
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.clientes.*') ? 'active' : '' }}" href="{{ route('tenant.clientes.index') }}">{{ __('Clientes') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.sucursales.*') ? 'active' : '' }}" href="{{ route('tenant.sucursales.index') }}">{{ __('Sucursales') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.licencias.*') ? 'active' : '' }}" href="{{ route('tenant.licencias.index') }}">{{ __('Mis Licencias') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('documents.upload.index') ? 'active' : '' }}" href="{{ route('tenant.documents.upload.index') }}">{{ __('Gestor de Documentos') }}</a></li>
                                    <li><a class="dropdown-item {{ request()->routeIs('tenant.configuration.*') ? 'active' : '' }}" href="{{ route('tenant.configuration.index') }}">{{ __('Configuración') }}</a></li>
                                </ul>
                            </li>

                            {{-- Menú desplegable para Módulos Licenciados --}}
                            
                            @if($licensedModules->count() > 0)
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Módulos
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        @foreach ($licensedModules as $modulo)
                                            @if (!empty($modulo->submenu) && (is_array($modulo->submenu) || is_object($modulo->submenu)))
                                                <li class="dropdown-submenu">
                                                    <a class="dropdown-item dropdown-toggle" href="#">
                                                        <i class="fa-solid {{ $modulo->icono }} fa-fw me-2"></i>{{ $modulo->nombre }}
                                                    </a>
                                                    <ul class="dropdown-menu">
                                                        @foreach ($modulo->submenu as $subitem_group)
                                                            {{-- Check if $subitem_group has a nested 'submenu' --}}
                                                            @if (isset($subitem_group['submenu']) && (is_array($subitem_group['submenu']) || is_object($subitem_group['submenu'])))
                                                                <li class="dropdown-submenu">
                                                                    <a class="dropdown-item dropdown-toggle" href="#">{{ $subitem_group['nombre'] }}</a>
                                                                    <ul class="dropdown-menu">
                                                                        @foreach ($subitem_group['submenu'] as $subitem_link)
                                                                            @if (isset($subitem_link['route']) && Route::has($subitem_link['route']))
                                                                                <li>
                                                                                    <a class="dropdown-item" href="{{ route($subitem_link['route']) }}">{{ $subitem_link['nombre'] }}</a>
                                                                                </li>
                                                                            @endif
                                                                        @endforeach
                                                                    </ul>
                                                                </li>
                                                            @else
                                                                {{-- If no nested submenu, treat $subitem_group as a direct link --}}
                                                                @if (isset($subitem_group['route_name']) && Route::has($subitem_group['route_name']))
                                                                    <li>
                                                                        <a class="dropdown-item" href="{{ route($subitem_group['route_name']) }}">{{ $subitem_group['nombre'] }}</a>
                                                                    </li>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @elseif($modulo->route_name && Route::has($modulo->route_name))
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

    <style>
        /* Estilos para submenús en Bootstrap 5 */
        .dropdown-submenu {
            position: relative;
        }
        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            display: none;
        }
        .dropdown-submenu:hover > .dropdown-menu {
            display: block;
        }
        .dropdown-submenu > a:after {
            display: block;
            content: " ";
            float: right;
            width: 0;
            height: 0;
            border-color: transparent;
            border-style: solid;
            border-width: 5px 0 5px 5px;
            border-left-color: #ccc;
            margin-top: 5px;
            margin-right: -10px;
        }
    </style>
    {{-- Inicializa objetos globales para evitar errores si no se definen en una vista específica --}}
    <script>
        window.apiUrls = window.apiUrls || {};
        window.currentData = window.currentData || {};
    </script>
    @stack('scripts')
</body>
</html>