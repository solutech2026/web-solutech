/**
 * PROXICARD - Profile Module
 * Manejo del perfil de usuario, formularios y notificaciones
 */

// ========================================
// NOTIFICACIONES
// ========================================

function showNotification(message, type = 'success') {
    // Remover notificaciones existentes
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
        <span class="message">${message}</span>
        <button class="close-btn" onclick="this.closest('.notification-toast').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto cerrar después de 4 segundos
    setTimeout(() => {
        if (notification && notification.remove) {
            notification.remove();
        }
    }, 4000);
}

// ========================================
// FORMULARIO DE PERFIL
// ========================================

const profileForm = document.getElementById('profileForm');
if (profileForm) {
    profileForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
        submitBtn.disabled = true;
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showNotification(data.message || 'Perfil actualizado correctamente', 'success');
                
                // Actualizar nombre en la tarjeta de perfil
                const nameInput = this.querySelector('input[name="name"]');
                if (nameInput) {
                    const profileName = document.querySelector('.profile-name');
                    if (profileName) profileName.textContent = nameInput.value;
                    
                    const profileAvatar = document.querySelector('.profile-avatar');
                    if (profileAvatar && nameInput.value.length >= 2) {
                        profileAvatar.textContent = nameInput.value.substring(0, 2).toUpperCase();
                    }
                }
                
                // Recargar después de 1.5 segundos si es necesario
                if (data.reload) {
                    setTimeout(() => location.reload(), 1500);
                }
            } else {
                showNotification(data.message || 'Error al actualizar el perfil', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error de conexión al actualizar el perfil', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// ========================================
// FORMULARIO DE CONTRASEÑA
// ========================================

const passwordForm = document.getElementById('passwordForm');
if (passwordForm) {
    passwordForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const password = this.querySelector('input[name="password"]').value;
        const confirm = this.querySelector('input[name="password_confirmation"]').value;
        
        // Validaciones del lado del cliente
        if (password !== confirm) {
            showNotification('Las contraseñas no coinciden', 'error');
            return;
        }
        
        if (password.length < 8) {
            showNotification('La contraseña debe tener al menos 8 caracteres', 'error');
            return;
        }
        
        // Validar fortaleza de contraseña
        const hasUpper = /[A-Z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        
        if (!hasUpper || !hasNumber) {
            showNotification('La contraseña debe incluir al menos una mayúscula y un número', 'error');
            return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cambiando...';
        submitBtn.disabled = true;
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                showNotification(data.message || 'Contraseña cambiada correctamente', 'success');
                this.reset();
            } else {
                showNotification(data.message || 'Error al cambiar la contraseña', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Error de conexión al cambiar la contraseña', 'error');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// ========================================
// CERRAR SESIONES
// ========================================

window.logoutOtherSessions = async function() {
    if (!confirm('¿Estás seguro de que deseas cerrar todas las demás sesiones activas?')) {
        return;
    }
    
    const btn = document.querySelector('.btn-outline-modern');
    const originalText = btn ? btn.innerHTML : '';
    
    if (btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando...';
        btn.disabled = true;
    }
    
    try {
        const response = await fetch('/admin/profile/logout-other-sessions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showNotification(data.message || 'Sesiones cerradas correctamente', 'success');
        } else {
            showNotification(data.message || 'Error al cerrar sesiones', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión al cerrar sesiones', 'error');
    } finally {
        if (btn) {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }
};

// ========================================
// VALIDACIONES EN TIEMPO REAL
// ========================================

// Validar fortaleza de contraseña mientras escribe
const passwordInput = document.querySelector('input[name="password"]');
if (passwordInput) {
    passwordInput.addEventListener('input', function(e) {
        const password = e.target.value;
        
        // Remover indicador anterior
        const oldIndicator = e.target.parentNode.querySelector('.password-strength');
        if (oldIndicator) oldIndicator.remove();
        
        if (password.length > 0) {
            const strengthIndicator = document.createElement('small');
            strengthIndicator.className = 'password-strength';
            
            let strength = 0;
            let message = '';
            let color = '';
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    message = 'Débil';
                    color = '#ef4444';
                    break;
                case 2:
                    message = 'Regular';
                    color = '#f59e0b';
                    break;
                case 3:
                    message = 'Buena';
                    color = '#10b981';
                    break;
                case 4:
                    message = 'Fuerte';
                    color = '#10b981';
                    break;
            }
            
            strengthIndicator.textContent = `Fortaleza: ${message}`;
            strengthIndicator.style.color = color;
            strengthIndicator.style.display = 'block';
            strengthIndicator.style.marginTop = '0.375rem';
            strengthIndicator.style.fontSize = '0.7rem';
            
            e.target.parentNode.appendChild(strengthIndicator);
        }
    });
}

// ========================================
// PREVISUALIZACIÓN DE AVATAR
// ========================================

const avatarInput = document.querySelector('input[name="avatar"]');
if (avatarInput) {
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(file.type)) {
                showNotification('Formato no válido. Use JPG o PNG', 'error');
                this.value = '';
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                showNotification('El archivo no debe superar los 2MB', 'error');
                this.value = '';
                return;
            }
            
            // Previsualizar avatar
            const reader = new FileReader();
            reader.onload = function(e) {
                const avatar = document.querySelector('.profile-avatar');
                if (avatar) {
                    avatar.style.backgroundImage = `url(${e.target.result})`;
                    avatar.style.backgroundSize = 'cover';
                    avatar.style.backgroundPosition = 'center';
                    avatar.textContent = '';
                }
            };
            reader.readAsDataURL(file);
        }
    });
}

// ========================================
// INICIALIZACIÓN
// ========================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Profile module loaded');
    
    // Mostrar mensajes flash si existen
    const flashMessage = document.querySelector('meta[name="flash-message"]');
    const flashType = document.querySelector('meta[name="flash-type"]');
    if (flashMessage && flashMessage.content) {
        showNotification(flashMessage.content, flashType?.content || 'success');
    }
});