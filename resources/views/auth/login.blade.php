@extends('layouts.guest')

@section('content')
<div class="login-wrapper">
    <div class="login-background">
        <div class="bg-gradient"></div>
        <div class="bg-blob-1"></div>
        <div class="bg-blob-2"></div>
        <div class="bg-blob-3"></div>
    </div>

    <div class="login-container">
        <div class="login-grid">
            <!-- Left Side - Branding -->
            <div class="login-brand">
                <div class="brand-content">
                    <div class="brand-logo">
                        <img src="/img/logo_solutech1.png" alt="SoluTech">
                    </div>
                    <h1 class="brand-title">
                        <span class="primary">Solu</span>
                        <span class="accent">Tech</span>
                    </h1>
                    <p class="brand-subtitle">Sistema de Control de Acceso</p>
                    <div class="brand-features">
                        <div class="feature">
                            <i class="fas fa-shield-alt"></i>
                            <span>Seguridad Avanzada</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-id-card"></i>
                            <span>Control de Acceso NFC</span>
                        </div>
                        <div class="feature">
                            <i class="fas fa-chart-line"></i>
                            <span>Reportes en Tiempo Real</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side - Login Form -->
            <div class="login-form-container">
                <div class="form-card">
                    <div class="form-header">
                        <h2>Bienvenido</h2>
                        <p>Ingresa tus credenciales para continuar</p>
                    </div>

                    @if(session('error'))
                        <div class="alert-custom alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert-custom alert-error">
                            @foreach($errors->all() as $error)
                                <div><i class="fas fa-times-circle"></i> {{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-form">
                        @csrf
                        
                        <div class="input-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus
                                   placeholder="Correo electrónico"
                                   class="input-field">
                        </div>

                        <div class="input-group-custom">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   required
                                   placeholder="Contraseña"
                                   class="input-field">
                            <button type="button" class="password-toggle" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-custom">
                                <input type="checkbox" name="remember">
                                <span class="checkmark"></span>
                                Recordarme
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="forgot-link">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn-login-custom">
                            <span>Iniciar Sesión</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>

                        @if (Route::has('register'))
                            <div class="register-link">
                                ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                            </div>
                        @endif

                        <div class="back-home">
                            <a href="/">
                                <i class="fas fa-arrow-left"></i> Volver al inicio
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .login-wrapper {
        min-height: 100vh;
        width: 100%;
        position: relative;
        font-family: 'Inter', sans-serif;
        overflow: hidden;
    }

    /* Background Effects */
    .login-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
    }

    .bg-gradient {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
    }

    .bg-blob-1 {
        position: absolute;
        top: -20%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.4) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        animation: float1 20s ease-in-out infinite;
    }

    .bg-blob-2 {
        position: absolute;
        bottom: -20%;
        left: -10%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(60px);
        animation: float2 25s ease-in-out infinite reverse;
    }

    .bg-blob-3 {
        position: absolute;
        top: 40%;
        left: 20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.2) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(50px);
        animation: float3 18s ease-in-out infinite;
    }

    @keyframes float1 {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(30px, -30px); }
    }

    @keyframes float2 {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-30px, 20px); }
    }

    @keyframes float3 {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(20px, 30px); }
    }

    /* Container */
    .login-container {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .login-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        max-width: 1200px;
        width: 100%;
        background: rgba(18, 22, 35, 0.7);
        backdrop-filter: blur(20px);
        border-radius: 32px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    /* Left Side - Branding */
    .login-brand {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(139, 92, 246, 0.05));
        padding: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .brand-content {
        text-align: center;
        max-width: 320px;
    }

    .brand-logo {
        margin-bottom: 24px;
    }

    .brand-logo img {
        width: 80px;
        height: 80px;
        border-radius: 20px;
    }

    .brand-title {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .brand-title .primary {
        color: #818cf8;
    }

    .brand-title .accent {
        color: #a78bfa;
    }

    .brand-subtitle {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 32px;
    }

    .brand-features {
        text-align: left;
        margin-top: 32px;
    }

    .feature {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        color: #cbd5e1;
        font-size: 13px;
    }

    .feature i {
        width: 24px;
        color: #818cf8;
        font-size: 16px;
    }

    /* Right Side - Form */
    .login-form-container {
        padding: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .form-card {
        width: 100%;
        max-width: 380px;
    }

    .form-header {
        margin-bottom: 32px;
        text-align: center;
    }

    .form-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: white;
        margin-bottom: 8px;
    }

    .form-header p {
        color: #94a3b8;
        font-size: 14px;
    }

    /* Input Styles */
    .input-group-custom {
        position: relative;
        margin-bottom: 20px;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        z-index: 2;
    }

    .input-field {
        width: 100%;
        padding: 14px 16px 14px 48px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        font-size: 14px;
        color: white;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        outline: none;
        border-color: #818cf8;
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }

    .input-field::placeholder {
        color: #4b5563;
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #6b7280;
        cursor: pointer;
        z-index: 2;
    }

    .password-toggle:hover {
        color: #818cf8;
    }

    /* Form Options */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .checkbox-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13px;
        color: #94a3b8;
    }

    .checkbox-custom input {
        display: none;
    }

    .checkmark {
        width: 18px;
        height: 18px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 4px;
        display: inline-block;
        position: relative;
    }

    .checkbox-custom input:checked + .checkmark {
        background: #818cf8;
        border-color: #818cf8;
    }

    .checkbox-custom input:checked + .checkmark::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 10px;
    }

    .forgot-link {
        font-size: 13px;
        color: #818cf8;
        text-decoration: none;
        transition: color 0.3s;
    }

    .forgot-link:hover {
        color: #a78bfa;
    }

    /* Login Button */
    .btn-login-custom {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border: none;
        border-radius: 16px;
        color: white;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-login-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -8px #6366f1;
    }

    .btn-login-custom i {
        transition: transform 0.3s ease;
    }

    .btn-login-custom:hover i {
        transform: translateX(4px);
    }

    /* Register Link */
    .register-link {
        text-align: center;
        margin-top: 20px;
        font-size: 13px;
        color: #94a3b8;
    }

    .register-link a {
        color: #818cf8;
        text-decoration: none;
        font-weight: 500;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

    .back-home {
        text-align: center;
        margin-top: 16px;
    }

    .back-home a {
        color: #6b7280;
        text-decoration: none;
        font-size: 12px;
        transition: color 0.3s;
    }

    .back-home a:hover {
        color: #818cf8;
    }

    /* Alert Styles */
    .alert-custom {
        padding: 12px 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #f87171;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #34d399;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .login-grid {
            grid-template-columns: 1fr;
            max-width: 500px;
        }

        .login-brand {
            padding: 32px;
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .brand-features {
            display: none;
        }

        .login-form-container {
            padding: 40px 32px;
        }

        .brand-logo img {
            width: 60px;
            height: 60px;
        }

        .brand-title {
            font-size: 28px;
        }
    }

    @media (max-width: 480px) {
        .login-form-container {
            padding: 32px 24px;
        }

        .form-header h2 {
            font-size: 24px;
        }

        .form-options {
            flex-direction: column;
            gap: 12px;
            align-items: flex-start;
        }
    }
</style>
@endpush

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