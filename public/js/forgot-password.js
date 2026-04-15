{{-- public/js/forgot-password.js --}}
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotForm');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    const emailInput = document.getElementById('email');
    
    // Auto-dismiss alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.classList.add('fade-out');
                setTimeout(() => {
                    if (alert.parentNode) alert.remove();
                }, 300);
            }
        }, 5000);
    });
    
    // Add fade-out animation style
    const style = document.createElement('style');
    style.textContent = `
        .fade-out {
            animation: slideUp 0.3s ease-out reverse !important;
        }
    `;
    document.head.appendChild(style);
    
    // Form submission with loading state
    if (form) {
        form.addEventListener('submit', function(e) {
            // Basic client-side validation
            const email = emailInput.value.trim();
            
            if (!email) {
                e.preventDefault();
                showError(emailInput, 'Por favor ingresa tu correo electrónico');
                return;
            }
            
            if (!isValidEmail(email)) {
                e.preventDefault();
                showError(emailInput, 'Por favor ingresa un correo electrónico válido');
                return;
            }
            
            // Show loading state
            btnText.style.display = 'none';
            btnLoader.style.display = 'flex';
            submitBtn.disabled = true;
            
            // If there are no validation errors, the form will submit naturally
            // If you want to handle AJAX submission, uncomment the code below
            /*
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage(data.message);
                    form.reset();
                } else {
                    showErrorMessage(data.message || 'Ocurrió un error');
                }
            })
            .catch(error => {
                showErrorMessage('Error de conexión. Por favor intenta de nuevo.');
            })
            .finally(() => {
                btnText.style.display = 'flex';
                btnLoader.style.display = 'none';
                submitBtn.disabled = false;
            });
            */
        });
        
        // Real-time validation
        emailInput.addEventListener('input', function() {
            removeError(emailInput);
        });
        
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !isValidEmail(email)) {
                showError(emailInput, 'Correo electrónico inválido');
            }
        });
    }
    
    // Helper functions
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/;
        return emailRegex.test(email);
    }
    
    function showError(input, message) {
        removeError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            color: #dc2626;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        `;
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
        
        input.parentNode.parentNode.appendChild(errorDiv);
        input.style.borderColor = '#dc2626';
        
        // Add shake animation
        input.style.animation = 'shake 0.3s ease-in-out';
        setTimeout(() => {
            input.style.animation = '';
        }, 300);
    }
    
    function removeError(input) {
        const errorDiv = input.parentNode.parentNode.querySelector('.error-message');
        if (errorDiv) errorDiv.remove();
        input.style.borderColor = '';
    }
    
    function showSuccessMessage(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'alert alert-success';
        successDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 350px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        `;
        successDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
            <button class="btn-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(successDiv);
        
        setTimeout(() => {
            if (successDiv.parentNode) successDiv.remove();
        }, 5000);
    }
    
    function showErrorMessage(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger';
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 350px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        `;
        errorDiv.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i>
            <span>${message}</span>
            <button class="btn-close" onclick="this.parentElement.remove()">×</button>
        `;
        
        document.body.appendChild(errorDiv);
        
        setTimeout(() => {
            if (errorDiv.parentNode) errorDiv.remove();
        }, 5000);
    }
    
    // Add shake animation
    const shakeStyle = document.createElement('style');
    shakeStyle.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    `;
    document.head.appendChild(shakeStyle);
});

// Optional: Add smooth scroll to top on page load
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}
window.scrollTo(0, 0);