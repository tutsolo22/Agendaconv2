<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="#">
            <i class="fa-solid fa-shield-halved"></i>
            {{ config('app.name', 'Laravel') }} - Admin
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fa-solid fa-tachometer-alt fa-fw"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tenants.*') ? 'active' : '' }}" href="{{ route('admin.tenants.index') }}">
                        <i class="fa-solid fa-building-user fa-fw"></i> Tenants
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.modulos.*') ? 'active' : '' }}" href="{{ route('admin.modulos.index') }}">
                        <i class="fa-solid fa-cubes fa-fw"></i> Módulos
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-id-card fa-fw"></i> Licencias
                    </a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa-solid fa-user-circle fa-fw"></i> {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('dashboard') }}">
                                <i class="fa-solid fa-toggle-off fa-fw"></i> {{ __('Cambiar a Panel de Usuario') }}
                            </a>
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