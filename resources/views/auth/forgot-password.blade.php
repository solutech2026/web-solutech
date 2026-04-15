{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.guest')

@section('content')
<div class="forgot-wrapper">
    <div class="forgot-card">
        <!-- Decorative elements -->
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>
        
        <div class="card-inner">
            <div class="brand-section">
                <div class="logo-wrapper">
                    <img src="/img/logo_solutech1.png" alt="SoluTech" class="brand-logo" 
                         onerror="this.src='https://via.placeholder.com/80'">
                    <div class="brand-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>
                <h1 class="brand-name">
                    <span class="primary-text">Solu</span>
                    <span class="accent-text">Tech</span>
                </h1>
                <p class="brand-tagline">Restablecer contraseña</p>
            </div>

            <div class="info-message">
                <i class="fas fa-info-circle"></i>
                <p>¿Olvidaste tu contraseña? No hay problema. Solo dinos tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('status') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div class="alert-content">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="forgot-form" id="forgotForm">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope"></i>
                        Correo Electrónico
                    </label>
                    <div class="input-wrapper">
                        <input type="email" 
                               class="form-input" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               placeholder="correo@ejemplo.com">
                        <div class="input-border"></div>
                    </div>
                </div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    <span class="btn-text">
                        <i class="fas fa-paper-plane"></i>
                        Enviar enlace de restablecimiento
                    </span>
                    <div class="btn-loader" style="display: none;">
                        <div class="spinner"></div>
                        <span>Enviando...</span>
                    </div>
                </button>

                <div class="links-container">
                    <a href="{{ route('login') }}" class="link-item">
                        <i class="fas fa-arrow-left"></i>
                        Volver al inicio de sesión
                    </a>
                    <a href="/" class="link-item">
                        <i class="fas fa-home"></i>
                        Volver al inicio
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
