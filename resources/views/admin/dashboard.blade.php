@extends('layouts.admin')

@section('title', 'Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="dashboard-container">
    <!-- Welcome Card -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="welcome-card">
                <h3>
                    <i class="fas fa-user-circle"></i>
                    ¡Bienvenido de vuelta, {{ Auth::user()->name }}!
                </h3>
                <p>Has iniciado sesión correctamente en el sistema de control de acceso SoluTech.</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">Usuarios Totales</div>
                <div class="stat-value">{{ $totalUsers ?? \App\Models\User::count() }}</div>
                <div class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> +12% este mes
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-door-open"></i>
                </div>
                <div class="stat-title">Accesos Hoy</div>
                <div class="stat-value">0</div>
                <div class="stat-trend">
                    <i class="fas fa-clock"></i> Esperando registros
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-title">Administradores</div>
                <div class="stat-value">{{ $adminCount ?? \App\Models\User::role(['super-admin', 'admin'])->count() }}</div>
                <div class="stat-trend">
                    <i class="fas fa-shield-alt"></i> Activos
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-title">Estado Sistema</div>
                <div class="stat-value">Activo</div>
                <div class="stat-trend up">
                    <i class="fas fa-heartbeat"></i> Operativo
                </div>
            </div>
        </div>
    </div>

    <!-- Content Cards -->
    <div class="row">
        <div class="col-md-7 mb-4">
            <div class="content-card">
                <h4>
                    <i class="fas fa-info-circle"></i>
                    Información del Sistema
                </h4>
                <p>Bienvenido al panel de control del sistema de control de acceso. Aquí podrás gestionar todos los aspectos del sistema, desde usuarios hasta reportes de accesos.</p>

                <div class="mt-4">
                    <h5>
                        <i class="fas fa-history"></i>
                        Últimos Accesos
                    </h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No hay registros de acceso recientes.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5 mb-4">
            <div class="content-card">
                <h4>
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h4>

                @can('manage access control')
                <a href="{{ route('admin.access-control.index') }}" class="quick-action-btn">
                    <i class="fas fa-door-open"></i>
                    <span>Control de Acceso</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                @endcan

                <a href="{{ route('admin.nfc-cards.index') }}" class="quick-action-btn">
                    <i class="fas fa-id-card"></i>
                    <span>Gestionar Tarjetas NFC</span>
                    <i class="fas fa-arrow-right"></i>
                </a>

                @can('manage users')
                <a href="{{ route('admin.users.index') }}" class="quick-action-btn">
                    <i class="fas fa-users"></i>
                    <span>Gestionar Usuarios</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
                @endcan

                <a href="{{ route('admin.profile.index') }}" class="quick-action-btn">
                    <i class="fas fa-user-circle"></i>
                    <span>Mi Perfil</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section (Opcional) -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <h4>
                    <i class="fas fa-chart-line"></i>
                    Actividad Reciente
                </h4>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay actividad reciente para mostrar.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush
