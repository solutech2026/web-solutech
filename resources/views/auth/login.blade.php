@extends('layouts.guest')

@section('content')
<div class="login-container">
    <div class="login-card">
        <div class="text-center mb-4">
            <img src="/img/logo_solutech1.png" alt="SoluTech" class="login-logo" 
                 onerror="this.src='https://via.placeholder.com/80'">
            <h3 class="login-title mb-2">
                <span class="primary">Solu</span>
                <span class="accent">Tech</span>
            </h3>
            <p class="login-subtitle">Sistema de Control de Acceso</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
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

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required
                           placeholder="Ingrese su contraseña">
                    <button class="btn btn-outline-secondary password-toggle" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Recordarme</label>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4 login-links">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-decoration-none small">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn btn-login w-100">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>

            <div class="text-center mt-3 login-links">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="text-decoration-none">
                        ¿No tienes cuenta? <strong>Regístrate aquí</strong>
                    </a>
                @endif
            </div>

            <div class="text-center mt-2 login-links">
                <a href="/" class="text-decoration-none small">
                    <i class="fas fa-arrow-left"></i> Volver al inicio
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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