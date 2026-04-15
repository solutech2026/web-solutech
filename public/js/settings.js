{{-- public/js/admin/settings.js --}}
class SettingsManager {
    constructor() {
        this.initializeElements();
        this.attachEvents();
        this.loadInitialData();
    }

    initializeElements() {
        // Formulario principal
        this.form = document.getElementById('settingsForm');
        
        // Formularios y campos - AHORA con los nombres correctos del formulario
        this.elements = {
            system_name: document.querySelector('[name="general[system_name]"]'),
            timezone: document.querySelector('[name="general[timezone]"]'),
            date_format: document.querySelector('[name="general[date_format]"]'),
            language: document.querySelector('[name="general[language]"]'),
            max_attempts: document.querySelector('[name="security[max_attempts]"]'),
            lockout_time: document.querySelector('[name="security[lockout_time]"]'),
            session_timeout: document.querySelector('[name="security[session_timeout]"]'),
            session_timeout_enabled: document.querySelector('[name="security[session_timeout_enabled]"]'),
            two_factor_auth: document.querySelector('[name="security[two_factor_auth]"]'),
            email_notifications: document.querySelector('[name="notifications[email_notifications]"]'),
            access_alerts: document.querySelector('[name="notifications[access_alerts]"]'),
            notification_emails: document.querySelector('[name="notifications[notification_emails]"]'),
            auto_backup: document.querySelector('[name="backup[auto_backup]"]'),
            backup_frequency: document.querySelector('[name="backup[backup_frequency]"]'),
            backup_time: document.querySelector('[name="backup[backup_time]"]'),
            whatsapp_integration: document.querySelector('[name="integrations[whatsapp]"]')
        };

        // Botones
        this.saveBtn = document.getElementById('saveBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.manualBackupBtn = document.getElementById('manualBackupBtn');

        // Modal
        this.modal = document.getElementById('configModal');
        this.modalClose = document.querySelector('.modal-close');
        this.modalCancel = document.getElementById('modalCancel');
        this.modalSave = document.getElementById('modalSave');
    }

    attachEvents() {
        // Evento submit del formulario
        if (this.form) {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSettings();
            });
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetSettings());
        }

        if (this.manualBackupBtn) {
            this.manualBackupBtn.addEventListener('click', () => this.performBackup());
        }

        // Botones de configuración de integraciones
        document.querySelectorAll('.btn-config').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const integration = btn.getAttribute('data-integration');
                this.openConfigModal(integration);
            });
        });

        // Modal events
        if (this.modalClose) {
            this.modalClose.addEventListener('click', () => this.closeModal());
        }

        if (this.modalCancel) {
            this.modalCancel.addEventListener('click', () => this.closeModal());
        }

        if (this.modalSave) {
            this.modalSave.addEventListener('click', () => this.saveIntegrationConfig());
        }

        // Cerrar modal al hacer clic fuera
        window.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });

        // Validación en tiempo real
        this.setupValidation();
    }

    setupValidation() {
        // Validar que los correos sean válidos
        if (this.elements.notification_emails) {
            this.elements.notification_emails.addEventListener('blur', () => {
                this.validateEmails(this.elements.notification_emails);
            });
        }

        // Validar números
        const numberFields = ['max_attempts', 'lockout_time', 'session_timeout'];
        numberFields.forEach(field => {
            if (this.elements[field]) {
                this.elements[field].addEventListener('input', (e) => {
                    let value = parseInt(e.target.value);
                    const min = parseInt(e.target.min) || 1;
                    const max = parseInt(e.target.max) || 999;
                    
                    if (isNaN(value)) {
                        e.target.value = min;
                    } else if (value < min) {
                        e.target.value = min;
                    } else if (value > max) {
                        e.target.value = max;
                    }
                });
            }
        });
    }

    validateEmails(input) {
        const value = input.value;
        if (!value) {
            input.style.borderColor = '#e5e7eb';
            return true;
        }

        const emails = value.split(',').map(email => email.trim());
        const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        
        const invalidEmails = emails.filter(email => email && !emailRegex.test(email));
        
        if (invalidEmails.length > 0) {
            input.style.borderColor = '#ef4444';
            this.showToast(`Correos inválidos: ${invalidEmails.join(', ')}`, 'error');
            return false;
        } else {
            input.style.borderColor = '#10b981';
            return true;
        }
    }

    collectSettings() {
        return {
            general: {
                system_name: this.elements.system_name?.value || '',
                timezone: this.elements.timezone?.value || 'America/Caracas',
                date_format: this.elements.date_format?.value || 'd/m/Y',
                language: this.elements.language?.value || 'es'
            },
            security: {
                max_attempts: parseInt(this.elements.max_attempts?.value) || 5,
                lockout_time: parseInt(this.elements.lockout_time?.value) || 15,
                session_timeout: parseInt(this.elements.session_timeout?.value) || 30,
                session_timeout_enabled: this.elements.session_timeout_enabled?.checked || false,
                two_factor_auth: this.elements.two_factor_auth?.checked || false
            },
            notifications: {
                email_notifications: this.elements.email_notifications?.checked || false,
                access_alerts: this.elements.access_alerts?.checked || false,
                notification_emails: this.elements.notification_emails?.value || ''
            },
            backup: {
                auto_backup: this.elements.auto_backup?.checked || false,
                backup_frequency: this.elements.backup_frequency?.value || 'weekly',
                backup_time: this.elements.backup_time?.value || '02:00'
            },
            integrations: {
                whatsapp: this.elements.whatsapp_integration?.checked || false
            }
        };
    }

    async saveSettings() {
        // Validar correos antes de guardar
        if (this.elements.notification_emails && !this.validateEmails(this.elements.notification_emails)) {
            return;
        }

        const settings = this.collectSettings();
        
        this.showLoading(true);
        
        try {
            const response = await fetch('/admin/settings/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(settings)
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('Configuración guardada exitosamente', 'success');
                this.updateLastSaved();
                
                // Recargar la página después de 1 segundo para aplicar cambios
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                this.showToast(data.message || 'Error al guardar la configuración', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showToast('Error de conexión al guardar la configuración', 'error');
        } finally {
            this.showLoading(false);
        }
    }

    resetSettings() {
        Swal.fire({
            title: '¿Restablecer configuración?',
            text: 'Esta acción restablecerá toda la configuración a los valores predeterminados. ¿Estás seguro?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, restablecer',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const response = await fetch('/admin/settings/reset', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': this.getCsrfToken(),
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.showToast('Configuración restablecida', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        this.showToast(data.message || 'Error al restablecer', 'error');
                    }
                } catch (error) {
                    this.showToast('Error de conexión', 'error');
                }
            }
        });
    }

    async performBackup() {
        Swal.fire({
            title: 'Realizar respaldo',
            text: '¿Deseas realizar una copia de seguridad de la base de datos?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, respaldar',
            cancelButtonText: 'Cancelar'
        });
        
        this.showToast('Iniciando respaldo manual...', 'info');
        
        try {
            const response = await fetch('/admin/settings/backup', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.getCsrfToken(),
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    title: '¡Respaldo completado!',
                    text: 'La copia de seguridad se ha realizado exitosamente.',
                    icon: 'success',
                    confirmButtonText: 'Descargar',
                    showCancelButton: true,
                    cancelButtonText: 'Cerrar'
                }).then((result) => {
                    if (result.isConfirmed && data.download_url) {
                        window.open(data.download_url, '_blank');
                    }
                });
            } else {
                Swal.fire('Error', data.message || 'Error al realizar el respaldo', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire('Error', 'Error de conexión al realizar el respaldo', 'error');
        }
    }

    openConfigModal(integration) {
        const titles = {
            nfc: 'Configuración de Lectores NFC',
            printer: 'Configuración de Impresoras',
            api: 'Configuración de API Externa'
        };
        
        const contents = {
            nfc: `
                <div class="form-group">
                    <label class="form-label">Puerto del Lector</label>
                    <input type="text" class="form-control" id="nfc_port" placeholder="COM3 o /dev/ttyUSB0">
                </div>
                <div class="form-group">
                    <label class="form-label">Baud Rate</label>
                    <select class="form-control" id="nfc_baudrate">
                        <option value="9600">9600</option>
                        <option value="19200">19200</option>
                        <option value="115200" selected>115200</option>
                    </select>
                </div>
            `,
            printer: `
                <div class="form-group">
                    <label class="form-label">Nombre de la Impresora</label>
                    <input type="text" class="form-control" id="printer_name" placeholder="EPSON TM-T20">
                </div>
                <div class="form-group">
                    <label class="form-label">Tamaño del Ticket</label>
                    <select class="form-control" id="printer_size">
                        <option value="58">58mm</option>
                        <option value="80" selected>80mm</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Tipo de conexión</label>
                    <select class="form-control" id="printer_connection">
                        <option value="usb">USB</option>
                        <option value="network">Red</option>
                        <option value="bluetooth">Bluetooth</option>
                    </select>
                </div>
            `,
            api: `
                <div class="form-group">
                    <label class="form-label">URL de la API</label>
                    <input type="url" class="form-control" id="api_url" placeholder="https://api.ejemplo.com/v1">
                </div>
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" class="form-control" id="api_key" placeholder="••••••••">
                </div>
                <div class="form-group">
                    <label class="form-label">Timeout (segundos)</label>
                    <input type="number" class="form-control" id="api_timeout" value="30" min="5" max="120">
                </div>
            `
        };
        
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        if (modalTitle) modalTitle.textContent = titles[integration] || 'Configuración';
        if (modalBody) modalBody.innerHTML = contents[integration] || '<p>Configuración no disponible</p>';
        
        this.currentIntegration = integration;
        this.modal.style.display = 'flex';
    }

    closeModal() {
        this.modal.style.display = 'none';
        this.currentIntegration = null;
    }

    saveIntegrationConfig() {
        // Aquí puedes guardar la configuración de la integración
        Swal.fire('Guardado', `Configuración de ${this.currentIntegration} guardada`, 'success');
        this.closeModal();
    }

    updateLastSaved() {
        const now = new Date();
        const formattedDate = now.toLocaleString('es-ES');
        const lastSavedSpan = document.querySelector('#lastSavedInfo span');
        if (lastSavedSpan) {
            lastSavedSpan.textContent = `Última modificación: ${formattedDate}`;
        }
    }

    loadInitialData() {
        this.updateLastSaved();
    }

    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || '';
    }

    showLoading(show) {
        if (this.saveBtn) {
            if (show) {
                this.saveBtn.disabled = true;
                this.saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            } else {
                this.saveBtn.disabled = false;
                this.saveBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
            }
        }
    }

    showToast(message, type = 'success') {
        // Usar SweetAlert2 para notificaciones más bonitas
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        const icon = type === 'success' ? 'success' : type === 'error' ? 'error' : 'info';
        Toast.fire({
            icon: icon,
            title: message
        });
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.settingsManager = new SettingsManager();
});