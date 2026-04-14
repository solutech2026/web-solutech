@extends('layouts.admin')

@section('title', 'Mi Perfil')
@section('header', 'Mi Perfil')

@section('content')
<div class="profile-container">
    <div class="row g-4">
        <!-- Columna izquierda -->
        <div class="col-xl-4 col-lg-5">
            <!-- Tarjeta de Perfil -->
            <div class="card-modern profile-info-card mb-4">
                <div class="profile-header-gradient"></div>
                <div class="profile-avatar-wrapper">
                    <div class="profile-avatar">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="profile-status online">
                        <i class="fas fa-circle"></i>
                    </div>
                </div>
                <div class="profile-info-content">
                    <h3 class="profile-name">{{ Auth::user()->name }}</h3>
                    <p class="profile-role">
                        <i class="fas fa-shield-alt"></i>
                        {{ Auth::user()->roles->first()->name ?? 'Administrador' }}
                    </p>
                    <div class="profile-badge-group">
                        <span class="badge-modern badge-active">
                            <i class="fas fa-circle"></i> Activo
                        </span>
                        <span class="badge-modern badge-verified">
                            <i class="fas fa-check-circle"></i> Verificado
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Tarjeta de Estadísticas -->
            <div class="card-modern stats-card">
                <div class="card-modern-header">
                    <i class="fas fa-chart-line"></i>
                    <h5>Estadísticas</h5>
                </div>
                <div class="card-modern-body">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Miembro desde</span>
                            <strong class="stat-value">{{ Auth::user()->created_at->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Último acceso</span>
                            <strong class="stat-value">Hoy a las {{ now()->format('H:i') }}</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Acciones realizadas</span>
                            <strong class="stat-value">0</strong>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-details">
                            <span class="stat-label">Correo electrónico</span>
                            <strong class="stat-value">{{ Auth::user()->email }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Columna derecha -->
        <div class="col-xl-8 col-lg-7">
            <div class="card-modern profile-tabs-card">
                <ul class="nav nav-tabs-modern" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="fas fa-user"></i>
                            <span>Información Personal</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                            <i class="fas fa-lock"></i>
                            <span>Cambiar Contraseña</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab">
                            <i class="fas fa-history"></i>
                            <span>Sesiones Activas</span>
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content-modern">
                    <!-- Información Personal -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <form class="profile-form" id="profileForm" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fas fa-user-circle"></i> Nombre completo
                                        </label>
                                        <input type="text" class="form-control-modern" name="name" value="{{ Auth::user()->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fas fa-envelope"></i> Correo electrónico
                                        </label>
                                        <input type="email" class="form-control-modern" name="email" value="{{ Auth::user()->email }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fas fa-phone"></i> Teléfono
                                        </label>
                                        <input type="text" class="form-control-modern" name="phone" value="{{ Auth::user()->phone ?? '' }}" placeholder="+58 412 1234567">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-modern">
                                        <label class="form-label-modern">
                                            <i class="fas fa-image"></i> Avatar
                                        </label>
                                        <input type="file" class="form-control-modern" name="avatar" accept="image/*">
                                        <small class="form-text-modern">Formatos: JPG, PNG (Max: 2MB)</small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-modern btn-primary-modern">
                                    <i class="fas fa-save"></i> Actualizar Perfil
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Cambiar Contraseña -->
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <form class="profile-form" id="passwordForm" method="POST" action="{{ route('admin.profile.password') }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-key"></i> Contraseña actual
                                </label>
                                <input type="password" class="form-control-modern" name="current_password" required>
                            </div>
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-lock"></i> Nueva contraseña
                                </label>
                                <input type="password" class="form-control-modern" name="password" required>
                                <small class="form-text-modern">Mínimo 8 caracteres, incluyendo mayúsculas y números</small>
                            </div>
                            <div class="form-group-modern">
                                <label class="form-label-modern">
                                    <i class="fas fa-check-circle"></i> Confirmar nueva contraseña
                                </label>
                                <input type="password" class="form-control-modern" name="password_confirmation" required>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn-modern btn-primary-modern">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Sesiones Activas -->
                    <div class="tab-pane fade" id="sessions" role="tabpanel">
                        <div class="alert-modern alert-info-modern">
                            <i class="fas fa-info-circle"></i>
                            <span>Esta es tu sesión actual. Las demás sesiones se muestran a continuación.</span>
                        </div>
                        
                        <div class="session-item-modern current-session">
                            <div class="session-icon">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <div class="session-info">
                                <div class="session-title">
                                    <strong>Esta sesión (actual)</strong>
                                    <span class="badge-modern badge-current">Activa ahora</span>
                                </div>
                                <div class="session-details">
                                    <span><i class="fas fa-map-marker-alt"></i> IP: {{ request()->ip() }}</span>
                                    <span><i class="fas fa-browser"></i> {{ $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="session-actions">
                            <button type="button" class="btn-modern btn-outline-modern" onclick="logoutOtherSessions()">
                                <i class="fas fa-sign-out-alt"></i> Cerrar todas las demás sesiones
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('js/profile.js') }}"></script>
@endpush