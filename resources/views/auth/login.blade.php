@extends('layouts.guest')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="neon-login-wrapper">
        <!-- Fondo grid -->
        <div class="neon-grid-bg"></div>

        <!-- Orbes flotantes -->
        <div class="neon-orb orb-cyan"></div>
        <div class="neon-orb orb-magenta"></div>
        <div class="neon-orb orb-blue"></div>

        <!-- Tarjeta de login -->
        <div class="neon-login-card">
            <div class="login-header">
                <div class="badge-restricted">
                    <i class="fas fa-shield-alt"></i>
                    <span>Acceso Seguro</span>
                </div>
                <h2>Bienvenido</h2>
                <p>Ingresa tus credenciales de acceso</p>
            </div>

            @if (session('error'))
                <div class="alert-neon">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert-neon">
                    <i class="fas fa-circle-exclamation"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- 🔥 AGREGAR autocomplete="on" al formulario -->
            <form method="POST" action="{{ route('login') }}" autocomplete="on">
                @csrf

                <div class="input-neon-group">
                    <label class="input-neon-label">
                        <i class="fas fa-envelope"></i>
                        Correo electrónico
                    </label>
                    <div class="input-neon-wrapper">
                        <!-- 🔥 AGREGAR autocomplete="username" -->
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               autocomplete="username"
                               placeholder="tu@empresa.com" 
                               class="input-neon-field">
                    </div>
                </div>

                <div class="input-neon-group">
                    <label class="input-neon-label">
                        <i class="fas fa-lock"></i>
                        Contraseña
                    </label>
                    <div class="input-neon-wrapper">
                        <!-- 🔥 AGREGAR autocomplete="current-password" -->
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••"
                               class="input-neon-field">
                        <button type="button" class="password-toggle-neon" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-options-neon">
                    <label class="checkbox-neon">
                        <input type="checkbox" name="remember" id="rememberCheckbox">
                        <span class="checkbox-custom"></span>
                        <span>Recordarme</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <button type="submit" class="btn-login-neon">
                    <span>Ingresar al sistema</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="back-home-neon">
                    <a href="/">
                        <i class="fas fa-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle para mostrar/ocultar contraseña
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // ========== NUEVO: Guardar credenciales en localStorage ==========
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.getElementById('password');
        const rememberCheckbox = document.getElementById('rememberCheckbox');
        const loginForm = document.querySelector('form');

        // Cargar credenciales guardadas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const savedEmail = localStorage.getItem('proxicard_saved_email');
            const savedPassword = localStorage.getItem('proxicard_saved_password');
            const savedRemember = localStorage.getItem('proxicard_saved_remember');
            
            if (savedEmail && savedRemember === 'true') {
                emailInput.value = savedEmail;
                if (rememberCheckbox) rememberCheckbox.checked = true;
            }
            
            if (savedPassword && savedRemember === 'true') {
                passwordInput.value = savedPassword;
            }
        });

        // Guardar credenciales cuando se envía el formulario con "Recordarme" marcado
        loginForm?.addEventListener('submit', function() {
            const remember = rememberCheckbox?.checked;
            const email = emailInput?.value;
            const password = passwordInput?.value;
            
            if (remember && email && password) {
                localStorage.setItem('proxicard_saved_email', email);
                localStorage.setItem('proxicard_saved_password', password);
                localStorage.setItem('proxicard_saved_remember', 'true');
            } else if (!remember) {
                // Si no marcó recordarme, limpiar credenciales guardadas
                localStorage.removeItem('proxicard_saved_email');
                localStorage.removeItem('proxicard_saved_password');
                localStorage.removeItem('proxicard_saved_remember');
            }
        });

        // Si el checkbox cambia a "no recordar", limpiar credenciales
        rememberCheckbox?.addEventListener('change', function(e) {
            if (!e.target.checked) {
                localStorage.removeItem('proxicard_saved_email');
                localStorage.removeItem('proxicard_saved_password');
                localStorage.removeItem('proxicard_saved_remember');
            }
        });
    </script>
@endpush
