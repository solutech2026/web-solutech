{{-- resources/views/admin/settings/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Configuración del Sistema')
@section('header', 'Configuración')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="settings-wrapper">
    <div class="settings-header">
        <div class="header-info">
            <h1 class="settings-title">
                <i class="fas fa-sliders-h"></i>
                Panel de Configuración
            </h1>
            <p class="settings-subtitle">Gestiona la configuración global del sistema de control de acceso</p>
        </div>
        <div class="header-actions">
            <div class="last-saved" id="lastSavedInfo">
                <i class="fas fa-clock"></i>
                <span>Última modificación: {{ $lastUpdated ?? 'Nunca' }}</span>
            </div>
        </div>
    </div>

    <form id="settingsForm">
        @csrf
        <div class="settings-grid">
            <!-- Tarjeta: Configuración General -->
            <div class="settings-card card-general">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <div class="header-text">
                        <h3>Configuración General</h3>
                        <p>Personaliza la información principal del sistema</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag"></i>
                            Nombre del Sistema
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="system_name" 
                               name="general[system_name]" 
                               value="{{ $settings['system_name'] ?? 'SoluTech Access Control' }}"
                               placeholder="Ej: ProxiCard Access">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i>
                                Zona Horaria
                            </label>
                            <select class="form-control" id="timezone" name="general[timezone]">
                                <option value="America/Caracas" {{ ($settings['timezone'] ?? '') == 'America/Caracas' ? 'selected' : '' }}>Caracas (UTC-4)</option>
                                <option value="America/New_York" {{ ($settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>Nueva York (UTC-5)</option>
                                <option value="Europe/Madrid" {{ ($settings['timezone'] ?? '') == 'Europe/Madrid' ? 'selected' : '' }}>Madrid (UTC+1)</option>
                                <option value="America/Mexico_City" {{ ($settings['timezone'] ?? '') == 'America/Mexico_City' ? 'selected' : '' }}>Ciudad de México (UTC-6)</option>
                                <option value="America/Bogota" {{ ($settings['timezone'] ?? '') == 'America/Bogota' ? 'selected' : '' }}>Bogotá (UTC-5)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Formato de Fecha
                            </label>
                            <select class="form-control" id="date_format" name="general[date_format]">
                                <option value="d/m/Y" {{ ($settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="m/d/Y" {{ ($settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="Y-m-d" {{ ($settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-language"></i>
                                Idioma Principal
                            </label>
                            <select class="form-control" id="language" name="general[language]">
                                <option value="es" {{ ($settings['language'] ?? '') == 'es' ? 'selected' : '' }}>Español</option>
                                <option value="en" {{ ($settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="pt" {{ ($settings['language'] ?? '') == 'pt' ? 'selected' : '' }}>Português</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Seguridad -->
            <div class="settings-card card-security">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="header-text">
                        <h3>Seguridad y Acceso</h3>
                        <p>Configura las políticas de seguridad del sistema</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key"></i>
                                Intentos máximos de login
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="max_attempts" 
                                   name="security[max_attempts]" 
                                   value="{{ $settings['max_attempts'] ?? 5 }}"
                                   min="1" 
                                   max="10">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-hourglass-half"></i>
                                Tiempo de bloqueo (minutos)
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="lockout_time" 
                                   name="security[lockout_time]" 
                                   value="{{ $settings['lockout_time'] ?? 15 }}"
                                   min="1" 
                                   max="60">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-hourglass-end"></i>
                            Tiempo de inactividad (minutos)
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="session_timeout" 
                               name="security[session_timeout]" 
                               value="{{ $settings['session_timeout'] ?? 30 }}"
                               min="5" 
                               max="120">
                    </div>

                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" id="two_factor_auth" name="security[two_factor_auth]" value="1" {{ ($settings['two_factor_auth'] ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-label">
                            <i class="fas fa-mobile-alt"></i>
                            <div>
                                <strong>Autenticación de dos factores (2FA)</strong>
                                <p>Requerir código de verificación adicional al iniciar sesión</p>
                            </div>
                        </div>
                    </div>

                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" id="session_timeout_enabled" name="security[session_timeout_enabled]" value="1" {{ ($settings['session_timeout_enabled'] ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-label">
                            <i class="fas fa-power-off"></i>
                            <div>
                                <strong>Cierre automático por inactividad</strong>
                                <p>Finalizar sesión automáticamente después del tiempo especificado</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Notificaciones -->
            <div class="settings-card card-notifications">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="header-text">
                        <h3>Notificaciones</h3>
                        <p>Configura las alertas y comunicaciones del sistema</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" id="email_notifications" name="notifications[email_notifications]" value="1" {{ ($settings['email_notifications'] ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-label">
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Notificaciones por correo electrónico</strong>
                                <p>Enviar alertas y reportes vía email</p>
                            </div>
                        </div>
                    </div>

                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" id="access_alerts" name="notifications[access_alerts]" value="1" {{ ($settings['access_alerts'] ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-label">
                            <i class="fas fa-exclamation-triangle"></i>
                            <div>
                                <strong>Alertas de accesos sospechosos</strong>
                                <p>Notificar cuando se detecten patrones inusuales de acceso</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-envelope-open-text"></i>
                            Correos para notificaciones
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="notification_emails" 
                               name="notifications[notification_emails]" 
                               value="{{ $settings['notification_emails'] ?? '' }}"
                               placeholder="correo1@empresa.com, correo2@empresa.com">
                        <small class="form-hint">Separar múltiples correos con comas</small>
                    </div>
                </div>
            </div>

            <!-- Tarjeta: Respaldos -->
            <div class="settings-card card-backup">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="header-text">
                        <h3>Respaldos</h3>
                        <p>Configura la copia de seguridad automática</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="toggle-group">
                        <label class="toggle-switch">
                            <input type="checkbox" id="auto_backup" name="backup[auto_backup]" value="1" {{ ($settings['auto_backup'] ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                        <div class="toggle-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>
                                <strong>Respaldo automático</strong>
                                <p>Realizar copias de seguridad programadas</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-week"></i>
                                Frecuencia
                            </label>
                            <select class="form-control" id="backup_frequency" name="backup[backup_frequency]">
                                <option value="daily" {{ ($settings['backup_frequency'] ?? '') == 'daily' ? 'selected' : '' }}>Diario</option>
                                <option value="weekly" {{ ($settings['backup_frequency'] ?? 'weekly') == 'weekly' ? 'selected' : '' }}>Semanal</option>
                                <option value="monthly" {{ ($settings['backup_frequency'] ?? '') == 'monthly' ? 'selected' : '' }}>Mensual</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clock"></i>
                                Hora
                            </label>
                            <input type="time" 
                                   class="form-control" 
                                   id="backup_time" 
                                   name="backup[backup_time]" 
                                   value="{{ $settings['backup_time'] ?? '02:00' }}">
                        </div>
                    </div>

                    <button type="button" class="btn-backup" id="manualBackupBtn">
                        <i class="fas fa-database"></i>
                        <span>Realizar Respaldo Ahora</span>
                    </button>
                </div>
            </div>

            <!-- Tarjeta: Integraciones -->
            <div class="settings-card card-integrations">
                <div class="card-header">
                    <div class="header-icon">
                        <i class="fas fa-plug"></i>
                    </div>
                    <div class="header-text">
                        <h3>Integraciones</h3>
                        <p>Conecta el sistema con servicios externos</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="integration-item">
                        <div class="integration-info">
                            <i class="fab fa-whatsapp"></i>
                            <div>
                                <strong>WhatsApp Business API</strong>
                                <p>Envío de notificaciones y alertas vía WhatsApp</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="whatsapp_integration" name="integrations[whatsapp]" value="1" {{ ($settings['whatsapp_integration'] ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="integration-item">
                        <div class="integration-info">
                            <i class="fas fa-id-card"></i>
                            <div>
                                <strong>Lectores NFC</strong>
                                <p>Configuración de lectores de tarjetas y credenciales</p>
                            </div>
                        </div>
                        <button type="button" class="btn-config" data-integration="nfc">
                            <i class="fas fa-cog"></i>
                            <span>Configurar</span>
                        </button>
                    </div>

                    <div class="integration-item">
                        <div class="integration-info">
                            <i class="fas fa-print"></i>
                            <div>
                                <strong>Impresoras de Tickets</strong>
                                <p>Configuración de impresión de tickets y reportes</p>
                            </div>
                        </div>
                        <button type="button" class="btn-config" data-integration="printer">
                            <i class="fas fa-cog"></i>
                            <span>Configurar</span>
                        </button>
                    </div>

                    <div class="integration-item">
                        <div class="integration-info">
                            <i class="fas fa-chart-line"></i>
                            <div>
                                <strong>API de Reportes</strong>
                                <p>Integración con sistemas externos de reporting</p>
                            </div>
                        </div>
                        <button type="button" class="btn-config" data-integration="api">
                            <i class="fas fa-cog"></i>
                            <span>Configurar</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="settings-footer">
            <button type="button" class="btn-reset" id="resetBtn">
                <i class="fas fa-undo-alt"></i>
                Restablecer Valores
            </button>
            <button type="submit" class="btn-save" id="saveBtn">
                <i class="fas fa-save"></i>
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<!-- Modal de Configuración de Integración -->
<div id="configModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Configuración</h3>
            <button class="modal-close">&times;</button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Contenido dinámico -->
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" id="modalCancel">Cancelar</button>
            <button class="btn-primary" id="modalSave">Guardar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/settings.js') }}"></script>
@endpush