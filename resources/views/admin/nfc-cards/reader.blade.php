@extends('layouts.admin')

@section('title', 'Configurar Lector NFC')

@section('header', 'Configuración del Lector NFC')

@section('content')
    <div class="nfc-reader-container">
        <div class="reader-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="fas fa-rss"></i>
                </div>
                <div class="hero-text">
                    <h1>Configurar Lector NFC</h1>
                    <p>Configura y prueba la conexión con el lector de tarjetas NFC</p>
                </div>
            </div>
        </div>

        <div class="reader-form-card">
            <!-- Selector de tipo de conexión -->
            <div class="connection-type-selector">
                <div class="connection-option" data-type="wired" onclick="selectConnectionType('wired')">
                    <i class="fas fa-usb"></i>
                    <h4>Cableado (USB)</h4>
                    <p>Conexión directa por puerto USB</p>
                </div>
                <div class="connection-option" data-type="wireless" onclick="selectConnectionType('wireless')">
                    <i class="fas fa-wifi"></i>
                    <h4>Inalámbrico</h4>
                    <p>Teléfono Android con NFC</p>
                </div>
                <div class="connection-option" data-type="network" onclick="selectConnectionType('network')">
                    <i class="fas fa-network-wired"></i>
                    <h4>Red / IP</h4>
                    <p>Lector NFC por red Ethernet/WiFi</p>
                </div>
            </div>

            <!-- Configuración Cableada (USB) -->
            <div id="wiredConfig" class="config-card connection-config" style="display: none;">
                <div class="reader-status-card">
                    <span class="status-label">Estado del lector:</span>
                    <span id="readerStatusWired" class="status-badge disconnected">
                        <i class="fas fa-plug"></i> Desconectado
                    </span>
                </div>

                <div class="form-group-reader">
                    <label>Puerto COM / Dispositivo</label>
                    <div class="input-group-reader">
                        <select id="comPort" class="select-reader com-port-select">
                            <option value="">-- Seleccionar puerto --</option>
                            <option value="COM1">COM1</option>
                            <option value="COM2">COM2</option>
                            <option value="COM3">COM3</option>
                            <option value="COM4">COM4</option>
                            <option value="/dev/ttyUSB0">/dev/ttyUSB0 (Linux)</option>
                            <option value="/dev/ttyS0">/dev/ttyS0 (Linux)</option>
                        </select>
                        <button type="button" class="btn-reader-primary" onclick="testWiredConnection()" id="testWiredBtn">
                            <i class="fas fa-plug"></i> Probar conexión
                        </button>
                    </div>
                </div>

                <div class="form-group-reader">
                    <label>Velocidad de baudios</label>
                    <select id="baudRate" class="select-reader">
                        <option value="9600">9600</option>
                        <option value="19200">19200</option>
                        <option value="38400">38400</option>
                        <option value="115200" selected>115200</option>
                    </select>
                </div>

                <div id="wiredConnectionStatus"></div>
            </div>

            <!-- Configuración Inalámbrica (Teléfono Android) -->
            <div id="wirelessConfig" class="config-card connection-config" style="display: none;">
                <div class="reader-status-card">
                    <span class="status-label">Estado del lector:</span>
                    <span id="readerStatusWireless" class="status-badge disconnected">
                        <i class="fas fa-mobile-alt"></i> Desconectado
                    </span>
                </div>

                <div class="form-group-reader">
                    <label>Método de conexión</label>
                    <div class="wireless-methods">
                        <button type="button" class="method-btn active" data-method="qrcode"
                            onclick="selectWirelessMethod('qrcode')">
                            <i class="fas fa-qrcode"></i> QR Code
                        </button>
                        <button type="button" class="method-btn" data-method="pin" onclick="selectWirelessMethod('pin')">
                            <i class="fas fa-key"></i> Código PIN
                        </button>
                        <button type="button" class="method-btn" data-method="scan" onclick="selectWirelessMethod('scan')">
                            <i class="fas fa-bluetooth"></i> Escanear dispositivo
                        </button>
                    </div>
                </div>

                <!-- Método QR Code -->
                <div id="qrcodeMethod" class="wireless-method-content">
                    <div class="qr-container">
                        <div class="qr-code" id="qrCodeContainer">
                            <div class="qr-placeholder" id="qrPlaceholder">
                                <i class="fas fa-qrcode"></i>
                                <span>Generando código...</span>
                            </div>
                        </div>
                        <p class="qr-instructions">
                            <i class="fas fa-mobile-alt"></i>
                            Escanea este código QR con la aplicación móvil Solubase NFC
                        </p>
                        <button class="btn-secondary-sm" onclick="refreshQRCode()">
                            <i class="fas fa-sync-alt"></i> Regenerar QR
                        </button>
                    </div>
                </div>

                <!-- Método PIN -->
                <div id="pinMethod" class="wireless-method-content" style="display: none;">
                    <div class="pin-container">
                        <div class="pin-code">
                            <span id="pinCode" class="pin-display">******</span>
                            <button class="btn-secondary-sm" onclick="generateNewPin()">
                                <i class="fas fa-sync-alt"></i> Generar nuevo PIN
                            </button>
                        </div>
                        <p class="pin-instructions">
                            <i class="fas fa-mobile-alt"></i>
                            Ingresa este PIN en la aplicación móvil Solubase NFC para emparejar tu dispositivo
                        </p>
                        <div class="paired-devices" id="pairedDevicesList">
                            <h5>Dispositivos emparejados:</h5>
                            <div class="empty-paired">No hay dispositivos emparejados</div>
                        </div>
                    </div>
                </div>

                <!-- Método Escanear -->
                <div id="scanMethod" class="wireless-method-content" style="display: none;">
                    <div class="scan-container">
                        <div class="scanning-status" id="scanningStatus">
                            <div class="spinner"></div>
                            <span>Buscando dispositivos cercanos...</span>
                        </div>
                        <div class="devices-list" id="devicesList">
                            <div class="device-item">
                                <div class="device-info">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>
                                        <strong>Android-SG9</strong>
                                        <small>XX:XX:XX:XX:XX:XX</small>
                                    </div>
                                </div>
                                <button class="btn-connect-sm" onclick="pairDevice('android-sg9')">Conectar</button>
                            </div>
                            <div class="device-item">
                                <div class="device-info">
                                    <i class="fas fa-mobile-alt"></i>
                                    <div>
                                        <strong>Redmi-Note-11</strong>
                                        <small>XX:XX:XX:XX:YY:YY</small>
                                    </div>
                                </div>
                                <button class="btn-connect-sm" onclick="pairDevice('redmi-note-11')">Conectar</button>
                            </div>
                        </div>
                        <button class="btn-secondary-sm" onclick="scanDevices()">
                            <i class="fas fa-sync-alt"></i> Buscar de nuevo
                        </button>
                    </div>
                </div>

                <div id="wirelessConnectionStatus"></div>
            </div>

            <!-- Configuración por Red -->
            <div id="networkConfig" class="config-card connection-config" style="display: none;">
                <div class="reader-status-card">
                    <span class="status-label">Estado del lector:</span>
                    <span id="readerStatusNetwork" class="status-badge disconnected">
                        <i class="fas fa-network-wired"></i> Desconectado
                    </span>
                </div>

                <div class="form-group-reader">
                    <label>Dirección IP / Hostname</label>
                    <div class="input-group-reader">
                        <input type="text" id="ipAddress" class="input-reader" placeholder="Ej: 192.168.1.100">
                        <input type="number" id="portNumber" class="input-reader" placeholder="Puerto" value="8080"
                            style="width: 100px;">
                    </div>
                </div>

                <div class="form-group-reader">
                    <label>Protocolo</label>
                    <select id="protocol" class="select-reader">
                        <option value="tcp">TCP/IP</option>
                        <option value="udp">UDP</option>
                        <option value="http">HTTP</option>
                        <option value="websocket">WebSocket</option>
                    </select>
                </div>

                <div class="form-group-reader">
                    <label>API Key (opcional)</label>
                    <input type="text" id="apiKey" class="input-reader" placeholder="Clave de autenticación">
                </div>

                <div class="form-group-reader">
                    <button type="button" class="btn-reader-primary" onclick="testNetworkConnection()"
                        id="testNetworkBtn">
                        <i class="fas fa-plug"></i> Probar conexión
                    </button>
                </div>

                <div id="networkConnectionStatus"></div>
            </div>

            <!-- Prueba de lectura (común para todos) -->
            <div class="config-card">
                <div class="form-group-reader">
                    <label>Prueba de lectura</label>
                    <div class="input-group-reader">
                        <input type="text" id="testCardCode" class="input-reader"
                            placeholder="Código leído aparecerá aquí" readonly>
                        <button type="button" class="btn-reader-primary" onclick="startReading()" id="readTestBtn">
                            <i class="fas fa-rss"></i> Leer tarjeta
                        </button>
                    </div>
                </div>

                <div id="readResult"></div>
            </div>

            <div class="alert-reader info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Instrucciones para conexión inalámbrica:</strong><br>
                    1. Descarga la aplicación "Solubase NFC Reader" desde Google Play<br>
                    2. Escanea el código QR o ingresa el PIN generado<br>
                    3. Acerca la tarjeta NFC a tu teléfono Android<br>
                    4. La tarjeta se leerá automáticamente en el sistema
                </div>
            </div>

            <div class="action-buttons-group">
                <button type="button" class="btn-reader-secondary" onclick="saveConfiguration()">
                    <i class="fas fa-save"></i> Guardar configuración
                </button>
                <a href="{{ route('admin.nfc-cards.index') }}" class="btn-reader-outline">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/nfc-reader.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
    <script>
        let currentConnectionType = 'wired';
        let currentWirelessMethod = 'qrcode';
        let isConnected = false;
        let currentDeviceId = null;

        // ============================================
        // CONFIGURACIÓN INICIAL
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            selectConnectionType('wired');
            selectWirelessMethod('qrcode');
            loadSavedConfig();
            loadPairedDevices();
        });

        // ============================================
        // SELECCIÓN DE TIPO DE CONEXIÓN
        // ============================================

        function selectConnectionType(type) {
            currentConnectionType = type;

            document.querySelectorAll('.connection-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            document.querySelector(`.connection-option[data-type="${type}"]`).classList.add('selected');

            document.querySelectorAll('.connection-config').forEach(config => {
                config.style.display = 'none';
            });

            document.getElementById(`${type}Config`).style.display = 'block';

            if (type === 'wireless') {
                loadPairedDevices();
            }
        }

        function selectWirelessMethod(method) {
            currentWirelessMethod = method;

            document.querySelectorAll('.method-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`.method-btn[data-method="${method}"]`).classList.add('active');

            document.querySelectorAll('.wireless-method-content').forEach(content => {
                content.style.display = 'none';
            });

            document.getElementById(`${method}Method`).style.display = 'block';

            if (method === 'qrcode') {
                generateQRCode();
            } else if (method === 'pin') {
                generatePin();
            } else if (method === 'scan') {
                scanNetworkDevices();
            }
        }

        // ============================================
        // QR CODE
        // ============================================

        async function generateQRCode() {
            const qrContainer = document.getElementById('qrPlaceholder');
            qrContainer.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Generando código...</span>';

            try {
                const response = await fetch('/admin/nfc-cards/reader/generate-qr');
                const data = await response.json();

                if (data.success) {
                    // Aquí se mostraría el QR real usando una librería como QRCode.js
                    qrContainer.innerHTML = `
                    <i class="fas fa-qrcode"></i>
                    <span>QR generado</span>
                    <small style="font-size: 0.6rem;">ID: ${data.session_id.substring(0, 8)}</small>
                `;
                } else {
                    qrContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Error al generar QR</span>';
                }
            } catch (error) {
                qrContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Error de conexión</span>';
            }
        }

        function refreshQRCode() {
            generateQRCode();
            showToast('QR regenerado correctamente', 'success');
        }

        // ============================================
        // PIN CODE
        // ============================================

        async function generatePin() {
            const pinDisplay = document.getElementById('pinCode');
            // El PIN debería venir del backend
            const pin = Math.floor(100000 + Math.random() * 900000);
            pinDisplay.innerHTML = pin;
        }

        function generateNewPin() {
            generatePin();
            showToast('Nuevo PIN generado', 'success');
        }

        // ============================================
        // CONEXIÓN CABLEADA (USB)
        // ============================================

        async function testWiredConnection() {
            const comPort = document.getElementById('comPort').value;
            const baudRate = document.getElementById('baudRate').value;
            const statusDiv = document.getElementById('wiredConnectionStatus');
            const readerStatus = document.getElementById('readerStatusWired');
            const testBtn = document.getElementById('testWiredBtn');

            if (!comPort) {
                statusDiv.innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Selecciona un puerto COM</span></div>`;
                return;
            }

            readerStatus.className = 'status-badge testing';
            readerStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
            testBtn.disabled = true;
            testBtn.innerHTML = '<div class="spinner"></div> Probando...';

            statusDiv.innerHTML =
                `<div class="status-message info"><div class="spinner"></div><span>Conectando a ${comPort} a ${baudRate} baudios...</span></div>`;

            try {
                const response = await fetch('/admin/nfc-cards/reader/test-wired', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        com_port: comPort,
                        baud_rate: baudRate
                    })
                });

                const data = await response.json();

                if (data.success) {
                    isConnected = true;
                    readerStatus.className = 'status-badge connected';
                    readerStatus.innerHTML = '<i class="fas fa-check-circle"></i> Conectado';
                    statusDiv.innerHTML = `
                    <div class="status-message success">
                        <i class="fas fa-check-circle"></i>
                        <span>Conectado exitosamente a ${comPort}</span>
                    </div>
                    <div class="connection-details">
                        <strong>Información del lector:</strong><br>
                        Modelo: ${data.model || 'ACR122U'}<br>
                        Firmware: ${data.firmware || 'v2.0.1'}
                    </div>
                `;
                } else {
                    isConnected = false;
                    readerStatus.className = 'status-badge disconnected';
                    readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
                    statusDiv.innerHTML =
                        `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error de conexión'}</span></div>`;
                }
            } catch (error) {
                isConnected = false;
                readerStatus.className = 'status-badge disconnected';
                readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
                statusDiv.innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
            } finally {
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="fas fa-plug"></i> Probar conexión';
            }
        }

        // ============================================
        // CONEXIÓN POR RED
        // ============================================

        async function testNetworkConnection() {
            const ipAddress = document.getElementById('ipAddress').value;
            const port = document.getElementById('portNumber').value;
            const protocol = document.getElementById('protocol').value;
            const apiKey = document.getElementById('apiKey').value;
            const statusDiv = document.getElementById('networkConnectionStatus');
            const readerStatus = document.getElementById('readerStatusNetwork');
            const testBtn = document.getElementById('testNetworkBtn');

            if (!ipAddress) {
                statusDiv.innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Ingresa una dirección IP</span></div>`;
                return;
            }

            readerStatus.className = 'status-badge testing';
            readerStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
            testBtn.disabled = true;
            testBtn.innerHTML = '<div class="spinner"></div> Probando...';

            statusDiv.innerHTML =
                `<div class="status-message info"><div class="spinner"></div><span>Conectando a ${ipAddress}:${port}...</span></div>`;

            try {
                const response = await fetch('/admin/nfc-cards/reader/test-network', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        ip_address: ipAddress,
                        port: port,
                        protocol: protocol,
                        api_key: apiKey
                    })
                });

                const data = await response.json();

                if (data.success) {
                    isConnected = true;
                    currentDeviceId = data.device?.id || ipAddress;
                    readerStatus.className = 'status-badge connected';
                    readerStatus.innerHTML = '<i class="fas fa-check-circle"></i> Conectado';
                    statusDiv.innerHTML = `
                    <div class="status-message success">
                        <i class="fas fa-check-circle"></i>
                        <span>Conectado a ${ipAddress}:${port}</span>
                    </div>
                    <div class="connection-details">
                        <strong>Información del dispositivo:</strong><br>
                        Nombre: ${data.device?.name || 'Lector NFC'}<br>
                        Tipo: ${data.device?.type || 'Generic'}<br>
                        Respuesta: ${data.response_time || '< 100ms'}
                    </div>
                `;
                } else {
                    isConnected = false;
                    readerStatus.className = 'status-badge disconnected';
                    readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
                    statusDiv.innerHTML =
                        `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error de conexión'}</span></div>`;
                }
            } catch (error) {
                isConnected = false;
                readerStatus.className = 'status-badge disconnected';
                readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
                statusDiv.innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
            } finally {
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="fas fa-plug"></i> Probar conexión';
            }
        }

        // ============================================
        // ESCANEO DE DISPOSITIVOS DE RED
        // ============================================

        async function scanNetworkDevices() {
            const devicesList = document.getElementById('devicesList');
            const scanningStatus = document.getElementById('scanningStatus');

            scanningStatus.style.display = 'flex';
            devicesList.innerHTML =
                '<div class="text-center" style="padding: 1rem;">Buscando dispositivos en la red...</div>';

            try {
                const response = await fetch('/admin/nfc-cards/scan-network');
                const data = await response.json();

                scanningStatus.style.display = 'none';

                if (data.success && data.devices.length > 0) {
                    devicesList.innerHTML = data.devices.map(device => `
                    <div class="device-item">
                        <div class="device-info">
                            <i class="fas ${device.type === 'WebSocket' ? 'fa-wifi' : (device.is_mobile ? 'fa-mobile-alt' : 'fa-microchip')}"></i>
                            <div>
                                <strong>${device.name}</strong>
                                <small>${device.ip}${device.port ? ':' + device.port : ''}</small>
                                <div class="device-details">
                                    <span class="device-type">${device.type || 'NFC Reader'}</span>
                                    ${device.signal ? `<span class="device-signal signal-${device.signal > 70 ? 'good' : 'medium'}">📶 ${device.signal}%</span>` : ''}
                                </div>
                            </div>
                        </div>
                        <button class="btn-connect-sm" onclick="pairDevice('${device.id}', '${device.name}', '${device.ip}', '${device.port || 8080}')">
                            Conectar
                        </button>
                    </div>
                `).join('');
                } else {
                    devicesList.innerHTML =
                        '<div class="text-center" style="padding: 1rem;">No se encontraron dispositivos en la red</div>';
                }
            } catch (error) {
                scanningStatus.style.display = 'none';
                devicesList.innerHTML =
                    '<div class="text-center" style="padding: 1rem; color: #ef4444;">Error al escanear dispositivos</div>';
                console.error('Error:', error);
            }
        }

        // ============================================
        // DISPOSITIVOS EMPAREJADOS
        // ============================================

        async function loadPairedDevices() {
            try {
                const response = await fetch('/admin/nfc-cards/paired-devices');
                const data = await response.json();

                const pairedContainer = document.getElementById('pairedDevicesList');
                if (pairedContainer) {
                    if (data.devices && data.devices.length > 0) {
                        pairedContainer.innerHTML = `
                        <h5>Dispositivos emparejados:</h5>
                        ${data.devices.map(device => `
                                <div class="paired-device-item">
                                    <div class="device-info">
                                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                        <div>
                                            <strong>${device.name}</strong>
                                            <small>${device.ip || 'Conectado'} - ${device.paired_at ? new Date(device.paired_at).toLocaleString() : ''}</small>
                                        </div>
                                    </div>
                                    <button class="btn-remove-sm" onclick="unpairDevice('${device.id}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            `).join('')}
                    `;
                    } else {
                        pairedContainer.innerHTML = '<div class="empty-paired">No hay dispositivos emparejados</div>';
                    }
                }
            } catch (error) {
                console.error('Error al cargar dispositivos emparejados:', error);
            }
        }

        async function pairDevice(deviceId, deviceName, deviceIp, devicePort) {
            showToast('Conectando a ' + deviceName + '...', 'info');

            try {
                const response = await fetch('/admin/nfc-cards/pair-device', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        device_id: deviceId,
                        device_name: deviceName,
                        device_ip: deviceIp,
                        device_port: devicePort,
                        device_type: 'network'
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Dispositivo emparejado correctamente', 'success');
                    document.getElementById('readerStatusWireless').className = 'status-badge connected';
                    document.getElementById('readerStatusWireless').innerHTML =
                        '<i class="fas fa-check-circle"></i> Conectado a ' + deviceName;
                    isConnected = true;
                    currentDeviceId = deviceId;
                    loadPairedDevices();
                } else {
                    showToast('Error al emparejar dispositivo', 'error');
                }
            } catch (error) {
                showToast('Error de conexión', 'error');
            }
        }

        async function unpairDevice(deviceId) {
            if (!confirm('¿Desemparejar este dispositivo?')) return;

            try {
                const response = await fetch('/admin/nfc-cards/unpair-device', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        device_id: deviceId
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Dispositivo desemparejado', 'success');
                    if (currentDeviceId === deviceId) {
                        isConnected = false;
                        currentDeviceId = null;
                        document.getElementById('readerStatusWireless').className = 'status-badge disconnected';
                        document.getElementById('readerStatusWireless').innerHTML =
                            '<i class="fas fa-mobile-alt"></i> Desconectado';
                    }
                    loadPairedDevices();
                } else {
                    showToast('Error al desemparejar', 'error');
                }
            } catch (error) {
                showToast('Error de conexión', 'error');
            }
        }

        // ============================================
        // LECTURA DE TARJETAS
        // ============================================

        async function startReading() {
            if (!isConnected && currentConnectionType !== 'wireless') {
                document.getElementById('readResult').innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Primero debes conectar el lector NFC</span></div>`;
                return;
            }

            const readBtn = document.getElementById('readTestBtn');
            const testCardCode = document.getElementById('testCardCode');
            const resultDiv = document.getElementById('readResult');

            readBtn.disabled = true;
            readBtn.innerHTML = '<div class="spinner"></div> Leyendo...';

            resultDiv.innerHTML =
                `<div class="status-message info"><div class="spinner"></div><span>Acerca una tarjeta al lector...</span></div>`;

            try {
                let requestBody = {};

                if (currentConnectionType === 'network' && currentDeviceId) {
                    const ipAddress = document.getElementById('ipAddress').value;
                    const port = document.getElementById('portNumber').value;
                    requestBody = {
                        device_id: currentDeviceId,
                        device_ip: ipAddress,
                        device_port: port
                    };
                } else if (currentConnectionType === 'wired') {
                    requestBody = {
                        device_type: 'usb',
                        com_port: document.getElementById('comPort').value
                    };
                }

                const response = await fetch('/admin/nfc-cards/reader/read-card', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(requestBody)
                });

                const data = await response.json();

                if (data.success) {
                    testCardCode.value = data.card_code;
                    resultDiv.innerHTML = `
                    <div class="status-message success">
                        <i class="fas fa-check-circle"></i>
                        <span>Tarjeta leída: ${data.card_code}</span>
                    </div>
                    <div class="action-buttons-group" style="margin-top: 1rem;">
                        <button type="button" class="btn-reader-secondary" onclick="registerCard('${data.card_code}', '${data.card_uid || ''}')">
                            <i class="fas fa-save"></i> Registrar esta tarjeta
                        </button>
                    </div>
                `;
                } else {
                    resultDiv.innerHTML =
                        `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error al leer la tarjeta'}</span></div>`;
                }
            } catch (error) {
                resultDiv.innerHTML =
                    `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
            } finally {
                readBtn.disabled = false;
                readBtn.innerHTML = '<i class="fas fa-rss"></i> Leer tarjeta';
            }
        }

        function registerCard(code, uid) {
            let url = `{{ route('admin.nfc-cards.create') }}?card_code=${encodeURIComponent(code)}`;
            if (uid) {
                url += `&card_uid=${encodeURIComponent(uid)}`;
            }
            window.location.href = url;
        }

        // ============================================
        // CONFIGURACIÓN
        // ============================================

        async function saveConfiguration() {
            const config = {
                connection_type: currentConnectionType,
                wireless_method: currentWirelessMethod
            };

            if (currentConnectionType === 'wired') {
                config.com_port = document.getElementById('comPort').value;
                config.baud_rate = document.getElementById('baudRate').value;
            } else if (currentConnectionType === 'network') {
                config.ip_address = document.getElementById('ipAddress').value;
                config.port = document.getElementById('portNumber').value;
                config.protocol = document.getElementById('protocol').value;
                config.api_key = document.getElementById('apiKey').value;
            }

            const saveBtn = event.target;
            const originalText = saveBtn.innerHTML;
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<div class="spinner"></div> Guardando...';

            try {
                const response = await fetch('/admin/nfc-cards/reader/save-config', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(config)
                });

                const data = await response.json();

                if (data.success) {
                    showToast('Configuración guardada correctamente', 'success');
                } else {
                    showToast('Error al guardar configuración', 'error');
                }
            } catch (error) {
                showToast('Error de conexión al guardar', 'error');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            }
        }

        async function loadSavedConfig() {
            try {
                const response = await fetch('/admin/nfc-cards/reader/get-config');
                const config = await response.json();

                if (config.connection_type) {
                    selectConnectionType(config.connection_type);
                }
                if (config.com_port) {
                    document.getElementById('comPort').value = config.com_port;
                }
                if (config.baud_rate) {
                    document.getElementById('baudRate').value = config.baud_rate;
                }
                if (config.ip_address) {
                    document.getElementById('ipAddress').value = config.ip_address;
                }
                if (config.port) {
                    document.getElementById('portNumber').value = config.port;
                }
                if (config.protocol) {
                    document.getElementById('protocol').value = config.protocol;
                }
                if (config.api_key) {
                    document.getElementById('apiKey').value = config.api_key;
                }
                if (config.wireless_method) {
                    selectWirelessMethod(config.wireless_method);
                }
            } catch (error) {
                console.error('Error al cargar configuración:', error);
            }
        }

        // ============================================
        // UTILIDADES
        // ============================================

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `status-message ${type}`;
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.maxWidth = '300px';
            toast.style.animation = 'fadeIn 0.3s ease';
            toast.innerHTML =
                `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
@endpush
