/**
 * PROXICARD Reports Module
 * Adaptado de la lógica del controlador Laravel ReportController
 */

let accessChart, locationChart, topUsersChart, hourlyChart;

// Configuración de Chart.js
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 11;

/**
 * Generar reporte según filtros seleccionados (adaptado de generateReport del controlador)
 */
async function generateReport() {
    const reportType = document.getElementById('reportType')?.value || 'access';
    const period = document.getElementById('reportPeriod').value;
    const companyId = document.getElementById('reportCompany').value;
    const category = document.getElementById('reportCategory').value;
    const subcategory = document.getElementById('reportSubcategory').value;
    const dateFrom = document.getElementById('dateFrom')?.value;
    const dateTo = document.getElementById('dateTo')?.value;
    const month = document.getElementById('reportMonth')?.value;
    const year = document.getElementById('reportYear')?.value;

    // Mostrar loading
    const generateBtn = document.querySelector('.btn-generate');
    const originalText = generateBtn.innerHTML;
    generateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
    generateBtn.disabled = true;

    try {
        const response = await fetch('/admin/reports/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                report_type: period,
                category: category === 'all' ? null : category,
                subcategory: subcategory === 'all' ? null : subcategory,
                company_id: companyId === 'all' ? null : companyId,
                start_date: dateFrom,
                end_date: dateTo,
                month: month,
                year: year
            })
        });

        const data = await response.json();
        
        if (data.success) {
            renderReportData(data.data);
        } else {
            showError(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al conectar con el servidor');
    } finally {
        generateBtn.innerHTML = originalText;
        generateBtn.disabled = false;
    }
}

/**
 * Renderizar todos los datos del reporte (adaptado de getReportData)
 */
function renderReportData(data) {
    // Actualizar estadísticas
    updateStats(data.stats);
    
    // Actualizar gráficos
    updateAccessChart(data);
    updatePeakHoursChart(data.peakHours);
    updateTopUsersChart(data.topUsers);
    updateAccessByDayChart(data.accessByDay);
    
    // Actualizar tabla
    updateTable(data.logs);
    
    // Actualizar información del período
    updatePeriodInfo(data.periodLabel, data.totalRecords);
}

/**
 * Actualizar tarjetas de estadísticas
 */
function updateStats(stats) {
    const statsContainer = document.querySelector('.stats-container');
    if (!statsContainer) return;

    const statsHtml = `
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-door-open"></i></div>
            <div class="stat-info">
                <h3>${stats.total}</h3>
                <p>Total Accesos</p>
            </div>
        </div>
        <div class="stat-card granted">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <h3>${stats.granted}</h3>
                <p>Accesos Permitidos</p>
            </div>
        </div>
        <div class="stat-card denied">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <h3>${stats.denied}</h3>
                <p>Accesos Denegados</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <h3>${stats.unique_persons}</h3>
                <p>Personas Únicas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
            <div class="stat-info">
                <h3>${stats.success_rate}%</h3>
                <p>Tasa de Éxito</p>
            </div>
        </div>
    `;

    statsContainer.innerHTML = statsHtml;
}

/**
 * Actualizar gráfico de accesos principales (línea/área)
 */
function updateAccessChart(data) {
    const ctx = document.getElementById('accessChart')?.getContext('2d');
    if (!ctx) return;

    // Preparar datos para el gráfico
    const labels = data.logs.slice(0, 7).map(log => formatDate(log.access_time, 'dd/MM'));
    const values = data.logs.slice(0, 7).map((_, index) => 
        data.logs.filter(l => formatDate(l.access_time, 'dd/MM') === labels[index]).length
    );

    if (accessChart) accessChart.destroy();
    
    accessChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['Sin datos'],
            datasets: [{
                label: 'Accesos',
                data: labels.length ? values : [0],
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
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.raw} accesos` } }
            }
        }
    });
}

/**
 * Actualizar gráfico de horas pico
 */
function updatePeakHoursChart(peakHours) {
    const ctx = document.getElementById('hourlyChart')?.getContext('2d');
    if (!ctx) return;

    const hours = Object.keys(peakHours || {});
    const counts = Object.values(peakHours || {});

    if (hourlyChart) hourlyChart.destroy();
    
    hourlyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours.length ? hours : ['Sin datos'],
            datasets: [{
                label: 'Accesos por Hora',
                data: hours.length ? counts : [0],
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
}

/**
 * Actualizar gráfico de top usuarios
 */
function updateTopUsersChart(topUsers) {
    const ctx = document.getElementById('topUsersChart')?.getContext('2d');
    if (!ctx) return;

    const names = (topUsers || []).map(u => u.name);
    const counts = (topUsers || []).map(u => u.count);

    if (topUsersChart) topUsersChart.destroy();
    
    topUsersChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: names.length ? names : ['Sin datos'],
            datasets: [{
                label: 'Número de Accesos',
                data: names.length ? counts : [0],
                backgroundColor: '#667eea',
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { callbacks: { label: (ctx) => `${ctx.raw} accesos` } }
            }
        }
    });
}

/**
 * Actualizar gráfico de accesos por día de la semana
 */
function updateAccessByDayChart(accessByDay) {
    const ctx = document.getElementById('locationChart')?.getContext('2d');
    if (!ctx) return;

    const days = Object.keys(accessByDay || {});
    const counts = Object.values(accessByDay || {});

    if (locationChart) locationChart.destroy();
    
    locationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: days.length ? days : ['Sin datos'],
            datasets: [{
                label: 'Accesos por Día',
                data: days.length ? counts : [0],
                backgroundColor: '#10b981',
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
}

/**
 * Actualizar tabla de datos
 */
function updateTable(logs) {
    const tableBody = document.getElementById('reportBody');
    if (!tableBody) return;

    if (!logs || logs.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>No hay registros para el período seleccionado</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = logs.map(log => `
        <tr>
            <td>${formatDate(log.access_time, 'dd/MM/YYYY HH:mm:ss')}</td>
            <td>${escapeHtml(log.person?.full_name || 'N/A')}</td>
            <td>${escapeHtml(log.person?.document_id || 'N/A')}</td>
            <td>${escapeHtml(log.person?.company?.name || 'N/A')}</td>
            <td>${escapeHtml(log.person?.category_label || log.person?.category || 'N/A')}</td>
            <td>${escapeHtml(log.person?.subcategory_label || log.person?.subcategory || 'N/A')}</td>
            <td>${escapeHtml(log.gate || 'Puerta Principal')}</td>
            <td>${escapeHtml((log.verification_method || 'NFC').toUpperCase())}</td>
            <td><span class="status-badge ${log.status === 'granted' ? 'granted' : 'denied'}">
                ${log.status === 'granted' ? 'Permitido' : 'Denegado'}
            </span></td>
        </tr>
    `).join('');
}

/**
 * Actualizar información del período
 */
function updatePeriodInfo(periodLabel, totalRecords) {
    const periodInfo = document.querySelector('.period-info');
    if (periodInfo) {
        periodInfo.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-calendar-alt"></i>
                <strong>Período:</strong> ${periodLabel} | 
                <strong>Total registros:</strong> ${totalRecords}
            </div>
        `;
    }
}

/**
 * Exportar a Excel (CSV) - Función principal que llama al exportador según el tipo de reporte
 */
async function exportExcel() {
    const reportType = document.getElementById('reportType')?.value || 'access';
    
    try {
        if (reportType === 'access') {
            await exportAccessCSV();
        } else if (reportType === 'persons') {
            await exportPersonsCSV();
        } else if (reportType === 'cards') {
            await exportCardsCSV();
        }
    } catch (error) {
        console.error('Error al exportar:', error);
        showError('Error al exportar el reporte');
    }
}

/**
 * Exportar a PDF - Abre una nueva ventana con el PDF generado
 */
async function exportPDF() {
    const reportType = document.getElementById('reportType')?.value || 'access';
    const period = document.getElementById('reportPeriod').value;
    const companyId = document.getElementById('reportCompany').value;
    const category = document.getElementById('reportCategory').value;
    const subcategory = document.getElementById('reportSubcategory').value;
    const dateFrom = document.getElementById('dateFrom')?.value;
    const dateTo = document.getElementById('dateTo')?.value;
    const month = document.getElementById('reportMonth')?.value;
    const year = document.getElementById('reportYear')?.value;
    
    // Construir URL con parámetros
    const params = new URLSearchParams({
        report_type: period,
        category: category === 'all' ? '' : category,
        subcategory: subcategory === 'all' ? '' : subcategory,
        company_id: companyId === 'all' ? '' : companyId,
    });
    
    if (dateFrom) params.append('start_date', dateFrom);
    if (dateTo) params.append('end_date', dateTo);
    if (month) params.append('month', month);
    if (year) params.append('year', year);
    
    // Mostrar loading
    const exportBtn = document.querySelector('.btn-pdf');
    const originalText = exportBtn?.innerHTML;
    if (exportBtn) {
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando PDF...';
        exportBtn.disabled = true;
    }
    
    try {
        // Abrir en nueva ventana para PDF
        window.open(`/admin/reports/export/pdf/${reportType}?${params.toString()}`, '_blank');
        showSuccess('Generando PDF...');
    } catch (error) {
        console.error('Error:', error);
        showError('Error al generar el PDF');
    } finally {
        if (exportBtn) {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        }
    }
}

/**
 * Exportar accesos a CSV
 */
async function exportAccessCSV() {
    const period = document.getElementById('reportPeriod').value;
    const companyId = document.getElementById('reportCompany').value;
    const category = document.getElementById('reportCategory').value;
    const subcategory = document.getElementById('reportSubcategory').value;
    const dateFrom = document.getElementById('dateFrom')?.value;
    const dateTo = document.getElementById('dateTo')?.value;
    const month = document.getElementById('reportMonth')?.value;
    const year = document.getElementById('reportYear')?.value;

    // Mostrar loading
    const exportBtn = document.querySelector('.btn-excel');
    const originalText = exportBtn?.innerHTML;
    if (exportBtn) {
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
        exportBtn.disabled = true;
    }

    try {
        const response = await fetch('/admin/reports/export/access', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                report_type: period,
                category: category === 'all' ? null : category,
                subcategory: subcategory === 'all' ? null : subcategory,
                company_id: companyId === 'all' ? null : companyId,
                start_date: dateFrom,
                end_date: dateTo,
                month: month,
                year: year
            })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `reporte_accesos_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showSuccess('Reporte exportado exitosamente');
        } else {
            const error = await response.json();
            showError(error.message || 'Error al exportar el reporte');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al exportar');
    } finally {
        if (exportBtn) {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        }
    }
}

/**
 * Exportar reporte de personas a CSV
 */
async function exportPersonsCSV() {
    const companyId = document.getElementById('reportCompany').value;
    const category = document.getElementById('reportCategory').value;
    const subcategory = document.getElementById('reportSubcategory').value;

    // Mostrar loading
    const exportBtn = document.querySelector('.btn-excel');
    const originalText = exportBtn?.innerHTML;
    if (exportBtn) {
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
        exportBtn.disabled = true;
    }

    try {
        const response = await fetch('/admin/reports/export/persons', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                category: category === 'all' ? null : category,
                subcategory: subcategory === 'all' ? null : subcategory,
                company_id: companyId === 'all' ? null : companyId
            })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `reporte_personas_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showSuccess('Reporte de personas exportado exitosamente');
        } else {
            const error = await response.json();
            showError(error.message || 'Error al exportar personas');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al exportar personas');
    } finally {
        if (exportBtn) {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        }
    }
}

/**
 * Exportar reporte de tarjetas a CSV
 */
async function exportCardsCSV() {
    const status = document.getElementById('cardStatus')?.value || 'all';
    const companyId = document.getElementById('reportCompany').value;

    // Mostrar loading
    const exportBtn = document.querySelector('.btn-excel');
    const originalText = exportBtn?.innerHTML;
    if (exportBtn) {
        exportBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Exportando...';
        exportBtn.disabled = true;
    }

    try {
        const response = await fetch('/admin/reports/export/cards', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                status: status,
                company_id: companyId === 'all' ? null : companyId
            })
        });

        if (response.ok) {
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `reporte_tarjetas_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showSuccess('Reporte de tarjetas exportado exitosamente');
        } else {
            const error = await response.json();
            showError(error.message || 'Error al exportar tarjetas');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Error al exportar tarjetas');
    } finally {
        if (exportBtn) {
            exportBtn.innerHTML = originalText;
            exportBtn.disabled = false;
        }
    }
}

/**
 * Mostrar mensaje de éxito
 */
function showSuccess(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success alert-dismissible fade show';
    successDiv.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <strong>Éxito:</strong> ${escapeHtml(message)}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    `;
    
    const container = document.querySelector('.reports-container');
    if (container) {
        container.insertBefore(successDiv, container.firstChild);
        setTimeout(() => successDiv.remove(), 3000);
    }
}

/**
 * Obtener estadísticas para gráficos (AJAX)
 */
async function loadStatistics(period = 'month') {
    try {
        const response = await fetch(`/admin/reports/statistics?period=${period}`);
        const data = await response.json();
        
        if (data.success) {
            updateStatisticsCharts(data.data);
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

/**
 * Actualizar gráficos de estadísticas
 */
function updateStatisticsCharts(data) {
    // Implementar según necesidades específicas
    console.log('Estadísticas actualizadas:', data);
}

/**
 * Formatear fecha
 */
function formatDate(dateString, format = 'dd/MM/YYYY HH:mm:ss') {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    
    const formats = {
        'dd/MM': `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}`,
        'dd/MM/YYYY': `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()}`,
        'dd/MM/YYYY HH:mm:ss': `${String(date.getDate()).padStart(2, '0')}/${String(date.getMonth() + 1).padStart(2, '0')}/${date.getFullYear()} ${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')}`
    };
    
    return formats[format] || formats['dd/MM/YYYY HH:mm:ss'];
}

/**
 * Escapar HTML para prevenir XSS
 */
function escapeHtml(str) {
    if (!str) return 'N/A';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/**
 * Mostrar mensaje de error
 */
function showError(message) {
    const tableBody = document.getElementById('reportBody');
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="empty-state error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>${escapeHtml(message)}</p>
                    </div>
                </td>
            </tr>
        `;
    }
    
    // Mostrar toast o alerta
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger alert-dismissible fade show';
    errorDiv.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <strong>Error:</strong> ${escapeHtml(message)}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    `;
    
    const container = document.querySelector('.reports-container');
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
        setTimeout(() => errorDiv.remove(), 5000);
    }
}

/**
 * Mostrar/ocultar campos de fecha según periodo seleccionado
 */
function toggleDateRange() {
    const period = document.getElementById('reportPeriod').value;
    const dateRangeFields = document.querySelectorAll('.date-range');
    const monthYearFields = document.querySelectorAll('.month-year-range');
    
    if (period === 'custom') {
        dateRangeFields.forEach(field => field.style.display = 'block');
        monthYearFields.forEach(field => field.style.display = 'none');
    } else if (period === 'monthly') {
        dateRangeFields.forEach(field => field.style.display = 'none');
        monthYearFields.forEach(field => field.style.display = 'block');
    } else {
        dateRangeFields.forEach(field => field.style.display = 'none');
        monthYearFields.forEach(field => field.style.display = 'none');
    }
}

/**
 * Cargar empresas para el filtro
 */
async function loadCompanies() {
    try {
        const response = await fetch('/api/companies/active');
        const companies = await response.json();
        
        const companySelect = document.getElementById('reportCompany');
        if (companySelect && companies.length) {
            const options = companies.map(company => 
                `<option value="${company.id}">${escapeHtml(company.name)}</option>`
            ).join('');
            companySelect.innerHTML = '<option value="all">Todas las empresas/colegios</option>' + options;
        }
    } catch (error) {
        console.error('Error loading companies:', error);
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    // Configurar eventos
    const periodSelect = document.getElementById('reportPeriod');
    if (periodSelect) {
        periodSelect.addEventListener('change', toggleDateRange);
    }
    
    // Configurar botones de exportación
    const exportExcelBtn = document.getElementById('exportExcel');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', exportExcel);
    }
    
    const exportPdfBtn = document.getElementById('exportPDF');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', exportPDF);
    }
    
    // Cargar empresas
    loadCompanies();
    
    // Inicializar gráficos vacíos
    const ctx = document.getElementById('accessChart')?.getContext('2d');
    if (ctx) {
        accessChart = new Chart(ctx, {
            type: 'line',
            data: { labels: ['Sin datos'], datasets: [{ label: 'Accesos', data: [0] }] },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }
    
    // Cargar estadísticas iniciales
    loadStatistics('month');
});