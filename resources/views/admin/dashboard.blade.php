@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Hero Section -->
    <div class="welcome-hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="greeting-badge">
                    <i class="fas fa-chart-line"></i>
                    <span>Panel de Control</span>
                </div>
                <h1>
                    ¡Bienvenido, {{ Auth::user()->name }}!
                </h1>
                <p>Gestiona el control de acceso, usuarios y reportes desde un solo lugar.</p>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-value">{{ now()->format('d/m/Y') }}</div>
                        <div class="hero-stat-label">Fecha Actual</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-value">{{ now()->format('h:i A') }}</div>
                        <div class="hero-stat-label">Hora</div>
                    </div>
                </div>
            </div>
            <div class="hero-illustration">
                <div class="floating-shapes">
                    <div class="shape shape-1"></div>
                    <div class="shape shape-2"></div>
                    <div class="shape shape-3"></div>
                </div>
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card-modern">
            <div class="stat-card-gradient gradient-blue"></div>
            <div class="stat-card-content">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalUsers ?? \App\Models\User::count() }}</h3>
                    <p>Usuarios Totales</p>
                </div>
            </div>
            <div class="stat-footer">
                <span class="trend up">
                    <i class="fas fa-arrow-up"></i> +12%
                </span>
                <span>vs mes anterior</span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-gradient gradient-green"></div>
            <div class="stat-card-content">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-info">
                    <h3>0</h3>
                    <p>Accesos Hoy</p>
                </div>
            </div>
            <div class="stat-footer">
                <span class="trend neutral">
                    <i class="fas fa-clock"></i> Esperando
                </span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-gradient gradient-purple"></div>
            <div class="stat-card-content">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $adminCount ?? \App\Models\User::role(['super-admin', 'admin'])->count() }}</h3>
                    <p>Administradores</p>
                </div>
            </div>
            <div class="stat-footer">
                <span class="trend up">
                    <i class="fas fa-shield-alt"></i> Activos
                </span>
            </div>
        </div>

        <div class="stat-card-modern">
            <div class="stat-card-gradient gradient-cyan"></div>
            <div class="stat-card-content">
                <div class="stat-icon-wrapper">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>100%</h3>
                    <p>Uptime Sistema</p>
                </div>
            </div>
            <div class="stat-footer">
                <span class="trend up">
                    <i class="fas fa-heartbeat"></i> Operativo
                </span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="dashboard-grid">
        <!-- System Info Card -->
        <div class="info-card">
            <div class="card-header-modern">
                <div class="header-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h4>Información del Sistema</h4>
                <span class="header-badge">Versión 2.0</span>
            </div>
            <div class="card-body-modern">
                <p>Bienvenido al panel de control de Solubase. Aquí podrás gestionar todos los aspectos del sistema de control de acceso, desde usuarios hasta reportes detallados.</p>
                
                <div class="system-stats">
                    <div class="system-stat-item">
                        <span class="stat-label">Base de Datos</span>
                        <span class="stat-value-badge success">Conectada</span>
                    </div>
                    <div class="system-stat-item">
                        <span class="stat-label">API Status</span>
                        <span class="stat-value-badge success">Activa</span>
                    </div>
                    <div class="system-stat-item">
                        <span class="stat-label">Servidor</span>
                        <span class="stat-value-badge">Laragon</span>
                    </div>
                </div>

                <div class="recent-section">
                    <h5>
                        <i class="fas fa-history"></i>
                        Últimos Accesos
                    </h5>
                    <div class="empty-state">
                        <i class="fas fa-door-closed"></i>
                        <p>No hay registros de acceso recientes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="actions-card">
            <div class="card-header-modern">
                <div class="header-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h4>Acciones Rápidas</h4>
                <span class="header-badge">Acceso Directo</span>
            </div>
            <div class="card-body-modern">
                <div class="quick-actions-grid">
                    @can('manage access control')
                    <a href="{{ route('admin.access-control.index') }}" class="quick-action-modern">
                        <div class="action-icon purple">
                            <i class="fas fa-door-open"></i>
                        </div>
                        <div class="action-info">
                            <span>Control de Acceso</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    @endcan

                    <a href="{{ route('admin.nfc-cards.index') }}" class="quick-action-modern">
                        <div class="action-icon blue">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="action-info">
                            <span>Gestionar Tarjetas</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>

                    @can('manage users')
                    <a href="{{ route('admin.users.index') }}" class="quick-action-modern">
                        <div class="action-icon green">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="action-info">
                            <span>Gestionar Usuarios</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                    @endcan

                    <a href="{{ route('admin.reports.index') }}" class="quick-action-modern">
                        <div class="action-icon cyan">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="action-info">
                            <span>Ver Reportes</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.profile.index') }}" class="quick-action-modern">
                        <div class="action-icon orange">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="action-info">
                            <span>Mi Perfil</span>
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="activity-card">
        <div class="card-header-modern">
            <div class="header-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4>Actividad Reciente del Sistema</h4>
            <button class="refresh-btn" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i>
            </button>
        </div>
        <div class="card-body-modern">
            <div class="empty-state large">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h6>No hay actividad reciente</h6>
                <p>Los registros de actividad aparecerán aquí cuando los usuarios interactúen con el sistema</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@push('scripts')
<script>
    // Animación de entrada para las tarjetas
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.stat-card-modern, .info-card, .actions-card, .activity-card');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
</script>
@endpush
