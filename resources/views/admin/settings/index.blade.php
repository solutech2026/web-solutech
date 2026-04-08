@extends('layouts.admin')

@section('title', 'Configuración del Sistema')

@section('header', 'Configuración')

@section('content')
<div class="settings-container">
    <div class="row">
        <!-- Configuración General -->
        <div class="col-md-6 mb-4">
            <div class="settings-card">
                <h4 class="mb-3">
                    <i class="fas fa-globe"></i> Configuración General
                </h4>
                <form id="generalSettingsForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nombre del Sistema</label>
                        <input type="text" class="form-control" name="system_name" value="SoluTech Access Control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Zona Horaria</label>
                        <select class="form-select" name="timezone">
                            <option value="America/Caracas" selected>Caracas (UTC-4)</option>
                            <option value="America/New_York">Nueva York (UTC-5)</option>
                            <option value="Europe/Madrid">Madrid (UTC+1)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Formato de Fecha</label>
                        <select class="form-select" name="date_format">
                            <option value="d/m/Y" selected>DD/MM/YYYY</option>
                            <option value="m/d/Y">MM/DD/YYYY</option>
                            <option value="Y-m-d">YYYY-MM-DD</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Idioma</label>
                        <select class="form-select" name="language">
                            <option value="es" selected>Español</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Configuración de Notificaciones -->
            <div class="settings-card">
                <h4 class="mb-3">
                    <i class="fas fa-bell"></i> Notificaciones
                </h4>
                <form id="notificationsForm">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                            <label class="form-check-label" for="emailNotifications">
                                Notificaciones por correo electrónico
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="accessAlerts">
                            <label class="form-check-label" for="accessAlerts">
                                Alertas de accesos sospechosos
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correos para notificaciones</label>
                        <input type="text" class="form-control" placeholder="correo1@ejemplo.com, correo2@ejemplo.com">
                        <small class="form-text text-muted">Separar múltiples correos con comas</small>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <!-- Configuración de Seguridad -->
            <div class="settings-card">
                <h4 class="mb-3">
                    <i class="fas fa-shield-alt"></i> Seguridad
                </h4>
                <form id="securitySettingsForm">
                    <div class="mb-3">
                        <label class="form-label">Intentos máximos de login</label>
                        <input type="number" class="form-control" name="max_attempts" value="5" min="1" max="10">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiempo de bloqueo (minutos)</label>
                        <input type="number" class="form-control" name="lockout_time" value="15" min="1" max="60">
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                            <label class="form-check-label" for="twoFactorAuth">
                                Autenticación de dos factores (2FA)
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="sessionTimeout" checked>
                            <label class="form-check-label" for="sessionTimeout">
                                Cierre automático de sesión por inactividad
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiempo de inactividad (minutos)</label>
                        <input type="number" class="form-control" name="session_timeout" value="30" min="5" max="120">
                    </div>
                </form>
            </div>

            <!-- Configuración de Respaldos -->
            <div class="settings-card">
                <h4 class="mb-3">
                    <i class="fas fa-database"></i> Respaldos
                </h4>
                <form id="backupSettingsForm">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autoBackup" checked>
                            <label class="form-check-label" for="autoBackup">
                                Respaldo automático
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Frecuencia de respaldo</label>
                        <select class="form-select" name="backup_frequency">
                            <option value="daily">Diario</option>
                            <option value="weekly" selected>Semanal</option>
                            <option value="monthly">Mensual</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora de respaldo</label>
                        <input type="time" class="form-control" name="backup_time" value="02:00">
                    </div>
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-primary" onclick="manualBackup()">
                            <i class="fas fa-database"></i> Realizar Respaldo Ahora
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Configuración de Integraciones -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="settings-card">
                <h4 class="mb-3">
                    <i class="fas fa-plug"></i> Integraciones
                </h4>
                <div class="row">
                    <div class="col-md-6">
                        <div class="integration-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fab fa-whatsapp"></i>
                                    <strong>WhatsApp API</strong>
                                    <p class="text-muted small mb-0 mt-1">Enviar notificaciones por WhatsApp</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="whatsappIntegration">
                                </div>
                            </div>
                        </div>
                        <div class="integration-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-id-card"></i>
                                    <strong>Lectores NFC</strong>
                                    <p class="text-muted small mb-0 mt-1">Configurar lectores de tarjetas NFC</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="configureNFCReaders()">
                                    <i class="fas fa-cog"></i> Configurar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="integration-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-print"></i>
                                    <strong>Impresoras de Tickets</strong>
                                    <p class="text-muted small mb-0 mt-1">Configurar impresión de tickets</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="configurePrinters()">
                                    <i class="fas fa-cog"></i> Configurar
                                </button>
                            </div>
                        </div>
                        <div class="integration-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <strong>API Externa</strong>
                                    <p class="text-muted small mb-0 mt-1">Integración con servicios externos</p>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" onclick="configureAPI()">
                                    <i class="fas fa-cog"></i> Configurar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="row">
        <div class="col-12">
            <div class="settings-actions">
                <button class="btn btn-secondary me-2" onclick="resetSettings()">
                    <i class="fas fa-undo"></i> Restablecer
                </button>
                <button class="btn btn-primary" onclick="saveSettings()">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/settings.css') }}">
@endpush

@push('scripts')
<script>
    function saveSettings() {
        // Recopilar datos de todos los formularios
        const generalData = {
            system_name: document.querySelector('input[name="system_name"]').value,
            timezone: document.querySelector('select[name="timezone"]').value,
            date_format: document.querySelector('select[name="date_format"]').value,
            language: document.querySelector('select[name="language"]').value
        };
        
        const securityData = {
            max_attempts: document.querySelector('input[name="max_attempts"]').value,
            lockout_time: document.querySelector('input[name="lockout_time"]').value,
            two_factor_auth: document.getElementById('twoFactorAuth').checked,
            session_timeout: document.getElementById('sessionTimeout').checked,
            session_timeout_minutes: document.querySelector('input[name="session_timeout"]').value
        };
        
        const notificationsData = {
            email_notifications: document.getElementById('emailNotifications').checked,
            access_alerts: document.getElementById('accessAlerts').checked,
            notification_emails: document.querySelector('#notificationsForm input[type="text"]').value
        };
        
        const backupData = {
            auto_backup: document.getElementById('autoBackup').checked,
            backup_frequency: document.querySelector('select[name="backup_frequency"]').value,
            backup_time: document.querySelector('input[name="backup_time"]').value
        };
        
        const integrationsData = {
            whatsapp: document.getElementById('whatsappIntegration').checked
        };
        
        // Aquí iría la llamada AJAX para guardar
        console.log('Guardando configuración:', {
            general: generalData,
            security: securityData,
            notifications: notificationsData,
            backup: backupData,
            integrations: integrationsData
        });
        
        // Mostrar mensaje de éxito
        showNotification('Configuración guardada correctamente', 'success');
    }
    
    function resetSettings() {
        if (confirm('¿Restablecer toda la configuración a los valores predeterminados?')) {
            // Restablecer formularios
            document.getElementById('generalSettingsForm').reset();
            document.getElementById('securitySettingsForm').reset();
            document.getElementById('notificationsForm').reset();
            document.getElementById('backupSettingsForm').reset();
            
            // Restablecer switches
            document.getElementById('emailNotifications').checked = true;
            document.getElementById('sessionTimeout').checked = true;
            document.getElementById('autoBackup').checked = true;
            document.getElementById('whatsappIntegration').checked = false;
            document.getElementById('accessAlerts').checked = false;
            document.getElementById('twoFactorAuth').checked = false;
            
            showNotification('Configuración restablecida', 'info');
        }
    }
    
    function manualBackup() {
        showNotification('Iniciando respaldo manual...', 'info');
        setTimeout(() => {
            showNotification('Respaldo completado exitosamente', 'success');
        }, 2000);
    }
    
    function configureNFCReaders() {
        showNotification('Configuración de lectores NFC', 'info');
    }
    
    function configurePrinters() {
        showNotification('Configuración de impresoras', 'info');
    }
    
    function configureAPI() {
        showNotification('Configuración de API externa', 'info');
    }
    
    function showNotification(message, type) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = `alert alert-${type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.style.borderRadius = '12px';
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-info-circle'}"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endpush