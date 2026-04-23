@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="neon-register-wrapper">
    <!-- Fondo grid -->
    <div class="neon-grid-bg-register"></div>
    
    <!-- Orbes flotantes -->
    <div class="neon-orb-register orb-cyan-register"></div>
    <div class="neon-orb-register orb-magenta-register"></div>
    <div class="neon-orb-register orb-purple-register"></div>

    <!-- Tarjeta de registro -->
    <div class="neon-register-card">
        <!-- Header -->
        <div class="register-header-neon">
            <div class="logo-icon-neon">
                <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 4L4 14V34L24 44L44 34V14L24 4Z" stroke="url(#gradientRegisterNeon)" stroke-width="2" fill="none"/>
                    <path d="M24 24L14 18V30L24 36L34 30V18L24 24Z" stroke="url(#gradientRegisterNeon)" stroke-width="2" fill="none"/>
                    <defs>
                        <linearGradient id="gradientRegisterNeon" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#00d4ff"/>
                            <stop offset="100%" stop-color="#a855f7"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
            
            <div class="badge-register-neon">
                <i class="fas fa-user-plus"></i>
                <span>Registro de usuarios</span>
            </div>
            
            <h1>
                <span>Solu</span>
                <span>Base</span>
            </h1>
            <p class="register-subtitle-neon">Crear una nueva cuenta</p>
        </div>

        @if($errors->any())
            <div class="alert-neon-register">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="register-form-neon">
            @csrf

            <!-- Name -->
            <div class="input-group-neon">
                <label class="input-label-neon">
                    <i class="fas fa-user"></i>
                    Nombre completo
                </label>
                <div class="input-wrapper-neon">
                    <input type="text" 
                           class="input-field-neon" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           placeholder="Ingrese su nombre completo">
                </div>
            </div>

            <!-- Email Address -->
            <div class="input-group-neon">
                <label class="input-label-neon">
                    <i class="fas fa-envelope"></i>
                    Correo electrónico
                </label>
                <div class="input-wrapper-neon">
                    <input type="email" 
                           class="input-field-neon" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username"
                           placeholder="correo@ejemplo.com">
                </div>
            </div>

            <!-- Password -->
            <div class="input-group-neon">
                <label class="input-label-neon">
                    <i class="fas fa-lock"></i>
                    Contraseña
                </label>
                <div class="input-wrapper-neon">
                    <input type="password" 
                           class="input-field-neon" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Ingrese su contraseña">
                    <button class="password-toggle-neon" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="input-hint-neon">
                    <i class="fas fa-info-circle"></i>
                    Mínimo 8 caracteres
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="input-group-neon">
                <label class="input-label-neon">
                    <i class="fas fa-lock"></i>
                    Confirmar contraseña
                </label>
                <div class="input-wrapper-neon">
                    <input type="password" 
                           class="input-field-neon" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirme su contraseña">
                    <button class="password-toggle-neon" type="button" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="register-actions-neon">
                <button type="submit" class="btn-register-neon">
                    <span>Crear cuenta</span>
                    <i class="fas fa-arrow-right"></i>
                    <div class="btn-shine-neon"></div>
                </button>

                <div class="register-links-neon">
                    <a href="{{ route('login') }}" class="login-link-neon">
                        <i class="fas fa-arrow-left"></i>
                        ¿Ya tienes cuenta? Inicia sesión
                    </a>
                    <a href="/" class="home-link-neon">
                        <i class="fas fa-home"></i>
                        Volver al inicio
                    </a>
                </div>
            </div>
        </form>

        <!-- Términos y condiciones -->
        <div class="register-terms-neon">
            <p>
                Al registrarte, aceptas nuestros 
                <a href="#">Términos de servicio</a> y 
                <a href="#">Política de privacidad</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle para contraseña
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
    
    // Toggle para confirmar contraseña
    document.getElementById('toggleConfirmPassword')?.addEventListener('click', function() {
        const password = document.getElementById('password_confirmation');
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
