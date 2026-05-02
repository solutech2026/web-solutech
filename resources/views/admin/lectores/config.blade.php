@extends('layouts.admin')

@section('title', $isEdit ? 'Editar Lector' : 'Nuevo Lector')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nfc-reader-config.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="lectores-config-container">
    <!-- Hero Section -->
    <div class="hero-config">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="hero-text">
                <h1>{{ $isEdit ? 'Editar Lector' : 'Configurar Nuevo Lector' }}</h1>
                <p>{{ $isEdit ? 'Modifica los parámetros del lector NFC' : 'Configura un nuevo lector NFC por IP o WiFi' }}</p>
            </div>
        </div>
    </div>

    <div class="form-config-container">
        <form method="POST" action="{{ route('lectores.guardar', $isEdit ? $reader->id : 'nuevo') }}" id="lectorForm" enctype="multipart/form-data">
            @csrf

            <!-- Selector de Tipo de Lector -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="section-title">
                        <h3>Tipo de Conexión</h3>
                        <p>Selecciona el tipo de conexión del dispositivo</p>
                    </div>
                </div>

                <div class="type-cards">
                    <label class="type-card {{ (!$isEdit && old('type', 'network') == 'network') || ($isEdit && ($reader->type ?? 'network') == 'network') ? 'active' : '' }}">
                        <input type="radio" name="type" value="network" {{ (!$isEdit && old('type', 'network') == 'network') || ($isEdit && ($reader->type ?? 'network') == 'network') ? 'checked' : '' }} onchange="toggleType()">
                        <div class="card-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="card-info">
                            <h4>Conexión por IP</h4>
                            <p>Red Ethernet / Cable de red</p>
                        </div>
                        <div class="card-check">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </label>

                    <label class="type-card {{ $isEdit && ($reader->type ?? '') == 'wifi' ? 'active' : '' }}">
                        <input type="radio" name="type" value="wifi" {{ $isEdit && ($reader->type ?? '') == 'wifi' ? 'checked' : '' }} onchange="toggleType()">
                        <div class="card-icon">
                            <i class="fas fa-wifi"></i>
                        </div>
                        <div class="card-info">
                            <h4>Conexión WiFi</h4>
                            <p>Red inalámbrica / Wireless</p>
                        </div>
                        <div class="card-check">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Información Básica -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="section-title">
                        <h3>Información Básica</h3>
                        <p>Datos generales del dispositivo</p>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>
                            <i class="fas fa-tag"></i>
                            Nombre del Lector *
                        </label>
                        <input type="text" name="name" class="input-field" 
                               value="{{ old('name', $isEdit ? ($reader->name ?? '') : '') }}" 
                               placeholder="Ej: Lector Puerta Principal" required>
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Ubicación
                        </label>
                        <input type="text" name="ubicacion" class="input-field" 
                               value="{{ old('ubicacion', $isEdit ? ($reader->ubicacion ?? '') : '') }}" 
                               placeholder="Ej: Entrada principal, Oficina 101">
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-barcode"></i>
                            Código del Dispositivo
                        </label>
                        <input type="text" name="device_code" class="input-field" 
                               value="{{ old('device_code', $isEdit ? ($reader->device_code ?? '') : '') }}" 
                               placeholder="Ej: LECTOR-001">
                        <small class="help-text">Código único para identificar el dispositivo</small>
                    </div>

                    <div class="input-group">
                        <label>
                            <i class="fas fa-microchip"></i>
                            Número de Serie
                        </label>
                        <input type="text" name="serial_number" class="input-field" 
                               value="{{ old('serial_number', $isEdit ? ($reader->serial_number ?? '') : '') }}" 
                               placeholder="Número de serie del dispositivo">
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CONFIGURACIÓN RED (IP) -->
            <!-- ========================================== -->
            <div id="networkConfig" class="form-section {{ (!$isEdit && old('type', 'network') == 'network') || ($isEdit && ($reader->type ?? 'network') == 'network') ? '' : 'hidden' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-network-wired"></i>
                    </div>
                    <div class="section-title">
                        <h3>Configuración de Red (IP)</h3>
                        <p>Parámetros de conexión por cable de red</p>
                    </div>
                </div>

                <div class="config-card">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>
                                <i class="fas fa-ethernet"></i>
                                Dirección IP *
                            </label>
                            <input type="text" name="ip_address" class="input-field" 
                                   value="{{ old('ip_address', $isEdit ? ($reader->ip_address ?? '') : '') }}" 
                                   placeholder="192.168.1.100" required>
                            <small class="help-text">Ej: 192.168.1.100</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-plug"></i>
                                Puerto *
                            </label>
                            <input type="number" name="port" class="input-field" 
                                   value="{{ old('port', $isEdit ? ($reader->port ?? '8080') : '8080') }}" 
                                   placeholder="8080" required>
                            <small class="help-text">Puerto de comunicación (Ej: 8080, 5000)</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-code-branch"></i>
                                Protocolo
                            </label>
                            <select name="protocol" class="input-field">
                                <option value="tcp" {{ (old('protocol', $isEdit ? ($reader->protocol ?? 'tcp') : 'tcp') == 'tcp') ? 'selected' : '' }}>TCP</option>
                                <option value="udp" {{ (old('protocol', $isEdit ? ($reader->protocol ?? '') : '') == 'udp') ? 'selected' : '' }}>UDP</option>
                                <option value="http" {{ (old('protocol', $isEdit ? ($reader->protocol ?? '') : '') == 'http') ? 'selected' : '' }}>HTTP</option>
                                <option value="https" {{ (old('protocol', $isEdit ? ($reader->protocol ?? '') : '') == 'https') ? 'selected' : '' }}>HTTPS</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-microchip"></i>
                                MAC Address
                            </label>
                            <input type="text" name="mac_address" class="input-field" 
                                   value="{{ old('mac_address', $isEdit ? ($reader->mac_address ?? '') : '') }}" 
                                   placeholder="00:11:22:33:44:55">
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-user"></i>
                                Usuario
                            </label>
                            <input type="text" name="username" class="input-field" 
                                   value="{{ old('username', $isEdit ? ($reader->username ?? '') : '') }}" 
                                   placeholder="Usuario para autenticación">
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-lock"></i>
                                Contraseña
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="password" class="input-field" 
                                       placeholder="Contraseña">
                                <button type="button" class="btn-toggle-password" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @if($isEdit && isset($reader->password) && $reader->password)
                                <small class="help-text success">
                                    <i class="fas fa-check-circle"></i> Contraseña configurada
                                </small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- CONFIGURACIÓN WIFI -->
            <!-- ========================================== -->
            <div id="wifiConfig" class="form-section {{ $isEdit && ($reader->type ?? '') == 'wifi' ? '' : 'hidden' }}">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div class="section-title">
                        <h3>Configuración WiFi</h3>
                        <p>Datos de conexión inalámbrica</p>
                    </div>
                </div>

                <div class="config-card">
                    <div class="form-grid">
                        <div class="input-group full-width">
                            <label>
                                <i class="fas fa-signal"></i>
                                SSID (Nombre de red) *
                            </label>
                            <input type="text" name="ssid" class="input-field" 
                                   value="{{ old('ssid', $isEdit ? ($reader->ssid ?? '') : '') }}" 
                                   placeholder="Nombre de la red WiFi" required>
                            <small class="help-text">Nombre de la red a la que se conectará el lector</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-lock"></i>
                                Contraseña WiFi *
                            </label>
                            <div class="password-wrapper">
                                <input type="password" name="wifi_password" class="input-field" 
                                       placeholder="Contraseña de la red WiFi">
                                <button type="button" class="btn-toggle-password" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="help-text">Contraseña para conectar a la red</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-ethernet"></i>
                                Dirección IP (estática)
                            </label>
                            <input type="text" name="wifi_ip_address" class="input-field" 
                                   value="{{ old('wifi_ip_address', $isEdit ? ($reader->wifi_ip_address ?? '') : '') }}" 
                                   placeholder="192.168.1.100">
                            <small class="help-text">Dejar en blanco para DHCP</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-plug"></i>
                                Puerto *
                            </label>
                            <input type="number" name="wifi_port" class="input-field" 
                                   value="{{ old('wifi_port', $isEdit ? ($reader->wifi_port ?? '8080') : '8080') }}" 
                                   placeholder="8080" required>
                            <small class="help-text">Puerto de comunicación</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-code-branch"></i>
                                Protocolo
                            </label>
                            <select name="wifi_protocol" class="input-field">
                                <option value="tcp" {{ (old('wifi_protocol', $isEdit ? ($reader->wifi_protocol ?? 'tcp') : 'tcp') == 'tcp') ? 'selected' : '' }}>TCP</option>
                                <option value="udp" {{ (old('wifi_protocol', $isEdit ? ($reader->wifi_protocol ?? '') : '') == 'udp') ? 'selected' : '' }}>UDP</option>
                                <option value="http" {{ (old('wifi_protocol', $isEdit ? ($reader->wifi_protocol ?? '') : '') == 'http') ? 'selected' : '' }}>HTTP</option>
                                <option value="https" {{ (old('wifi_protocol', $isEdit ? ($reader->wifi_protocol ?? '') : '') == 'https') ? 'selected' : '' }}>HTTPS</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-shield-alt"></i>
                                Encriptación
                            </label>
                            <select name="encryption" class="input-field">
                                <option value="wpa2" {{ (old('encryption', $isEdit ? ($reader->encryption ?? 'wpa2') : 'wpa2') == 'wpa2') ? 'selected' : '' }}>WPA2 (Recomendado)</option>
                                <option value="wpa3" {{ (old('encryption', $isEdit ? ($reader->encryption ?? '') : '') == 'wpa3') ? 'selected' : '' }}>WPA3</option>
                                <option value="wep" {{ (old('encryption', $isEdit ? ($reader->encryption ?? '') : '') == 'wep') ? 'selected' : '' }}>WEP</option>
                                <option value="open" {{ (old('encryption', $isEdit ? ($reader->encryption ?? '') : '') == 'open') ? 'selected' : '' }}>Abierta (Sin encriptación)</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-microchip"></i>
                                MAC Address
                            </label>
                            <input type="text" name="wifi_mac_address" class="input-field" 
                                   value="{{ old('wifi_mac_address', $isEdit ? ($reader->wifi_mac_address ?? '') : '') }}" 
                                   placeholder="00:11:22:33:44:55">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración Avanzada -->
            <div class="form-section">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div class="section-title">
                        <h3>Configuración Avanzada</h3>
                        <p>Parámetros opcionales de funcionamiento</p>
                    </div>
                </div>

                <div class="config-card">
                    <div class="form-grid">
                        <div class="input-group">
                            <label>
                                <i class="fas fa-clock"></i>
                                Timeout (segundos)
                            </label>
                            <input type="number" name="timeout" class="input-field" 
                                   value="{{ old('timeout', $isEdit ? ($reader->timeout ?? '30') : '30') }}" 
                                   placeholder="30">
                            <small class="help-text">Tiempo máximo de espera para respuesta</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-sync-alt"></i>
                                Intervalo de Reintento (ms)
                            </label>
                            <input type="number" name="retry_interval" class="input-field" 
                                   value="{{ old('retry_interval', $isEdit ? ($reader->retry_interval ?? '5000') : '5000') }}" 
                                   placeholder="5000">
                            <small class="help-text">Milisegundos entre reintentos de conexión</small>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-bell"></i>
                                Alertas de Desconexión
                            </label>
                            <select name="alert_on_disconnect" class="input-field">
                                <option value="1" {{ (old('alert_on_disconnect', $isEdit ? ($reader->alert_on_disconnect ?? '1') : '1') == '1') ? 'selected' : '' }}>Sí, enviar alerta</option>
                                <option value="0" {{ (old('alert_on_disconnect', $isEdit ? ($reader->alert_on_disconnect ?? '') : '') == '0') ? 'selected' : '' }}>No, solo registrar</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label>
                                <i class="fas fa-check-circle"></i>
                                Estado del Lector
                            </label>
                            <div class="toggle-container">
                                <label class="toggle">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ (old('is_active', $isEdit ? ($reader->is_active ?? true) : true)) ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                    <span class="toggle-label">Activo</span>
                                </label>
                            </div>
                            <small class="help-text">Si está inactivo, el lector no procesará peticiones</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-actions">
                <a href="{{ route('lectores.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> {{ $isEdit ? 'Actualizar Lector' : 'Guardar Lector' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function() {
    toggleType();
    
    // Depuración del formulario
    const form = document.getElementById('lectorForm');
    const submitBtn = document.querySelector('.btn-submit');
    
    if (form) {
        console.log('Formulario encontrado');
        
        form.addEventListener('submit', function(e) {
            console.log('Evento submit disparado');
            console.log('Datos del formulario:', new FormData(form));
            
            // Verificar campos requeridos
            const requiredFields = form.querySelectorAll('[required]');
            let missingFields = [];
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    const label = field.closest('.input-group')?.querySelector('label')?.innerText || field.name;
                    missingFields.push(label);
                    field.classList.add('error');
                }
            });
            
            if (missingFields.length > 0) {
                e.preventDefault();
                alert('Por favor complete los siguientes campos requeridos:\n- ' + missingFields.join('\n- '));
                return false;
            }
        });
    } else {
        console.error('Formulario NO encontrado');
    }
});
function toggleType() {
    const networkConfig = document.getElementById('networkConfig');
    const wifiConfig = document.getElementById('wifiConfig');
    const selected = document.querySelector('input[name="type"]:checked');
    
    if (networkConfig) networkConfig.classList.add('hidden');
    if (wifiConfig) wifiConfig.classList.add('hidden');
    
    if (selected) {
        const type = selected.value;
        if (type === 'network' && networkConfig) {
            networkConfig.classList.remove('hidden');
        } else if (type === 'wifi' && wifiConfig) {
            wifiConfig.classList.remove('hidden');
        }
    }
    
    document.querySelectorAll('.type-card').forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            card.classList.add('active');
        } else {
            card.classList.remove('active');
        }
    });
}

function togglePassword(btn) {
    const passwordWrapper = btn.closest('.password-wrapper');
    const passwordInput = passwordWrapper.querySelector('input');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    btn.querySelector('i').classList.toggle('fa-eye');
    btn.querySelector('i').classList.toggle('fa-eye-slash');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleType();
});
</script>
@endpush