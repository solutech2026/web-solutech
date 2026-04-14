/**
 * PROXICARD Sidebar Functions
 */

let sidebarCollapsed = false;

/**
 * Toggle sidebar visibility (para móvil)
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

/**
 * Toggle sidebar collapsed state (para escritorio)
 */
function toggleCollapseSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    sidebarCollapsed = !sidebarCollapsed;
    
    if (sidebarCollapsed) {
        sidebar.classList.add('collapsed');
        if (mainContent) mainContent.classList.add('expanded');
    } else {
        sidebar.classList.remove('collapsed');
        if (mainContent) mainContent.classList.remove('expanded');
    }
    
    localStorage.setItem('sidebarCollapsed', sidebarCollapsed);
}

/**
 * Initialize sidebar state from localStorage
 */
function initSidebarState() {
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        toggleCollapseSidebar();
    }
}

/**
 * Close sidebar on mobile when clicking a link
 */
function initMobileSidebarBehavior() {
    const navLinks = document.querySelectorAll('.sidebar .nav-link');
    const sidebar = document.getElementById('sidebar');
    
    if (window.innerWidth <= 768) {
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                }
            });
        });
    }
}

/**
 * Handle window resize
 */
function handleResize() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (window.innerWidth > 768) {
        if (sidebar && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
        }
    }
    
    // Resetear márgenes si es necesario
    if (mainContent && window.innerWidth <= 768) {
        mainContent.style.marginLeft = '0';
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    initSidebarState();
    initMobileSidebarBehavior();
    window.addEventListener('resize', handleResize);
});