@extends('layouts.admin')

@section('title', 'Reportes y Estadísticas')

@section('header', 'Reportes')

@section('content')
<div class="reports-container">
    <!-- Filtros -->
    <div class="row">
        <div class="col-12">
            <div class="reports-filter-card">
                <h4>
                    <i class="fas fa-chart-line"></i> Generar Reporte
                </h4>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Ubicación</label>
                        <select class="form-select" id="reportCompany">
                            <option value="all">Todas las ubicaciones</option>
                            <option value="solutech">SoluTech (Oficina)</option>
                            <option value="avila">Parque Ávila</option>
                            <option value="waraira">Parque Warairarepano</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="reportType">
                            <option value="access">Accesos</option>
                            <option value="persons">Personas</option>
                            <option value="cards">Tarjetas NFC</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Fecha Desde</label>
                        <input type="date" class="form-control" id="dateFrom">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Fecha Hasta</label>
                        <input type="date" class="form-control" id="dateTo">
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="generateReport()">
                            <i class="fas fa-chart-bar"></i> Generar Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-card">
                <h4>
                    <i class="fas fa-chart-line"></i> Accesos por Día
                </h4>
                <div class="chart-container">
                    <canvas id="accessChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h4>
                    <i class="fas fa-chart-pie"></i> Distribución por Ubicación
                </h4>
                <div class="chart-container">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="chart-card">
                <h4>
                    <i class="fas fa-chart-bar"></i> Top Usuarios con Más Accesos
                </h4>
                <div class="chart-container">
                    <canvas id="topUsersChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h4>
                    <i class="fas fa-chart-line"></i> Horas Pico de Acceso
                </h4>
                <div class="chart-container">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Resultados -->
    <div class="row">
        <div class="col-12">
            <div class="results-card">
                <div class="results-header">
                    <h4>
                        <i class="fas fa-table"></i> Resultados del Reporte
                    </h4>
                    <div class="btn-group">
                        <button class="btn-excel" onclick="exportExcel()">
                            <i class="fas fa-file-excel"></i> Exportar Excel
                        </button>
                        <button class="btn-pdf" onclick="exportPDF()">
                            <i class="fas fa-file-pdf"></i> Exportar PDF
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table reports-table" id="reportTable">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Persona</th>
                                <th>Ubicación</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="reportBody">
                            <tr>
                                <td colspan="6" class="text-center">
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
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/reports.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let accessChart, locationChart, topUsersChart, hourlyChart;
    
    function generateReport() {
        const company = document.getElementById('reportCompany').value;
        const type = document.getElementById('reportType').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        // Mostrar loading
        const generateBtn = document.querySelector('.reports-filter-card .btn-primary');
        const originalText = generateBtn.innerHTML;
        generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        generateBtn.disabled = true;
        
        // Simular carga
        setTimeout(() => {
            // Datos simulados según el tipo de reporte
            if (type === 'access') {
                generateAccessReport();
            } else if (type === 'persons') {
                generatePersonsReport();
            } else {
                generateCardsReport();
            }
            
            generateBtn.innerHTML = originalText;
            generateBtn.disabled = false;
        }, 1000);
    }
    
    function generateAccessReport() {
        const days = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        const accessData = [45, 52, 48, 61, 73, 89, 67];
        
        // Gráfico de accesos por día
        if (accessChart) accessChart.destroy();
        const ctx = document.getElementById('accessChart').getContext('2d');
        accessChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [{
                    label: 'Accesos',
                    data: accessData,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#667eea',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
        
        // Gráfico de ubicaciones
        if (locationChart) locationChart.destroy();
        const ctx2 = document.getElementById('locationChart').getContext('2d');
        locationChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['SoluTech', 'Parque Ávila', 'Warairarepano'],
                datasets: [{
                    data: [156, 243, 98],
                    backgroundColor: ['#667eea', '#10b981', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        
        // Gráfico de top usuarios
        if (topUsersChart) topUsersChart.destroy();
        const ctx3 = document.getElementById('topUsersChart').getContext('2d');
        topUsersChart = new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['Ana García', 'Carlos Rodríguez', 'María López', 'Visitante 1', 'Visitante 2'],
                datasets: [{
                    label: 'Número de Accesos',
                    data: [45, 38, 32, 25, 18],
                    backgroundColor: '#667eea',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
        
        // Gráfico de horas pico
        if (hourlyChart) hourlyChart.destroy();
        const ctx4 = document.getElementById('hourlyChart').getContext('2d');
        hourlyChart = new Chart(ctx4, {
            type: 'line',
            data: {
                labels: ['8am', '10am', '12pm', '2pm', '4pm', '6pm', '8pm'],
                datasets: [{
                    label: 'Accesos',
                    data: [12, 25, 42, 38, 35, 28, 15],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#f59e0b',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });
        
        // Actualizar tabla
        const tableBody = document.getElementById('reportBody');
        tableBody.innerHTML = `
            <tr><td>${new Date().toLocaleDateString()} 08:30</td><td>Ana García</td><td>SoluTech</td><td>Entrada</td><td>NFC</td><td><span class="status-badge granted">Permitido</span></td></tr>
            <tr><td>${new Date().toLocaleDateString()} 09:15</td><td>Visitante</td><td>Parque Ávila</td><td>Entrada</td><td>Manual</td><td><span class="status-badge granted">Permitido</span></td></tr>
            <tr><td>${new Date().toLocaleDateString()} 17:30</td><td>Carlos Rodríguez</td><td>SoluTech</td><td>Salida</td><td>NFC</td><td><span class="status-badge granted">Permitido</span></td></tr>
            <tr><td>${new Date().toLocaleDateString()} 14:20</td><td>María López</td><td>Warairarepano</td><td>Entrada</td><td>QR</td><td><span class="status-badge granted">Permitido</span></td></tr>
        `;
    }
    
    function generatePersonsReport() {
        // Datos para reporte de personas
        if (locationChart) locationChart.destroy();
        const ctx2 = document.getElementById('locationChart').getContext('2d');
        locationChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['SoluTech', 'Parque Ávila', 'Warairarepano'],
                datasets: [{
                    label: 'Personas Registradas',
                    data: [25, 180, 95],
                    backgroundColor: ['#667eea', '#10b981', '#f59e0b'],
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
        
        const tableBody = document.getElementById('reportBody');
        tableBody.innerHTML = `
            <tr><td>-</td><td>Ana García</td><td>SoluTech</td><td>Empleado</td><td>Tecnología</td><td><span class="status-badge granted">Activo</span></td></tr>
            <tr><td>-</td><td>Carlos Rodríguez</td><td>SoluTech</td><td>Empleado</td><td>Tecnología</td><td><span class="status-badge granted">Activo</span></td></tr>
            <tr><td>-</td><td>Visitante 1</td><td>Parque Ávila</td><td>Visitante</td><td>Recreación</td><td><span class="status-badge granted">Activo</span></td></tr>
        `;
    }
    
    function generateCardsReport() {
        // Datos para reporte de tarjetas
        if (locationChart) locationChart.destroy();
        const ctx2 = document.getElementById('locationChart').getContext('2d');
        locationChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Asignadas', 'Sin Asignar'],
                datasets: [{
                    data: [2, 1],
                    backgroundColor: ['#10b981', '#f59e0b']
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
        
        const tableBody = document.getElementById('reportBody');
        tableBody.innerHTML = `
            <tr><td>-</td><td>NFC-ABC123</td><td>Ana García</td><td>Asignada</td><td>Activa</td><td><span class="status-badge granted">Activa</span></td></tr>
            <tr><td>-</td><td>NFC-DEF456</td><td>Sin asignar</td><td>Disponible</td><td>Inactiva</td><td><span class="status-badge denied">Sin asignar</span></td></tr>
            <tr><td>-</td><td>NFC-GHI789</td><td>Carlos Rodríguez</td><td>Asignada</td><td>Activa</td><td><span class="status-badge granted">Activa</span></td></tr>
        `;
    }
    
    function exportExcel() {
        alert('Exportando a Excel...');
    }
    
    function exportPDF() {
        alert('Exportando a PDF...');
    }
    
    // Inicializar gráficos vacíos
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('accessChart').getContext('2d');
        accessChart = new Chart(ctx, {
            type: 'line',
            data: { labels: [], datasets: [] },
            options: { responsive: true, maintainAspectRatio: true }
        });
    });
</script>
@endpush