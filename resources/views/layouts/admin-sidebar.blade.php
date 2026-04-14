<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo-container">
            <div class="logo-icon">
                <img src="{{ asset('img/logo_app.png') }}" alt="PROXICARD" class="logo-image">
            </div>
            <div class="logo-text">
                <h3>
                    <span class="proxicard-gradient">PROXI</span>
                    <span class="proxicard-light">CARD</span>
                </h3>
                <p>Sistema de Control de Acceso</p>
            </div>
        </div>
    </div>
    
    <ul class="sidebar-nav">
        @php
            $user = Auth::user();
            $isSuperAdmin = $user->hasRole('super-admin');
            $isAdmin = $user->hasRole('admin');
        @endphp
        
        <!-- Dashboard -->
        @if($isSuperAdmin || $isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endif
        
        <!-- Control de Acceso -->
        @if($isSuperAdmin || $isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.access-control.index') }}" class="nav-link {{ request()->routeIs('admin.access-control*') ? 'active' : '' }}" data-title="Control de Acceso">
                <i class="fas fa-door-open"></i>
                <span>Control de Acceso</span>
            </a>
        </li>
        @endif
        
        <!-- Tarjetas NFC - Solo super-admin -->
        @if($isSuperAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.nfc-cards.index') }}" class="nav-link {{ request()->routeIs('admin.nfc-cards*') ? 'active' : '' }}" data-title="Tarjetas NFC">
                <i class="fas fa-id-card"></i>
                <span>Tarjetas NFC</span>
            </a>
        </li>
        @endif
        
        <!-- Personas -->
        @if($isSuperAdmin || $isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.persons.index') }}" class="nav-link {{ request()->routeIs('admin.persons*') ? 'active' : '' }}" data-title="Personas">
                <i class="fas fa-address-book"></i>
                <span>Personas</span>
            </a>
        </li>
        @endif
        
        <!-- Empresas y Colegios - Solo super-admin -->
        @if($isSuperAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.companies.index') }}" class="nav-link {{ request()->routeIs('admin.companies*') ? 'active' : '' }}" data-title="Empresas y Colegios">
                <i class="fas fa-building"></i>
                <span>Empresas y Colegios</span>
            </a>
        </li>
        @endif
        
        <!-- Usuarios - Solo super-admin -->
        @if($isSuperAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-title="Usuarios">
                <i class="fas fa-users-cog"></i>
                <span>Usuarios</span>
            </a>
        </li>
        @endif
        
        <!-- Reportes -->
        @if($isSuperAdmin || $isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" data-title="Reportes">
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
            </a>
        </li>
        @endif
        
        <!-- Configuración - Solo super-admin -->
        @if($isSuperAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" data-title="Configuración">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </li>
        @endif
        
        <!-- Mi Perfil - Todos los roles -->
        <li class="nav-item">
            <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}" data-title="Mi Perfil">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="nfc-badge">
            <i class="fas fa-microchip"></i>
            <span>NFC Ready</span>
        </div>
        <div class="version">
            <i class="fas fa-shield-alt"></i>
            <span>v2.0</span>
        </div>
    </div>
</div>

<button class="sidebar-toggle-mobile" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>