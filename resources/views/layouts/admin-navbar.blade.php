<link rel="stylesheet" href="{{ asset('css/top-navbar.css') }}">

<div class="top-navbar" id="topNavbar">
    <div class="navbar-left">
        <!-- Botón para móvil -->
        <button class="menu-toggle-btn d-md-none" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Botón para colapsar sidebar en desktop -->
        <button class="collapse-btn d-none d-md-flex" onclick="toggleCollapseSidebar()">
            <i class="fas fa-chevron-left" id="collapseIcon"></i>
        </button>
        
        <!-- Buscador Moderno -->
        <div class="search-wrapper">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar...">
                <div class="search-shortcut">
                    <span>⌘K</span>
                </div>
            </div>
            <div class="search-results" id="searchResults" style="display: none;"></div>
        </div>
        
        <div class="page-title">
            <h4>@yield('header', 'Panel de Control')</h4>
            <p>{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>
    
    <div class="user-menu">
        <!-- Notificaciones con Dropdown -->
        <div class="notification-dropdown">
            <div class="notification-badge" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <span class="badge-count">3</span>
            </div>
            <div class="dropdown-menu dropdown-menu-end notification-menu">
                <div class="notification-header">
                    <h6>Notificaciones</h6>
                    <a href="#" class="mark-all-read">Marcar todas</a>
                </div>
                <div class="notification-list">
                    <div class="notification-item unread">
                        <div class="notification-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-text">Nuevo usuario registrado</p>
                            <span class="notification-time">Hace 5 minutos</span>
                        </div>
                    </div>
                    <div class="notification-item unread">
                        <div class="notification-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-text">Acceso registrado - Puerta Principal</p>
                            <span class="notification-time">Hace 15 minutos</span>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="notification-content">
                            <p class="notification-text">Reporte semanal disponible</p>
                            <span class="notification-time">Hace 2 horas</span>
                        </div>
                    </div>
                </div>
                <div class="notification-footer">
                    <a href="#">Ver todas</a>
                </div>
            </div>
        </div>
        
        <div class="user-info">
            <p class="name">{{ Auth::user()->name }}</p>
            <p class="role">{{ Auth::user()->roles->first()->name ?? 'Administrador' }}</p>
        </div>
        
        <div class="dropdown">
            <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="avatar-text">{{ substr(Auth::user()->name, 0, 2) }}</span>
                <span class="avatar-status online"></span>
            </div>
            <ul class="dropdown-menu dropdown-menu-end user-menu-dropdown">
                <li class="dropdown-header">
                    <div class="user-info-mini">
                        <div class="user-avatar-mini">{{ substr(Auth::user()->name, 0, 2) }}</div>
                        <div class="user-details">
                            <strong>{{ Auth::user()->name }}</strong>
                            <span>{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.profile.index') }}">
                        <i class="fas fa-user-circle"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog"></i> Configuración
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-question-circle"></i> Ayuda
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/top-navbar.js') }}"></script>
@endpush