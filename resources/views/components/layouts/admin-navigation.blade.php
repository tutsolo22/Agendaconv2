<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('tenant.dashboard') }}">
            <i class="fa-solid fa-building"></i>
            {{-- Muestra el nombre del Tenant si está disponible --}}
            {{ Auth::user()->tenant->name ?? config('app.name', 'Laravel') }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#tenantNavbarSupportedContent" aria-controls="tenantNavbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="tenantNavbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}" href="{{ route('tenant.dashboard') }}">
                        <i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.users.*') ? 'active' : '' }}" href="{{ route('tenant.users.index') }}">
                        <i class="fa-solid fa-users fa-fw"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tenant.sucursales.*') ? 'active' : '' }}" href="{{ route('tenant.sucursales.index') }}">
                        <i class="fa-solid fa-shop fa-fw"></i> Sucursales
                    </a>
                </li>
                {{-- Los módulos con licencia se inyectan a través de LicensedModulesComposer --}}
                @if (isset($licensedModules) && $licensedModules->count() > 0)
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownModules" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-cubes fa-fw"></i> Módulos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownModules">
                            @foreach ($licensedModules as $module)
                                <li><a class="dropdown-item" href="{{ route('tenant.module.show', ['moduleSlug' => $module->nombre]) }}">
                                    <i class="fa-solid {{ $module->icono ?? 'fa-cube' }} fa-fw me-2"></i>{{ $module->nombre }}
                                </a></li>
                            @endforeach
                        </ul>
                    </li>
                @endif
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-user-circle fa-fw"></i> {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user-pen fa-fw"></i> {{ __('Profile') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa-solid fa-right-from-bracket fa-fw"></i> {{ __('Log Out') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>