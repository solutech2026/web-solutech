@extends('layouts.admin')

@section('title', 'Reportes y Estadísticas')
@section('header', 'Reportes')

@section('content')
<div class="reports-container">
    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="reports-filter-card">
                <div class="filter-header">
                    <i class="fas fa-chart-line"></i>
                    <h4>Generar Reporte</h4>
                </div>
                <div class="filter-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt"></i> Tipo de Reporte
                            </label>
                            <select class="form-select" id="reportType">
                                <option value="access">📊 Accesos</option>
                                <option value="persons">👥 Personas</option>
                                <option value="cards">💳 Tarjetas NFC</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="fas fa-building"></i> Empresa / Colegio
                            </label>
                            <select class="form-select" id="reportCompany">
                                <option value="all">🏢 Todas las ubicaciones</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->type == 'company' ? '🏢' : '🏫' }} {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="fas fa-tag"></i> Categoría
                            </label>
                            <select class="form-select" id="reportCategory">
                                <option value="all">📌 Todas</option>
                                <option value="employee">💼 Empleados</option>
                                <option value="school">🎓 Personal Escolar</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="fas fa-layer-group"></i> Subcategoría
                            </label>
                            <select class="form-select" id="reportSubcategory">
                                <option value="all">📌 Todas</option>
                                <option value="student">🎓 Estudiantes</option>
                                <option value="teacher">👨‍🏫 Docentes</option>
                                <option value="administrative">📋 Administrativos</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                <i class="fas fa-chart-bar"></i> Periodo
                            </label>
                            <select class="form-select" id="reportPeriod">
                                <option value="daily">📅 Diario</option>
                                <option value="weekly">📆 Semanal</option>
                                <option value="monthly">📊 Mensual</option>
                                <option value="yearly">📈 Anual</option>
                                <option value="custom">⚙️ Personalizado</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3 date-range" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-day"></i> Fecha Desde
                            </label>
                            <input type="date" class="form-control" id="dateFrom">
                        </div>
                        <div class="col-md-3 mb-3 date-range" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-day"></i> Fecha Hasta
                            </label>
                            <input type="date" class="form-control" id="dateTo">
                        </div>
                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <button class="btn-generate" onclick="generateReport()">
                                <i class="fas fa-chart-bar"></i> Generar Reporte
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas Rápidas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_persons'] ?? 0 }}</h3>
                <p>Total Personas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['total_cards'] ?? 0 }}</h3>
                <p>Tarjetas NFC</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['assigned_cards'] ?? 0 }}</h3>
                <p>Tarjetas Asignadas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-door-open"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['today_access'] ?? 0 }}</h3>
                <p>Accesos Hoy</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['week_access'] ?? 0 }}</h3>
                <p>Accesos Semana</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $stats['month_access'] ?? 0 }}</h3>
                <p>Accesos Mes</p>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chart-line"></i>
                <h4>Accesos por Día</h4>
            </div>
            <div class="chart-body">
                <canvas id="accessChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chart-pie"></i>
                <h4>Distribución por Ubicación</h4>
            </div>
            <div class="chart-body">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chart-bar"></i>
                <h4>Top Usuarios con Más Accesos</h4>
            </div>
            <div class="chart-body">
                <canvas id="topUsersChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <div class="chart-header">
                <i class="fas fa-chart-line"></i>
                <h4>Horas Pico de Acceso</h4>
            </div>
            <div class="chart-body">
                <canvas id="hourlyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="results-card">
        <div class="results-header">
            <div class="results-title">
                <i class="fas fa-table"></i>
                <h4>Resultados del Reporte</h4>
            </div>
            <div class="results-actions">
                <button class="btn-excel" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
                <button class="btn-pdf" onclick="exportPDF()">
                    <i class="fas fa-file-pdf"></i> Exportar PDF
                </button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="reports-table" id="reportTable">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>Persona</th>
                        <th>Empresa/Colegio</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="reportBody">
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="empty-state">
                                <i class="fas fa-chart-line"></i>
                                <p>Seleccione filtros y genere un reporte</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/reports.js') }}"></script>
@endpush