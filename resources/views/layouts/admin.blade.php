<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Sistema de Control de Acceso Proxicard">
    <meta name="author" content="SoluTech">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <base target="_self">
    
    <title>@yield('title', 'Proxicard - Control de Acceso')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/top-navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-layout.css') }}">
    
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        @include('layouts.admin-sidebar')
        
        <!-- Main Content -->
        <div class="main-content" id="mainContent">
            <!-- Top Navbar -->
            @include('layouts.admin-navbar')
            
            <!-- Content Area -->
            <main class="content-area">
                <div class="container-fluid px-4 py-3">
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="admin-footer">
                <div class="footer-content">
                    <p>&copy; {{ date('Y') }} SoluTech - Sistema de Control de Acceso Proxicard. Todos los derechos reservados.</p>
                    <div class="footer-links">
                        <a href="{{ route('terms') }}">Términos</a>
                        <a href="{{ route('privacy') }}">Privacidad</a>
                        <a href="{{ route('legal') }}">Legal</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Scripts personalizados -->
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/top-navbar.js') }}"></script>
    
    @stack('scripts')
    
    <script>
        // Forzar que todos los enlaces se abran en la misma ventana
        document.addEventListener('DOMContentLoaded', function() {
            // Enlaces internos
            document.querySelectorAll('a:not([href^="http"])').forEach(function(link) {
                if (link.target === '_blank') {
                    link.removeAttribute('target');
                }
                // Asegurar que los enlaces internos no abran nueva ventana
                if (link.href && link.href.startsWith(window.location.origin)) {
                    link.removeAttribute('target');
                }
            });
            
            // Prevenir clics en enlaces vacíos
            document.querySelectorAll('a[href="#"], a[href="javascript:void(0)"]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                });
            });
            
            // Agregar clase al body cuando el sidebar está colapsado
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === 'class') {
                            if (sidebar.classList.contains('collapsed')) {
                                document.body.classList.add('sidebar-collapsed');
                            } else {
                                document.body.classList.remove('sidebar-collapsed');
                            }
                        }
                    });
                });
                observer.observe(sidebar, { attributes: true });
            }
        });
        
        // Manejar errores de red
        window.addEventListener('online', function() {
            console.log('Conexión restablecida');
            // Recargar datos si es necesario
        });
        
        window.addEventListener('offline', function() {
            console.warn('Sin conexión a internet');
            // Mostrar notificación
        });
        
        // Prevenir envío duplicado de formularios
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.classList.contains('js-prevent-double-submit')) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && submitBtn.disabled) {
                    e.preventDefault();
                } else if (submitBtn) {
                    submitBtn.disabled = true;
                    setTimeout(() => {
                        submitBtn.disabled = false;
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>