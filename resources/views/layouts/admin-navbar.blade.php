<div class="top-navbar">
    <div class="navbar-left">
        <!-- Botón para móvil -->
        <button class="menu-toggle-btn d-md-none" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Botón para colapsar sidebar en desktop -->
        <button class="collapse-btn d-none d-md-flex" onclick="toggleCollapseSidebar()">
            <i class="fas fa-chevron-left" id="collapseIcon"></i>
        </button>
        
        <!-- Buscador -->
        <div class="search-bar">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Buscar...">
        </div>
        
        <div class="page-title">
            <h4>@yield('header', 'Panel de Control')</h4>
            <p>{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
    </div>
    
    <div class="user-menu">
        <!-- Notificaciones -->
        <div class="notification-badge">
            <i class="fas fa-bell"></i>
            <span class="badge-count">3</span>
        </div>
        
        <div class="user-info">
            <p class="name">{{ Auth::user()->name }}</p>
            <p class="role">{{ Auth::user()->roles->first()->name ?? 'Administrador' }}</p>
        </div>
        
        <div class="dropdown">
            <div class="user-avatar dropdown-toggle" data-bs-toggle="dropdown" style="cursor: pointer;">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
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
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('admin.logout') }}">
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
    
    // Cambiar sombra al hacer scroll
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.top-navbar');
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.05)';
        }
    });
</script>