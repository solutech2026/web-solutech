/**
 * PROXICARD - Access Control Module
 */

// Filtrar personas
function filterPersons() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const company = document.getElementById('companyFilter').value;
    
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
    } else if (emptyMessage) {
        emptyMessage.remove();
    }
}

// Abrir modal de asignación de NFC
function openAssignNFCModal(personId, personName) {
    document.getElementById('assignPersonName').innerText = personName;
    document.getElementById('assignNFCForm').action = `/admin/access-control/${personId}/assign-nfc`;
    const modal = new bootstrap.Modal(document.getElementById('assignNFCModal'));
    modal.show();
}

// Eliminar persona
function deletePerson(id) {
    if (confirm('¿Estás seguro de eliminar esta persona? Esta acción no se puede deshacer.')) {
        fetch(`/admin/access-control/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
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
}

// Exportar logs
function exportLogs() {
    const company = document.getElementById('companyFilter').value;
    const search = document.getElementById('searchInput').value;
    const params = new URLSearchParams({ company, search });
    window.location.href = `/admin/access-control/export-logs?${params.toString()}`;
}

// Mostrar notificación
function showNotification(message, type = 'success') {
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
    
    setTimeout(() => {
        if (notification && notification.remove) {
            notification.remove();
        }
    }, 4000);
}

// Asignar NFC - submit form
document.getElementById('assignNFCForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Asignando...';
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
            showNotification(data.message || 'Tarjeta asignada correctamente', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error al asignar la tarjeta', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error de conexión', 'error');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const companyFilter = document.getElementById('companyFilter');
    
    if (searchInput) searchInput.addEventListener('keyup', filterPersons);
    if (categoryFilter) categoryFilter.addEventListener('change', filterPersons);
    if (companyFilter) companyFilter.addEventListener('change', filterPersons);
    
    // Animación de entrada para las tarjetas
    const cards = document.querySelectorAll('.person-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.05}s`;
    });
});