<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="text-center">
            <img src="/img/logo_solutech1.png" alt="SoluTech" class="logo">
            <h3>SoluTech</h3>
            <p>Sistema de Control de Acceso</p>
        </div>
    </div>
    
    <ul class="sidebar-nav">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-title="Dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.access-control.index') }}" class="nav-link {{ request()->routeIs('admin.access-control*') ? 'active' : '' }}" data-title="Control de Acceso">
                <i class="fas fa-door-open"></i>
                <span>Control de Acceso</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.nfc-cards.index') }}" class="nav-link {{ request()->routeIs('admin.nfc-cards*') ? 'active' : '' }}" data-title="Tarjetas NFC">
                <i class="fas fa-id-card"></i>
                <span>Tarjetas NFC</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.persons.index') }}" class="nav-link {{ request()->routeIs('admin.persons*') ? 'active' : '' }}" data-title="Personas">
                <i class="fas fa-address-book"></i>
                <span>Personas</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" data-title="Usuarios">
                <i class="fas fa-users-cog"></i>
                <span>Usuarios</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" data-title="Reportes">
                <i class="fas fa-chart-bar"></i>
                <span>Reportes</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}" data-title="Configuración">
                <i class="fas fa-cog"></i>
                <span>Configuración</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a href="{{ route('admin.profile.index') }}" class="nav-link {{ request()->routeIs('admin.profile*') ? 'active' : '' }}" data-title="Mi Perfil">
                <i class="fas fa-user-circle"></i>
                <span>Mi Perfil</span>
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <i class="fas fa-shield-alt"></i>
        <span>Versión 1.0</span>
    </div>
</div>

<script>
    let sidebarCollapsed = false;
    
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
    }
    
    function toggleCollapseSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        const collapseIcon = document.getElementById('collapseIcon');
        
        sidebarCollapsed = !sidebarCollapsed;
        
        if (sidebarCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
            if (collapseIcon) {
                collapseIcon.classList.remove('fa-chevron-left');
                collapseIcon.classList.add('fa-chevron-right');
            }
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            if (collapseIcon) {
                collapseIcon.classList.remove('fa-chevron-right');
                collapseIcon.classList.add('fa-chevron-left');
            }
        }
        
        localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
    }
    
    // Cargar estado guardado
    document.addEventListener('DOMContentLoaded', function() {
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            toggleCollapseSidebar();
        }
    });
</script>