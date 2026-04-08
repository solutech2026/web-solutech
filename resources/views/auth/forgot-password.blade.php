@extends('layouts.guest')

@section('content')
<div class="forgot-container">
    <div class="forgot-card">
        <div class="text-center mb-4">
            <img src="/img/logo_solutech1.png" alt="SoluTech" class="forgot-logo" 
                 onerror="this.src='https://via.placeholder.com/80'">
            <h3 class="forgot-title mb-2">
                <span class="primary">Solu</span>
                <span class="accent">Tech</span>
            </h3>
            <p class="forgot-subtitle">Restablecer contraseña</p>
        </div>

        <div class="text-muted text-center mb-4" style="font-size: 14px;">
            ¿Olvidaste tu contraseña? No hay problema. Solo dinos tu correo electrónico 
            y te enviaremos un enlace para restablecer tu contraseña.
        </div>

        <!-- Session Status -->
        @if(session('status'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="form-label">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus
                           placeholder="correo@ejemplo.com">
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 forgot-links">
                <a href="{{ route('login') }}" class="text-decoration-none small">
                    <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
                </a>
            </div>

            <button type="submit" class="btn btn-forgot">
                <i class="fas fa-paper-plane"></i> Enviar enlace de restablecimiento
            </button>

            <div class="text-center mt-3 forgot-links">
                <a href="/" class="text-decoration-none small">
                    <i class="fas fa-home"></i> Volver al inicio
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/forgot-password.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
