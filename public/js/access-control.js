/**
 * PROXICARD - Access Control Module
 * Versión con modal funcional
 */

// Variable global para el modal de Bootstrap
let assignNFCModal = null;
let currentPersonId = null;

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - Initializing Access Control');
    
    // Inicializar el modal de Bootstrap
    initModal();
    
    // Inicializar event listeners
    initEventListeners();
    initAnimations();
    
    // Configurar el formulario de asignación
    setupAssignForm();
});

// Inicializar modal de Bootstrap
function initModal() {
    const modalElement = document.getElementById('assignNFCModal');
    if (modalElement) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            assignNFCModal = new bootstrap.Modal(modalElement);
            console.log('Bootstrap modal initialized');
        } else {
            console.warn('Bootstrap not found, using fallback');
            // Fallback manual
            assignNFCModal = {
                show: function() {
                    const el = document.getElementById('assignNFCModal');
                    if (el) {
                        el.style.display = 'block';
                        el.classList.add('show');
                        document.body.classList.add('modal-open');
                        // Crear backdrop
                        let backdrop = document.querySelector('.modal-backdrop');
                        if (!backdrop) {
                            backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade show';
                            document.body.appendChild(backdrop);
                        }
                    }
                },
                hide: function() {
                    const el = document.getElementById('assignNFCModal');
                    if (el) {
                        el.style.display = 'none';
                        el.classList.remove('show');
                        document.body.classList.remove('modal-open');
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) backdrop.remove();
                    }
                }
            };
        }
    }
}

function initEventListeners() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const companyFilter = document.getElementById('companyFilter');
    
    if (searchInput) searchInput.addEventListener('keyup', filterPersons);
    if (categoryFilter) categoryFilter.addEventListener('change', filterPersons);
    if (companyFilter) companyFilter.addEventListener('change', filterPersons);
}

function initAnimations() {
    const cards = document.querySelectorAll('.person-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
    });
}

function setupAssignForm() {
    const assignForm = document.getElementById('assignNFCForm');
    if (assignForm) {
        // Remover event listeners anteriores clonando y reemplazando
        const newForm = assignForm.cloneNode(true);
        assignForm.parentNode.replaceChild(newForm, assignForm);
        
        // Agregar nuevo event listener
        newForm.addEventListener('submit', handleAssignNFCSubmit);
        console.log('Assign form setup complete');
    }
}

// Filtrar personas
function filterPersons() {
    const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const category = document.getElementById('categoryFilter')?.value || 'all';
    const company = document.getElementById('companyFilter')?.value || 'all';
    
    const cards = document.querySelectorAll('.person-card');
    let visibleCount = 0;
    
    cards.forEach(card => {
        let show = true;
        
        const cardCategory = card.dataset.category;
        const cardCompany = card.dataset.company;
        const cardName = card.dataset.name || '';
        const cardDocument = card.dataset.document || '';
        
        if (search && !cardName.includes(search) && !cardDocument.includes(search)) {
            show = false;
        }
        if (category !== 'all' && cardCategory !== category) {
            show = false;
        }
        if (company !== 'all' && cardCompany !== company) {
            show = false;
        }
        
        card.style.display = show ? 'flex' : 'none';
        if (show) visibleCount++;
    });
    
    // Mostrar mensaje si no hay resultados
    const emptyMessage = document.getElementById('emptyFilterMessage');
    if (visibleCount === 0 && cards.length > 0) {
        if (!emptyMessage) {
            const grid = document.getElementById('personsGrid');
            if (grid) {
                const msg = document.createElement('div');
                msg.id = 'emptyFilterMessage';
                msg.className = 'empty-state';
                msg.innerHTML = `
                    <i class="fas fa-filter"></i>
                    <h3>No hay resultados</h3>
                    <p>No se encontraron personas con los filtros seleccionados</p>
                `;
                grid.appendChild(msg);
            }
        }
    } else if (emptyMessage) {
        emptyMessage.remove();
    }
}

// Abrir modal de asignación de NFC
function openAssignNFCModal(personId, personName) {
    console.log('Opening modal for:', personId, personName);
    
    try {
        // Guardar el ID de la persona actual
        currentPersonId = personId;
        
        // Limpiar el nombre (eliminar posibles caracteres problemáticos)
        const cleanName = personName.replace(/[<>]/g, '');
        
        // Asignar el nombre al modal
        const personNameElement = document.getElementById('assignPersonName');
        if (personNameElement) {
            personNameElement.textContent = cleanName;
        }
        
        // Asignar la acción del formulario
        const form = document.getElementById('assignNFCForm');
        if (form) {
            // Establecer nueva action
            const actionUrl = `/admin/access-control/${personId}/assign-nfc`;
            form.setAttribute('action', actionUrl);
            console.log('Form action set to:', actionUrl);
        }
        
        // Limpiar el select
        const cardSelect = document.getElementById('nfcCardSelect');
        if (cardSelect) {
            cardSelect.value = '';
        }
        
        // Mostrar el modal
        if (assignNFCModal && typeof assignNFCModal.show === 'function') {
            assignNFCModal.show();
        } else {
            // Re-inicializar y mostrar
            initModal();
            if (assignNFCModal && typeof assignNFCModal.show === 'function') {
                assignNFCModal.show();
            } else {
                // Fallback extremo
                const modalEl = document.getElementById('assignNFCModal');
                if (modalEl) {
                    modalEl.style.display = 'block';
                    modalEl.classList.add('show');
                    document.body.classList.add('modal-open');
                }
            }
        }
    } catch (error) {
        console.error('Error opening modal:', error);
        showNotification('Error al abrir el modal', 'error');
    }
}

// Cerrar modal
function closeAssignModal() {
    if (assignNFCModal && typeof assignNFCModal.hide === 'function') {
        assignNFCModal.hide();
    } else {
        const modalEl = document.getElementById('assignNFCModal');
        if (modalEl) {
            modalEl.style.display = 'none';
            modalEl.classList.remove('show');
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) backdrop.remove();
        }
    }
}

// Manejar el envío del formulario de asignación NFC
async function handleAssignNFCSubmit(event) {
    event.preventDefault();
    console.log('Form submitted');
    
    const form = event.target;
    const submitBtn = document.getElementById('submitAssignBtn');
    const originalText = submitBtn.innerHTML;
    const cardSelect = document.getElementById('nfcCardSelect');
    
    // Validar que se haya seleccionado una tarjeta
    if (!cardSelect || !cardSelect.value) {
        showNotification('Por favor selecciona una tarjeta NFC', 'error');
        return;
    }
    
    console.log('Selected card:', cardSelect.value);
    console.log('Form action:', form.action);
    
    // Mostrar estado de carga
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
    submitBtn.disabled = true;
    
    try {
        const formData = new FormData(form);
        
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        const data = await response.json();
        console.log('Response:', data);
        
        if (response.ok && data.success) {
            showNotification(data.message || 'Tarjeta asignada correctamente', 'success');
            
            // Cerrar el modal
            closeAssignModal();
            
            // Recargar la página después de un breve retraso
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error al asignar la tarjeta', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión al servidor', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Eliminar persona
function deletePerson(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '¿Eliminar persona?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                performDeletePerson(id);
            }
        });
    } else {
        if (confirm('¿Estás seguro de eliminar esta persona? Esta acción no se puede deshacer.')) {
            performDeletePerson(id);
        }
    }
}

function performDeletePerson(id) {
    fetch(`/admin/access-control/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Persona eliminada correctamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error al eliminar la persona', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error al eliminar la persona', 'error');
    });
}

// Exportar logs
function exportLogs() {
    const company = document.getElementById('companyFilter')?.value || 'all';
    const search = document.getElementById('searchInput')?.value || '';
    const params = new URLSearchParams({ company, search });
    window.location.href = `/admin/access-control/export-logs?${params.toString()}`;
}

// Mostrar notificación
function showNotification(message, type = 'success') {
    console.log('Notification:', type, message);
    
    // Usar SweetAlert si está disponible
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: type === 'success' ? 'Éxito' : 'Error',
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: type === 'success' ? '#198754' : '#dc3545',
            color: '#fff'
        });
        return;
    }
    
    // Fallback con alerta simple
    alert(message);
}

// Función de prueba para diagnosticar
function testModal() {
    console.log('Testing modal function...');
    openAssignNFCModal(1, 'Usuario de Prueba');
}

// Exportar funciones para uso global
window.openAssignNFCModal = openAssignNFCModal;
window.deletePerson = deletePerson;
window.exportLogs = exportLogs;
window.testModal = testModal;
window.filterPersons = filterPersons;