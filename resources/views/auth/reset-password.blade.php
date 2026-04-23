@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reset.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="neon-reset-wrapper">
    <!-- Fondo grid -->
    <div class="neon-grid-bg-reset"></div>
    
    <!-- Orbes flotantes -->
    <div class="neon-orb-reset orb-cyan-reset"></div>
    <div class="neon-orb-reset orb-magenta-reset"></div>
    <div class="neon-orb-reset orb-purple-reset"></div>

    <!-- Tarjeta principal -->
    <div class="neon-reset-card">
        <!-- Header -->
        <div class="reset-header-neon">
            <div class="brand-icon-reset">
                <i class="fas fa-key"></i>
            </div>
            <div class="badge-reset-neon">
                <i class="fas fa-lock"></i>
                <span>Restablecer contraseña</span>
            </div>
            <h1 class="reset-title-neon">
                <span class="primary-text-neon">Nueva</span>
                <span class="accent-text-neon">Contraseña</span>
            </h1>
            <p class="reset-subtitle-neon">Ingresa tu nueva contraseña para acceder al sistema</p>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert-neon-reset alert-error-reset" id="errorAlert">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content-reset">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="close-btn-reset" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" class="reset-form-neon" id="resetForm">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div class="form-group-neon">
                <label for="email" class="form-label-neon">
                    <i class="fas fa-envelope"></i>
                    Correo Electrónico
                </label>
                <div class="input-wrapper-neon">
                    <input type="email" 
                           class="form-input-neon" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $request->email) }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           placeholder="tu@empresa.com">
                    <div class="input-border-neon"></div>
                </div>
            </div>

            <!-- Password -->
            <div class="form-group-neon">
                <label for="password" class="form-label-neon">
                    <i class="fas fa-lock"></i>
                    Nueva Contraseña
                </label>
                <div class="input-wrapper-neon">
                    <input type="password" 
                           class="form-input-neon" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                    <button class="password-toggle-reset" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="input-border-neon"></div>
                </div>
                <div class="input-hint-neon">
                    <i class="fas fa-info-circle"></i>
                    Mínimo 8 caracteres, incluye letras y números
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group-neon">
                <label for="password_confirmation" class="form-label-neon">
                    <i class="fas fa-lock"></i>
                    Confirmar Contraseña
                </label>
                <div class="input-wrapper-neon">
                    <input type="password" 
                           class="form-input-neon" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           placeholder="••••••••">
                    <button class="password-toggle-reset" type="button" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                    <div class="input-border-neon"></div>
                </div>
            </div>

            <!-- Password strength indicator -->
            <div class="password-strength-neon" id="passwordStrength" style="display: none;">
                <div class="strength-bar">
                    <div class="strength-fill" id="strengthFill"></div>
                </div>
                <span class="strength-text" id="strengthText"></span>
            </div>

            <button type="submit" class="submit-btn-reset" id="submitBtn">
                <span class="btn-text-reset">
                    <i class="fas fa-check-circle"></i>
                    Restablecer contraseña
                </span>
                <div class="btn-loader-reset" style="display: none;">
                    <div class="spinner-reset"></div>
                    <span>Procesando...</span>
                </div>
            </button>

            <div class="links-container-reset">
                <a href="{{ route('login') }}" class="link-item-reset">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
                <a href="/" class="link-item-reset">
                    <i class="fas fa-home"></i>
                    Volver al inicio
                </a>
            </div>
        </form>
    </div>
</div>

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

    // Medidor de fortaleza de contraseña
    const passwordInput = document.getElementById('password');
    const strengthDiv = document.getElementById('passwordStrength');
    const strengthFill = document.getElementById('strengthFill');
    const strengthText = document.getElementById('strengthText');

    passwordInput?.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length === 0) {
            strengthDiv.style.display = 'none';
            return;
        }
        
        strengthDiv.style.display = 'block';
        
        // Calcular fortaleza
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        // Actualizar barra y texto
        const width = (strength / 4) * 100;
        strengthFill.style.width = width + '%';
        
        if (strength <= 1) {
            strengthFill.style.background = '#ff4444';
            strengthText.textContent = 'Contraseña débil';
            strengthText.style.color = '#ff4444';
        } else if (strength <= 2) {
            strengthFill.style.background = '#ffaa00';
            strengthText.textContent = 'Contraseña media';
            strengthText.style.color = '#ffaa00';
        } else if (strength <= 3) {
            strengthFill.style.background = '#00d4ff';
            strengthText.textContent = 'Contraseña fuerte';
            strengthText.style.color = '#00d4ff';
        } else {
            strengthFill.style.background = '#00ff88';
            strengthText.textContent = 'Contraseña muy fuerte';
            strengthText.style.color = '#00ff88';
        }
    });

    // Manejo del submit del formulario con loader
    const form = document.getElementById('resetForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text-reset');
    const btnLoader = submitBtn.querySelector('.btn-loader-reset');

    if (form) {
        form.addEventListener('submit', function() {
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            submitBtn.disabled = true;
        });
    }

    // Auto-cerrar alertas después de 5 segundos
    setTimeout(() => {
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) errorAlert.remove();
    }, 5000);
</script>
@endpush
@endsection
