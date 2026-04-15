@extends('layouts.admin')

@section('title', $isEdit ? 'Editar Lector' : 'Nuevo Lector')

@section('content')
<div class="config-container">
    <div class="config-card">
        <!-- Header -->
        <div class="config-header">
            <div class="header-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <h2>{{ $isEdit ? 'Editar Lector' : 'Configurar Nuevo Lector' }}</h2>
            <p>{{ $isEdit ? 'Modifica los parámetros del lector NFC' : 'Ingresa los datos del nuevo lector NFC' }}</p>
        </div>

        <!-- Formulario -->
        <div class="config-body">
            <form method="POST" action="{{ route('lectores.guardar', $isEdit ? $id : 'nuevo') }}">
                @csrf

                <!-- Información Básica -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Información Básica
                    </h3>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tag"></i> Nombre del Lector *
                        </label>
                        <input type="text" name="name" class="form-control" 
                               value="{{ $reader['name'] ?? '' }}" 
                               placeholder="Ej: Lector Puerta Principal" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Ubicación
                        </label>
                        <input type="text" name="ubicacion" class="form-control" 
                               value="{{ $reader['ubicacion'] ?? '' }}" 
                               placeholder="Ej: Entrada principal, Oficina 101">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-plug"></i> Tipo de Conexión *
                        </label>
                        <div class="radio-group">
                            <label class="radio-option">
                                <input type="radio" name="type" value="network" 
                                       id="typeNetwork" 
                                       {{ ($reader['type'] ?? 'network') == 'network' ? 'checked' : '' }} 
                                       onchange="toggleType()">
                                <span class="radio-custom"></span>
                                <i class="fas fa-network-wired"></i>
                                <span>Red (IP)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="type" value="wifi" 
                                       id="typeWifi" 
                                       {{ ($reader['type'] ?? '') == 'wifi' ? 'checked' : '' }} 
                                       onchange="toggleType()">
                                <span class="radio-custom"></span>
                                <i class="fas fa-wifi"></i>
                                <span>WiFi</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Configuración Red (IP) -->
                <div id="networkConfig" class="form-section {{ ($reader['type'] ?? 'network') == 'network' ? '' : 'd-none' }}">
                    <h3 class="section-title">
                        <i class="fas fa-network-wired"></i>
                        Configuración de Red
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-ethernet"></i> Dirección IP *
                            </label>
                            <input type="text" name="ip_address" class="form-control" 
                                   value="{{ $reader['ip_address'] ?? '' }}" 
                                   placeholder="192.168.1.100">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-plug"></i> Puerto *
                            </label>
                            <input type="number" name="port" class="form-control" 
                                   value="{{ $reader['port'] ?? '8080' }}" 
                                   placeholder="8080">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-code-branch"></i> Protocolo
                            </label>
                            <select name="protocol" class="form-select">
                                <option value="tcp" {{ ($reader['protocol'] ?? 'tcp') == 'tcp' ? 'selected' : '' }}>TCP</option>
                                <option value="udp" {{ ($reader['protocol'] ?? '') == 'udp' ? 'selected' : '' }}>UDP</option>
                                <option value="http" {{ ($reader['protocol'] ?? '') == 'http' ? 'selected' : '' }}>HTTP</option>
                                <option value="https" {{ ($reader['protocol'] ?? '') == 'https' ? 'selected' : '' }}>HTTPS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Usuario (opcional)
                            </label>
                            <input type="text" name="username" class="form-control" 
                                   value="{{ $reader['username'] ?? '' }}" 
                                   placeholder="Usuario">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Contraseña (opcional)
                        </label>
                        <input type="password" name="password" class="form-control" 
                               placeholder="Contraseña">
                        @if(isset($reader['password']) && $reader['password'])
                            <small class="form-text">
                                <i class="fas fa-info-circle"></i> Dejar en blanco para mantener la actual
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Configuración WiFi -->
                <div id="wifiConfig" class="form-section {{ ($reader['type'] ?? '') == 'wifi' ? '' : 'd-none' }}">
                    <h3 class="section-title">
                        <i class="fas fa-wifi"></i>
                        Configuración WiFi
                    </h3>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-signal"></i> SSID (Nombre de red) *
                        </label>
                        <input type="text" name="ssid" class="form-control" 
                               value="{{ $reader['ssid'] ?? '' }}" 
                               placeholder="MiRedWiFi">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-ethernet"></i> Dirección IP *
                            </label>
                            <input type="text" name="wifi_ip_address" class="form-control" 
                                   value="{{ $reader['wifi_ip_address'] ?? '' }}" 
                                   placeholder="192.168.1.100">
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-plug"></i> Puerto *
                            </label>
                            <input type="number" name="wifi_port" class="form-control" 
                                   value="{{ $reader['wifi_port'] ?? '8080' }}" 
                                   placeholder="8080">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-code-branch"></i> Protocolo
                            </label>
                            <select name="wifi_protocol" class="form-select">
                                <option value="tcp" {{ ($reader['wifi_protocol'] ?? 'tcp') == 'tcp' ? 'selected' : '' }}>TCP</option>
                                <option value="udp" {{ ($reader['wifi_protocol'] ?? '') == 'udp' ? 'selected' : '' }}>UDP</option>
                                <option value="http" {{ ($reader['wifi_protocol'] ?? '') == 'http' ? 'selected' : '' }}>HTTP</option>
                                <option value="https" {{ ($reader['wifi_protocol'] ?? '') == 'https' ? 'selected' : '' }}>HTTPS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Usuario (opcional)
                            </label>
                            <input type="text" name="wifi_username" class="form-control" 
                                   value="{{ $reader['wifi_username'] ?? '' }}" 
                                   placeholder="Usuario">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-lock"></i> Contraseña WiFi (opcional)
                        </label>
                        <input type="password" name="wifi_password" class="form-control" 
                               placeholder="Contraseña WiFi">
                        @if(isset($reader['wifi_password']) && $reader['wifi_password'])
                            <small class="form-text">
                                <i class="fas fa-info-circle"></i> Dejar en blanco para mantener la actual
                            </small>
                        @endif
                    </div>
                </div>

                <!-- Alertas -->
                <div class="alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Los campos marcados con * son obligatorios. Asegúrate de que los datos sean correctos para garantizar la conexión.</span>
                </div>

                <!-- Botones -->
                <div class="form-actions">
                    <a href="{{ route('lectores.index') }}" class="btn-cancel">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> {{ $isEdit ? 'Actualizar Lector' : 'Guardar Lector' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lectores-config.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
function toggleType() {
    const networkConfig = document.getElementById('networkConfig');
    const wifiConfig = document.getElementById('wifiConfig');
    const selected = document.querySelector('input[name="type"]:checked');
    
    if (selected) {
        const type = selected.value;
        if (type === 'network') {
            networkConfig.classList.remove('d-none');
            wifiConfig.classList.add('d-none');
        } else {
            networkConfig.classList.add('d-none');
            wifiConfig.classList.remove('d-none');
        }
    }
}

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    toggleType();
});
</script>
@endpush