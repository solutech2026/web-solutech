@extends('layouts.guest')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="neon-forgot-wrapper">
    <!-- Fondo grid -->
    <div class="neon-grid-bg-forgot"></div>
    
    <!-- Orbes flotantes -->
    <div class="neon-orb-forgot orb-cyan-forgot"></div>
    <div class="neon-orb-forgot orb-magenta-forgot"></div>
    <div class="neon-orb-forgot orb-blue-forgot"></div>

    <!-- Tarjeta principal -->
    <div class="neon-forgot-card">
        <!-- Brand Section -->
        <div class="brand-section-neon">
            <div class="logo-wrapper-neon">
                <div class="brand-icon-neon">
                    <i class="fas fa-lock"></i>
                </div>
            </div>
            <h1 class="brand-name-neon">
                <span class="primary-text-neon">PROXI</span>
                <span class="accent-text-neon">CARD</span>
            </h1>
            <p class="brand-tagline-neon">Restablecer contraseña</p>
        </div>

        <!-- Info Message -->
        <div class="info-message-neon">
            <i class="fas fa-info-circle"></i>
            <p>¿Olvidaste tu contraseña? No hay problema. Solo dinos tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>

        <!-- Session Status (success) -->
        @if(session('status'))
            <div class="alert-neon-forgot alert-success-neon" id="successAlert">
                <i class="fas fa-check-circle"></i>
                <div class="alert-content-neon">{{ session('status') }}</div>
                <button type="button" class="close-btn-neon" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="alert-neon-forgot alert-error-neon" id="errorAlert">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="alert-content-neon">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
                <button type="button" class="close-btn-neon" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="forgot-form-neon" id="forgotForm">
            @csrf

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
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           placeholder="correo@ejemplo.com">
                    <div class="input-border-neon"></div>
                </div>
            </div>

            <button type="submit" class="submit-btn-neon" id="submitBtn">
                <span class="btn-text-neon">
                    <i class="fas fa-paper-plane"></i>
                    Enviar enlace de restablecimiento
                </span>
                <div class="btn-loader-neon" style="display: none;">
                    <div class="spinner-neon"></div>
                    <span>Enviando...</span>
                </div>
            </button>

            <div class="links-container-neon">
                <a href="{{ route('login') }}" class="link-item-neon">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
                <a href="/" class="link-item-neon">
                    <i class="fas fa-home"></i>
                    Volver al inicio
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Manejo del submit del formulario con loader
    const form = document.getElementById('forgotForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text-neon');
    const btnLoader = submitBtn.querySelector('.btn-loader-neon');

    if (form) {
        form.addEventListener('submit', function() {
            // Mostrar loader
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            submitBtn.disabled = true;
        });
    }

    // Auto-cerrar alertas después de 5 segundos
    setTimeout(() => {
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');
        if (successAlert) successAlert.remove();
        if (errorAlert) errorAlert.remove();
    }, 5000);
</script>
@endpush
@endsection
