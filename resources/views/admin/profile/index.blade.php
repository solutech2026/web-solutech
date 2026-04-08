@extends('layouts.admin')

@section('title', 'Mi Perfil')

@section('header', 'Mi Perfil')

@section('content')
<div class="profile-container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Tarjeta de Perfil -->
            <div class="profile-card text-center">
                <div class="profile-avatar mx-auto">
                    {{ substr(Auth::user()->name, 0, 2) }}
                </div>
                <h3 class="profile-name">{{ Auth::user()->name }}</h3>
                <p class="profile-role">{{ Auth::user()->roles->first()->name ?? 'Administrador' }}</p>
                <div class="mt-3">
                    <span class="profile-badge">
                        <i class="fas fa-circle"></i> Activo
                    </span>
                </div>
            </div>
            
            <!-- Tarjeta de Estadísticas -->
            <div class="stats-card">
                <h5>
                    <i class="fas fa-chart-bar"></i> Estadísticas
                </h5>
                <hr>
                <div class="stats-item">
                    <span><i class="fas fa-calendar-alt"></i> Miembro desde:</span>
                    <strong>{{ Auth::user()->created_at->format('d/m/Y') }}</strong>
                </div>
                <div class="stats-item">
                    <span><i class="fas fa-clock"></i> Último acceso:</span>
                    <strong>Hoy a las {{ now()->format('H:i') }}</strong>
                </div>
                <div class="stats-item">
                    <span><i class="fas fa-tasks"></i> Acciones realizadas:</span>
                    <strong>0</strong>
                </div>
                <div class="stats-item">
                    <span><i class="fas fa-envelope"></i> Correo:</span>
                    <strong>{{ Auth::user()->email }}</strong>
                </div>
            </div>
        </div>
        
        <div class="col-md-8 mb-4">
            <div class="profile-card">
                <ul class="nav profile-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="fas fa-user"></i> Información Personal
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password" type="button" role="tab">
                            <i class="fas fa-lock"></i> Cambiar Contraseña
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab">
                            <i class="fas fa-history"></i> Sesiones Activas
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <!-- Información Personal -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <form class="profile-form" id="profileForm" method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nombre completo</label>
                                    <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" name="phone" value="{{ Auth::user()->phone ?? '' }}" placeholder="+58 412 1234567">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Avatar</label>
                                    <input type="file" class="form-control" name="avatar" accept="image/*">
                                    <small class="text-muted">Formatos permitidos: JPG, PNG (Max: 2MB)</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
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
                            <div class="mb-3">
                                <label class="form-label">Contraseña actual</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nueva contraseña</label>
                                <input type="password" class="form-control" name="password" required>
                                <small class="text-muted">Mínimo 8 caracteres, incluyendo mayúsculas y números</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirmar nueva contraseña</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Cambiar Contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Sesiones Activas -->
                    <div class="tab-pane fade" id="sessions" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Esta es tu sesión actual
                        </div>
                        <div class="session-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-laptop-code"></i>
                                    <strong>Esta sesión (actual)</strong>
                                    <p class="text-muted small mb-0 mt-1">
                                        <i class="fas fa-map-marker-alt"></i> IP: {{ request()->ip() }} 
                                        <i class="fas fa-browser ms-2"></i> Navegador: {{ $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido' }}
                                    </p>
                                </div>
                                <span class="badge bg-success">
                                    <i class="fas fa-circle" style="font-size: 8px;"></i> Activa
                                </span>
                            </div>
                        </div>
                        <div class="text-end mt-3">
                            <button class="btn btn-outline-danger" onclick="logoutOtherSessions()">
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
<script>
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.style.borderRadius = '12px';
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    document.getElementById('profileForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        // Simular envío
        showNotification('Perfil actualizado correctamente', 'success');
        // Aquí iría el submit real
        // this.submit();
    });
    
    document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const password = this.querySelector('input[name="password"]').value;
        const confirm = this.querySelector('input[name="password_confirmation"]').value;
        
        if (password !== confirm) {
            showNotification('Las contraseñas no coinciden', 'danger');
            return;
        }
        
        if (password.length < 8) {
            showNotification('La contraseña debe tener al menos 8 caracteres', 'danger');
            return;
        }
        
        showNotification('Contraseña cambiada correctamente', 'success');
        this.reset();
        // Aquí iría el submit real
        // this.submit();
    });
    
    function logoutOtherSessions() {
        if (confirm('¿Cerrar todas las demás sesiones activas?')) {
            showNotification('Sesiones cerradas correctamente', 'success');
            // Aquí iría la llamada AJAX
        }
    }
</script>
@endpush