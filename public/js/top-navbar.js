/**
 * PROXICARD - Top Navbar Module
 */

let sidebarCollapsed = false;

// Toggle sidebar móvil
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
        document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    }
}

// Toggle colapsar sidebar desktop
function toggleCollapseSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    const collapseIcon = document.getElementById('collapseIcon');
    
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
        if (collapseIcon) {
            collapseIcon.classList.remove('fa-chevron-left');
            collapseIcon.classList.add('fa-chevron-right');
        }
    } else {
        sidebar.classList.remove('collapsed');
        if (mainContent) mainContent.classList.remove('expanded');
        if (collapseIcon) {
            collapseIcon.classList.remove('fa-chevron-right');
            collapseIcon.classList.add('fa-chevron-left');
        }
    }
    
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
}

// Marcar notificaciones como leídas
function markNotificationsAsRead() {
    const unreadItems = document.querySelectorAll('.notification-item.unread');
    unreadItems.forEach(item => {
        item.classList.remove('unread');
    });
    
    const badge = document.querySelector('.badge-count');
    if (badge) {
        badge.textContent = '0';
        badge.style.display = 'none';
    }
}

// Atajo de teclado Ctrl+K / Cmd+K
function setupKeyboardShortcut() {
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-bar input');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
}

// Efecto scroll en navbar
function setupScrollEffect() {
    window.addEventListener('scroll', function() {
        const navbar = document.querySelector('.top-navbar');
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Búsqueda en tiempo real (placeholder)
function setupSearch() {
    const searchInput = document.querySelector('.search-bar input');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput || !searchResults) return;
    
    let debounceTimer;
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();
        
        debounceTimer = setTimeout(() => {
            if (query.length >= 2) {
                // Aquí puedes implementar la búsqueda real
                searchResults.innerHTML = `
                    <div class="search-empty">
                        <i class="fas fa-search"></i>
                        <p>Buscando "${query}"...</p>
                    </div>
                `;
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        }, 300);
    });
    
    // Cerrar resultados al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!searchResults.contains(e.target) && !searchInput.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    // Cargar estado guardado del sidebar
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        toggleCollapseSidebar();
    }
    
    // Configurar event listeners
    const markAllReadBtn = document.querySelector('.mark-all-read');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            markNotificationsAsRead();
        });
    }
    
    // Inicializar funcionalidades
    setupKeyboardShortcut();
    setupScrollEffect();
    setupSearch();
});