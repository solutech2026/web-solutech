@extends('layouts.guest')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/solubase-login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
    <div class="solubase-login-wrapper">
        <div class="solubase-background">
            <div class="gradient-orb-1"></div>
            <div class="gradient-orb-2"></div>
            <div class="gradient-orb-3"></div>
            <div class="grid-pattern"></div>
        </div>

        <div class="solubase-container">
            <div class="solubase-grid">
                <!-- Left Side - Branding -->
                <div class="solubase-brand">
                    <div class="brand-content">
                        <div class="logo-container">
                            <div class="logo-icon">
                                <img src="{{ asset('img/logo_app.png') }}" alt="Logo de la aplicación" width="48"
                                    height="48">
                            </div>
                        </div>

                        <h1 class="brand-title">
                            <span class="solubase-blue">PROXI</span>
                            <span class="solubase-purple">CARD</span>
                        </h1>
                        <p class="brand-tagline">Sistema de Control de Acceso</p>

                        <div class="brand-stats">
                            <div class="stat-item">
                                <div class="stat-value">500+</div>
                                <div class="stat-label">Empresas</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">50K+</div>
                                <div class="stat-label">Usuarios</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value">99.9%</div>
                                <div class="stat-label">Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="solubase-form-container">
                    <div class="form-card-modern">
                        <div class="form-header-modern">
                            <div class="welcome-badge">
                                <i class="fas fa-hand-peace"></i>
                                <span>Bienvenido de vuelta</span>
                            </div>
                            <h2>Iniciar Sesión</h2>
                            <p>Ingresa tus credenciales para acceder a tu cuenta</p>
                        </div>

                        @if (session('error'))
                            <div class="alert-modern alert-error-modern">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert-modern alert-error-modern">
                                @foreach ($errors->all() as $error)
                                    <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="login-form-modern">
                            @csrf

                            <div class="input-modern">
                                <label class="input-label">
                                    <i class="fas fa-envelope"></i>
                                    Correo electrónico
                                </label>
                                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                                    placeholder="usuario@empresa.com" class="input-field-modern">
                                <div class="input-focus-border"></div>
                            </div>

                            <div class="input-modern">
                                <label class="input-label">
                                    <i class="fas fa-lock"></i>
                                    Contraseña
                                </label>
                                <div class="password-wrapper">
                                    <input type="password" name="password" id="password" required placeholder="••••••••"
                                        class="input-field-modern">
                                    <button type="button" class="password-toggle-modern" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="input-focus-border"></div>
                            </div>

                            <div class="form-options-modern">
                                <label class="checkbox-modern">
                                    <input type="checkbox" name="remember">
                                    <span class="checkmark-modern"></span>
                                    <span>Recordarme</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="forgot-link-modern">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn-login-modern">
                                <span>Acceder</span>
                                <i class="fas fa-arrow-right"></i>
                                <div class="btn-shine"></div>
                            </button>

                            @if (Route::has('register'))
                                <div class="register-link-modern">
                                    ¿No tienes cuenta?
                                    <a href="{{ route('register') }}">Crear cuenta gratuita</a>
                                </div>
                            @endif

                            <div class="back-home-modern">
                                <a href="/">
                                    <i class="fas fa-arrow-left"></i> Volver al inicio
                                </a>
                            </div>
                        </form>

                        <div class="social-login">
                            <div class="divider">
                                <span>O continúa con</span>
                            </div>
                            <div class="social-buttons">
                                <button class="social-btn">
                                    <i class="fab fa-google"></i>
                                </button>
                                <button class="social-btn">
                                    <i class="fab fa-github"></i>
                                </button>
                                <button class="social-btn">
                                    <i class="fab fa-microsoft"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.getElementById('togglePassword')?.addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
@endpush
