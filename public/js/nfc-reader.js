{{-- public/js/admin/nfc-reader.js --}}
let currentConnectionType = 'network';
let isConnected = false;
let currentDeviceId = null;

// ============================================
// CONFIGURACIÓN INICIAL
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    selectConnectionType('network');
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

    if (type === 'network') {
        document.getElementById('networkConfig').style.display = 'block';
    } else if (type === 'wifi') {
        document.getElementById('wifiConfig').style.display = 'block';
        loadPairedDevices();
    }
}

// ============================================
// ESCANEO DE DISPOSITIVOS WIFI
// ============================================

async function scanWifiDevices() {
    const devicesList = document.getElementById('wifiDevicesList');
    const scanningStatus = document.getElementById('scanningStatus');
    const scanBtn = document.getElementById('scanWifiBtn');

    scanningStatus.style.display = 'flex';
    devicesList.innerHTML = '<div class="loading-devices">Buscando dispositivos...</div>';
    scanBtn.disabled = true;
    scanBtn.innerHTML = '<div class="spinner-small"></div> Escaneando...';

    try {
        const response = await fetch('/admin/nfc-cards/reader/scan-wifi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        scanningStatus.style.display = 'none';

        if (data.success && data.devices && data.devices.length > 0) {
            devicesList.innerHTML = data.devices.map(device => `
                <div class="device-item" onclick="selectWifiDevice('${device.ip}', '${device.port || 8080}', '${device.name || ''}')">
                    <div class="device-info">
                        <i class="fas ${device.type === 'reader' ? 'fa-microchip' : 'fa-wifi'}"></i>
                        <div>
                            <strong>${device.name || 'Lector NFC'}</strong>
                            <small>IP: ${device.ip}${device.port ? ':' + device.port : ''}</small>
                            <small class="signal-strength">Señal: ${device.signal || 'N/A'}%</small>
                        </div>
                    </div>
                    <button class="btn-select-device" onclick="event.stopPropagation(); selectWifiDevice('${device.ip}', '${device.port || 8080}', '${device.name || ''}')">
                        <i class="fas fa-arrow-right"></i> Seleccionar
                    </button>
                </div>
            `).join('');
        } else {
            devicesList.innerHTML = '<div class="empty-devices">No se encontraron dispositivos WiFi. Verifica que el lector esté encendido y en la misma red.</div>';
        }
    } catch (error) {
        scanningStatus.style.display = 'none';
        devicesList.innerHTML = '<div class="empty-devices error">Error al escanear dispositivos. Verifica la conexión de red.</div>';
        console.error('Error:', error);
    } finally {
        scanBtn.disabled = false;
        scanBtn.innerHTML = '<i class="fas fa-sync-alt"></i> Escanear dispositivos';
    }
}

function selectWifiDevice(ip, port, name) {
    document.getElementById('wifiIpAddress').value = ip;
    document.getElementById('wifiPort').value = port;
    
    if (name) {
        document.getElementById('wifiSsid').value = name;
    }
    
    showToast(`Dispositivo seleccionado: ${ip}:${port}`, 'success');
    
    // Resaltar el campo de IP
    const ipField = document.getElementById('wifiIpAddress');
    ipField.style.borderColor = '#10b981';
    setTimeout(() => {
        ipField.style.borderColor = '#e5e7eb';
    }, 2000);
}

// ============================================
// CONEXIÓN POR IP (Red Ethernet)
// ============================================

async function testNetworkConnection() {
    const ipAddress = document.getElementById('ipAddress').value;
    const port = document.getElementById('portNumber').value;
    const protocol = document.getElementById('protocol').value;
    const username = document.getElementById('networkUsername').value;
    const password = document.getElementById('networkPassword').value;
    const statusDiv = document.getElementById('networkConnectionStatus');
    const readerStatus = document.getElementById('readerStatusNetwork');
    const testBtn = document.getElementById('testNetworkBtn');
    const statusLed = document.querySelector('#networkConfig .status-led');

    if (!ipAddress) {
        statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Ingresa una dirección IP</span></div>`;
        return;
    }

    readerStatus.className = 'status-badge testing';
    readerStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
    if (statusLed) statusLed.style.background = '#f59e0b';
    testBtn.disabled = true;
    testBtn.innerHTML = '<div class="spinner"></div> Probando...';

    statusDiv.innerHTML = `<div class="status-message info"><div class="spinner"></div><span>Conectando a ${ipAddress}:${port}...</span></div>`;

    try {
        const response = await fetch('/admin/nfc-cards/reader/test-network', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ip_address: ipAddress,
                port: port,
                protocol: protocol,
                username: username,
                password: password
            })
        });

        const data = await response.json();

        if (data.success) {
            isConnected = true;
            currentDeviceId = data.device?.id || ipAddress;
            readerStatus.className = 'status-badge connected';
            readerStatus.innerHTML = '<i class="fas fa-check-circle"></i> Conectado';
            if (statusLed) statusLed.classList.add('connected');
            statusDiv.innerHTML = `
                <div class="status-message success">
                    <i class="fas fa-check-circle"></i>
                    <span>Conectado a ${ipAddress}:${port}</span>
                </div>
                <div class="connection-details">
                    <strong>Información del dispositivo:</strong><br>
                    Modelo: ${data.model || 'Lector NFC'}<br>
                    Firmware: ${data.firmware || 'v1.0.0'}<br>
                    Estado: ${data.status || 'Online'}<br>
                    Respuesta: ${data.response_time || '< 100ms'}
                </div>
            `;
        } else {
            isConnected = false;
            readerStatus.className = 'status-badge disconnected';
            readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
            if (statusLed) statusLed.classList.remove('connected');
            statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error de conexión'}</span></div>`;
        }
    } catch (error) {
        isConnected = false;
        readerStatus.className = 'status-badge disconnected';
        readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
        if (statusLed) statusLed.classList.remove('connected');
        statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
    } finally {
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="fas fa-plug"></i> Probar conexión';
        if (statusLed && !isConnected) statusLed.style.background = '#d1d5db';
    }
}

// ============================================
// CONEXIÓN WIFI
// ============================================

async function testWifiConnection() {
    const ssid = document.getElementById('wifiSsid').value;
    const ipAddress = document.getElementById('wifiIpAddress').value;
    const port = document.getElementById('wifiPort').value;
    const protocol = document.getElementById('wifiProtocol').value;
    const username = document.getElementById('wifiUsername').value;
    const password = document.getElementById('wifiPassword').value;
    const statusDiv = document.getElementById('wifiConnectionStatus');
    const readerStatus = document.getElementById('readerStatusWifi');
    const testBtn = document.getElementById('testWifiBtn');
    const statusLed = document.querySelector('#wifiConfig .status-led');

    if (!ipAddress) {
        statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Ingresa la IP del dispositivo o escanea uno</span></div>`;
        return;
    }

    readerStatus.className = 'status-badge testing';
    readerStatus.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Probando...';
    if (statusLed) statusLed.style.background = '#f59e0b';
    testBtn.disabled = true;
    testBtn.innerHTML = '<div class="spinner"></div> Probando...';

    statusDiv.innerHTML = `<div class="status-message info"><div class="spinner"></div><span>Conectando a dispositivo WiFi ${ipAddress}:${port}...</span></div>`;

    try {
        const response = await fetch('/admin/nfc-cards/reader/test-wifi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ssid: ssid,
                ip_address: ipAddress,
                port: port,
                protocol: protocol,
                username: username,
                password: password
            })
        });

        const data = await response.json();

        if (data.success) {
            isConnected = true;
            currentDeviceId = data.device?.id || ipAddress;
            readerStatus.className = 'status-badge connected';
            readerStatus.innerHTML = '<i class="fas fa-check-circle"></i> Conectado';
            if (statusLed) statusLed.classList.add('connected');
            statusDiv.innerHTML = `
                <div class="status-message success">
                    <i class="fas fa-check-circle"></i>
                    <span>Conectado a ${ipAddress}:${port}</span>
                </div>
                <div class="connection-details">
                    <strong>Información del dispositivo:</strong><br>
                    SSID: ${ssid || 'WiFi'}<br>
                    Modelo: ${data.model || 'Lector NFC WiFi'}<br>
                    Señal: ${data.signal || 'Buena'}<br>
                    Estado: ${data.status || 'Online'}
                </div>
            `;
            
            // Guardar dispositivo emparejado
            await savePairedDevice({
                id: currentDeviceId,
                name: ssid || 'Lector WiFi',
                ip: ipAddress,
                port: port,
                type: 'wifi'
            });
        } else {
            isConnected = false;
            readerStatus.className = 'status-badge disconnected';
            readerStatus.innerHTML = '<i class="fas fa-wifi"></i> Desconectado';
            if (statusLed) statusLed.classList.remove('connected');
            statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error de conexión'}</span></div>`;
        }
    } catch (error) {
        isConnected = false;
        readerStatus.className = 'status-badge disconnected';
        readerStatus.innerHTML = '<i class="fas fa-wifi"></i> Desconectado';
        if (statusLed) statusLed.classList.remove('connected');
        statusDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
    } finally {
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="fas fa-plug"></i> Probar conexión';
        if (statusLed && !isConnected) statusLed.style.background = '#d1d5db';
    }
}

// ============================================
// DISPOSITIVOS EMPAREJADOS
// ============================================

async function loadPairedDevices() {
    try {
        const response = await fetch('/admin/nfc-cards/reader/paired-devices');
        const data = await response.json();

        const pairedContainer = document.getElementById('pairedDevicesList');
        if (pairedContainer) {
            if (data.devices && data.devices.length > 0) {
                pairedContainer.innerHTML = data.devices.map(device => `
                    <div class="paired-device-item">
                        <div class="device-info">
                            <i class="fas ${device.type === 'wifi' ? 'fa-wifi' : 'fa-network-wired'}"></i>
                            <div>
                                <strong>${device.name || 'Dispositivo'}</strong>
                                <small>IP: ${device.ip}${device.port ? ':' + device.port : ''}</small>
                                <small>${device.paired_at ? 'Emparejado: ' + new Date(device.paired_at).toLocaleString() : ''}</small>
                            </div>
                        </div>
                        <button class="btn-connect-sm" onclick="connectToPairedDevice('${device.id}', '${device.ip}', '${device.port || 8080}')">
                            <i class="fas fa-link"></i> Conectar
                        </button>
                        <button class="btn-remove-sm" onclick="unpairDevice('${device.id}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `).join('');
            } else {
                pairedContainer.innerHTML = '<div class="empty-paired">No hay dispositivos emparejados</div>';
            }
        }
    } catch (error) {
        console.error('Error al cargar dispositivos emparejados:', error);
    }
}

async function savePairedDevice(device) {
    try {
        await fetch('/admin/nfc-cards/reader/save-paired-device', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(device)
        });
        loadPairedDevices();
    } catch (error) {
        console.error('Error al guardar dispositivo:', error);
    }
}

async function connectToPairedDevice(deviceId, ip, port) {
    if (currentConnectionType === 'wifi') {
        document.getElementById('wifiIpAddress').value = ip;
        document.getElementById('wifiPort').value = port;
        await testWifiConnection();
    } else {
        document.getElementById('ipAddress').value = ip;
        document.getElementById('portNumber').value = port;
        await testNetworkConnection();
    }
}

async function unpairDevice(deviceId) {
    if (!confirm('¿Desemparejar este dispositivo?')) return;

    try {
        const response = await fetch('/admin/nfc-cards/reader/unpair-device', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ device_id: deviceId })
        });

        const data = await response.json();

        if (data.success) {
            showToast('Dispositivo desemparejado', 'success');
            if (currentDeviceId === deviceId) {
                isConnected = false;
                currentDeviceId = null;
                if (currentConnectionType === 'wifi') {
                    const readerStatus = document.getElementById('readerStatusWifi');
                    readerStatus.className = 'status-badge disconnected';
                    readerStatus.innerHTML = '<i class="fas fa-wifi"></i> Desconectado';
                } else {
                    const readerStatus = document.getElementById('readerStatusNetwork');
                    readerStatus.className = 'status-badge disconnected';
                    readerStatus.innerHTML = '<i class="fas fa-plug"></i> Desconectado';
                }
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
    if (!isConnected) {
        document.getElementById('readResult').innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Primero debes conectar el lector NFC</span></div>`;
        return;
    }

    const readBtn = document.getElementById('readTestBtn');
    const testCardCode = document.getElementById('testCardCode');
    const resultDiv = document.getElementById('readResult');

    readBtn.disabled = true;
    readBtn.innerHTML = '<div class="spinner"></div> Leyendo...';

    resultDiv.innerHTML = `<div class="status-message info"><div class="spinner"></div><span>Acerca una tarjeta al lector...</span></div>`;

    try {
        let requestBody = { device_id: currentDeviceId };

        if (currentConnectionType === 'network') {
            requestBody.ip_address = document.getElementById('ipAddress').value;
            requestBody.port = document.getElementById('portNumber').value;
            requestBody.protocol = document.getElementById('protocol').value;
        } else if (currentConnectionType === 'wifi') {
            requestBody.ip_address = document.getElementById('wifiIpAddress').value;
            requestBody.port = document.getElementById('wifiPort').value;
            requestBody.protocol = document.getElementById('wifiProtocol').value;
            requestBody.device_type = 'wifi';
        }

        const response = await fetch('/admin/nfc-cards/reader/read-card', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
                <div class="action-buttons-group" style="margin-top: 1rem; padding: 0;">
                    <button type="button" class="btn-reader-secondary" onclick="registerCard('${data.card_code}', '${data.card_uid || ''}')">
                        <i class="fas fa-save"></i> Registrar esta tarjeta
                    </button>
                </div>
            `;
        } else {
            resultDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>${data.message || 'Error al leer la tarjeta'}</span></div>`;
        }
    } catch (error) {
        resultDiv.innerHTML = `<div class="status-message error"><i class="fas fa-exclamation-circle"></i><span>Error: ${error.message}</span></div>`;
    } finally {
        readBtn.disabled = false;
        readBtn.innerHTML = '<i class="fas fa-rss"></i> Leer tarjeta';
    }
}

function registerCard(code, uid) {
    let url = `/admin/nfc-cards/create?card_code=${encodeURIComponent(code)}`;
    if (uid) url += `&card_uid=${encodeURIComponent(uid)}`;
    window.location.href = url;
}

// ============================================
// CONFIGURACIÓN
// ============================================

async function saveConfiguration() {
    const config = {
        connection_type: currentConnectionType
    };

    if (currentConnectionType === 'network') {
        config.ip_address = document.getElementById('ipAddress').value;
        config.port = document.getElementById('portNumber').value;
        config.protocol = document.getElementById('protocol').value;
        config.username = document.getElementById('networkUsername').value;
        config.password = document.getElementById('networkPassword').value;
    } else if (currentConnectionType === 'wifi') {
        config.ssid = document.getElementById('wifiSsid').value;
        config.ip_address = document.getElementById('wifiIpAddress').value;
        config.port = document.getElementById('wifiPort').value;
        config.protocol = document.getElementById('wifiProtocol').value;
        config.username = document.getElementById('wifiUsername').value;
        config.password = document.getElementById('wifiPassword').value;
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
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
        
        // Cargar configuración de red
        if (config.ip_address && config.port) {
            document.getElementById('ipAddress').value = config.ip_address || '';
            document.getElementById('portNumber').value = config.port || '8080';
            document.getElementById('protocol').value = config.protocol || 'tcp';
            document.getElementById('networkUsername').value = config.username || '';
            document.getElementById('networkPassword').value = config.password || '';
        }
        
        // Cargar configuración WiFi
        if (config.wifi_ip_address) {
            document.getElementById('wifiSsid').value = config.ssid || '';
            document.getElementById('wifiIpAddress').value = config.wifi_ip_address || '';
            document.getElementById('wifiPort').value = config.wifi_port || '8080';
            document.getElementById('wifiProtocol').value = config.wifi_protocol || 'tcp';
            document.getElementById('wifiUsername').value = config.wifi_username || '';
            document.getElementById('wifiPassword').value = config.wifi_password || '';
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
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
    
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}