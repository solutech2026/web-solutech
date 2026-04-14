// ============================================
// VARIABLES GLOBALES
// ============================================

const categoryRadios = document.querySelectorAll('input[name="category"]');
const subcategorySection = document.getElementById('subcategorySection');
const subcategoryOptions = document.querySelectorAll('.subcategory-option');
const subcategoryInput = document.getElementById('subcategoryInput');

const employeeFields = document.querySelector('.employee-fields');
const studentFields = document.querySelector('.student-fields');
const teacherFields = document.querySelector('.teacher-fields');
const administrativeFields = document.querySelector('.administrative-fields');
const scheduleFields = document.querySelector('.schedule-fields');
const companyLabel = document.getElementById('companyLabel');

let scheduleIndex = 1;

// ============================================
// FUNCIONES PRINCIPALES
// ============================================

/**
 * Oculta todas las secciones de campos
 */
function hideAllCategoryFields() {
    if (employeeFields) employeeFields.style.display = 'none';
    if (studentFields) studentFields.style.display = 'none';
    if (teacherFields) teacherFields.style.display = 'none';
    if (administrativeFields) administrativeFields.style.display = 'none';
    if (scheduleFields) scheduleFields.style.display = 'none';
}

/**
 * Actualiza el formulario según la categoría seleccionada
 */
function updateFormByCategory() {
    const selectedCategory = document.querySelector('input[name="category"]:checked')?.value;
    const selectedSubcategory = subcategoryInput.value;

    hideAllCategoryFields();

    if (selectedCategory === 'employee') {
        if (companyLabel) companyLabel.innerHTML = 'Empresa *';
        if (employeeFields) employeeFields.style.display = 'block';
        if (scheduleFields) scheduleFields.style.display = 'none';
        if (subcategorySection) subcategorySection.style.display = 'none';
        if (subcategoryInput) subcategoryInput.value = '';
        if (subcategoryOptions) {
            subcategoryOptions.forEach(opt => opt.classList.remove('active'));
        }
        
    } else if (selectedCategory === 'school') {
        if (companyLabel) companyLabel.innerHTML = 'Colegio *';
        if (subcategorySection) subcategorySection.style.display = 'block';

        if (selectedSubcategory === 'student') {
            if (studentFields) studentFields.style.display = 'block';
            if (scheduleFields) scheduleFields.style.display = 'block';
            
        } else if (selectedSubcategory === 'teacher') {
            if (teacherFields) teacherFields.style.display = 'block';
            if (scheduleFields) scheduleFields.style.display = 'block';
            
        } else if (selectedSubcategory === 'administrative') {
            if (administrativeFields) administrativeFields.style.display = 'block';
            if (scheduleFields) scheduleFields.style.display = 'block';
        }
    }
}

/**
 * Inicializa los botones de horarios
 */
function initScheduleButtons() {
    const addBtn = document.querySelector('.btn-add-schedule');
    if (addBtn) {
        const newAddBtn = addBtn.cloneNode(true);
        addBtn.parentNode.replaceChild(newAddBtn, addBtn);
        
        newAddBtn.addEventListener('click', function() {
            const container = document.getElementById('scheduleContainer');
            if (!container) return;
            
            const rowCount = container.querySelectorAll('.schedule-row').length;
            const newRow = document.createElement('div');
            newRow.className = 'schedule-row';
            newRow.innerHTML = `
                <select name="schedule[${rowCount}][day]" class="input-modern">
                    <option value="">Día</option>
                    <option value="monday">Lunes</option>
                    <option value="tuesday">Martes</option>
                    <option value="wednesday">Miércoles</option>
                    <option value="thursday">Jueves</option>
                    <option value="friday">Viernes</option>
                </select>
                <input type="time" name="schedule[${rowCount}][start_time]" placeholder="Hora inicio">
                <input type="time" name="schedule[${rowCount}][end_time]" placeholder="Hora fin">
                <input type="text" name="schedule[${rowCount}][subject]" placeholder="Materia/Actividad">
                <input type="text" name="schedule[${rowCount}][classroom]" placeholder="Aula">
                <button type="button" class="btn-remove-schedule">-</button>
            `;
            container.appendChild(newRow);
            
            const removeBtn = newRow.querySelector('.btn-remove-schedule');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newRow.remove();
                });
            }
        });
    }
    
    document.querySelectorAll('.btn-remove-schedule').forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        newBtn.addEventListener('click', function() {
            this.closest('.schedule-row').remove();
        });
    });
}

/**
 * Maneja la carga y previsualización de fotos
 */
function initPhotoUpload() {
    const photoInput = document.getElementById('photoInput');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('La imagen no debe superar los 2MB');
                    this.value = '';
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato no permitido. Use JPG, PNG o GIF');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('currentPhotoPreview');
                    if (preview) {
                        preview.innerHTML = `<img src="${event.target.result}" alt="Vista previa" style="width: 100%; height: 100%; object-fit: cover;">`;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Elimina la foto actual
 */
window.removePhoto = function() {
    if (confirm('¿Eliminar la foto actual?')) {
        const photoInput = document.getElementById('photoInput');
        const preview = document.getElementById('currentPhotoPreview');

        let removeInput = document.querySelector('input[name="remove_photo"]');
        if (!removeInput) {
            removeInput = document.createElement('input');
            removeInput.type = 'hidden';
            removeInput.name = 'remove_photo';
            removeInput.value = '1';
            const form = document.getElementById('personForm');
            if (form) form.appendChild(removeInput);
        } else {
            removeInput.value = '1';
        }

        if (preview) {
            preview.innerHTML = `
                <div class="photo-placeholder">
                    <i class="fas fa-camera"></i>
                    <span>Sin foto</span>
                </div>
            `;
        }
        if (photoInput) photoInput.value = '';
    }
};

/**
 * Inicializa todos los event listeners
 */
function initEventListeners() {
    if (categoryRadios) {
        categoryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.category-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                this.closest('.category-option')?.classList.add('active');
                
                if (this.value === 'school') {
                    if (subcategorySection) subcategorySection.style.display = 'block';
                } else {
                    if (subcategorySection) subcategorySection.style.display = 'none';
                    if (subcategoryInput) subcategoryInput.value = '';
                    if (subcategoryOptions) {
                        subcategoryOptions.forEach(opt => opt.classList.remove('active'));
                    }
                }
                updateFormByCategory();
            });
        });
    }
    
    if (subcategoryOptions) {
        subcategoryOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.subcategory;
                if (subcategoryInput) subcategoryInput.value = value;
                subcategoryOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                updateFormByCategory();
            });
        });
    }
    
    // Evento submit del formulario - SIN LIMPIAR CAMPOS
    const personForm = document.getElementById('personForm');
    if (personForm) {
        personForm.addEventListener('submit', function(e) {
            console.log('=== ENVIANDO FORMULARIO ===');
            
            const selectedCategory = document.querySelector('input[name="category"]:checked')?.value;
            const selectedSubcategory = subcategoryInput?.value;
            
            if (selectedCategory === 'school' && selectedSubcategory === 'student') {
                const emergencyName = document.querySelector('input[name="emergency_contact_name"]');
                const emergencyPhone = document.querySelector('input[name="emergency_phone"]');
                
                console.log('Emergency Name valor:', emergencyName?.value);
                console.log('Emergency Phone valor:', emergencyPhone?.value);
                
                // NO limpiar ni modificar los valores
                // Solo asegurar que los campos estén habilitados
                if (emergencyName) emergencyName.disabled = false;
                if (emergencyPhone) emergencyPhone.disabled = false;
            }
            
            return true;
        });
    }
}

// ============================================
// INICIALIZACIÓN
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== INICIALIZANDO FORMULARIO ===');
    
    initScheduleButtons();
    initPhotoUpload();
    initEventListeners();
    
    const selectedCategory = document.querySelector('input[name="category"]:checked');
    if (selectedCategory) {
        const parentOption = selectedCategory.closest('.category-option');
        if (parentOption) parentOption.classList.add('active');
        
        if (selectedCategory.value === 'school') {
            if (subcategorySection) subcategorySection.style.display = 'block';
            const selectedSub = subcategoryInput?.value;
            if (selectedSub) {
                const activeOption = document.querySelector(
                    `.subcategory-option[data-subcategory="${selectedSub}"]`);
                if (activeOption) activeOption.classList.add('active');
            }
        }
        updateFormByCategory();
    }

    const existingScheduleRows = document.querySelectorAll('.schedule-row');
    if (existingScheduleRows.length > 0) {
        scheduleIndex = existingScheduleRows.length;
    }
    
    // Verificar estado inicial de los campos de emergencia
    setTimeout(() => {
        const emergencyName = document.querySelector('input[name="emergency_contact_name"]');
        const emergencyPhone = document.querySelector('input[name="emergency_phone"]');
        
        console.log('Estado inicial:');
        console.log('- emergency_contact_name existe:', !!emergencyName);
        console.log('- emergency_phone existe:', !!emergencyPhone);
        if (emergencyName) console.log('- emergency_contact_name valor:', emergencyName.value);
        if (emergencyPhone) console.log('- emergency_phone valor:', emergencyPhone.value);
    }, 500);
});