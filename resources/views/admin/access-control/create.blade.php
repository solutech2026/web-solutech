@extends('layouts.admin')

@section('title', 'Registrar Persona')

@section('header', 'Registrar Nueva Persona')

@section('content')
<div class="create-person-container">
    <div class="create-card">
        <div class="create-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h3>Registrar Nueva Persona</h3>
        <p class="text-muted">Complete los datos de la persona según su categoría</p>

        <form method="POST" action="{{ route('admin.access-control.store') }}">
            @csrf

            <div class="row">
                <!-- Categoría -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Categoría *</label>
                    <select name="category" id="personCategory" class="form-select" required>
                        <option value="employee">Empleado</option>
                        <option value="school">Personal Escolar</option>
                    </select>
                </div>

                <!-- Subcategoría (solo para escolar) -->
                <div class="col-md-6 form-group" id="subcategoryGroup" style="display: none;">
                    <label class="form-label">Rol *</label>
                    <select name="subcategory" id="personSubcategory" class="form-select">
                        <option value="">-- Seleccionar rol --</option>
                        <option value="student">Estudiante</option>
                        <option value="teacher">Docente</option>
                        <option value="administrative">Administrativo</option>
                    </select>
                </div>

                <!-- Empresa / Colegio -->
                <div class="col-md-6 form-group">
                    <label class="form-label" id="companyLabel">Empresa *</label>
                    <select name="company_id" class="form-select" required>
                        <option value="">Seleccionar</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" data-type="{{ $company->type }}">
                                {{ $company->name }} ({{ $company->type == 'company' ? 'Empresa' : 'Colegio' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nombre -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <!-- Apellido -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Apellido</label>
                    <input type="text" name="lastname" class="form-control">
                </div>

                <!-- Cédula -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Cédula / Documento</label>
                    <input type="text" name="document_id" class="form-control">
                </div>

                <!-- Email -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control">
                </div>

                <!-- Teléfono -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control">
                </div>

                <!-- Género -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Género</label>
                    <select name="gender" class="form-select">
                        <option value="">Seleccionar</option>
                        <option value="male">Masculino</option>
                        <option value="female">Femenino</option>
                        <option value="other">Otro</option>
                    </select>
                </div>

                <!-- Fecha de nacimiento -->
                <div class="col-md-6 form-group">
                    <label class="form-label">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" class="form-control">
                </div>

                <!-- ========== CAMPOS PARA EMPLEADO ========== -->
                <div class="employee-fields">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Cargo / Posición</label>
                        <input type="text" name="position" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Departamento</label>
                        <select name="department" class="form-select">
                            <option value="">Seleccionar</option>
                            <option value="Administración">Administración</option>
                            <option value="Tecnología">Tecnología</option>
                            <option value="Ventas">Ventas</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Recursos Humanos">Recursos Humanos</option>
                            <option value="Operaciones">Operaciones</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label class="form-label">Biografía / Notas</label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Experiencia profesional, educación, logros..."></textarea>
                    </div>
                </div>

                <!-- ========== CAMPOS PARA ESTUDIANTE ========== -->
                <div class="student-fields" style="display: none;">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Grado *</label>
                        <select name="grade_level" class="form-select">
                            <option value="">Seleccionar grado</option>
                            <optgroup label="Primaria">
                                <option value="1st">1er Grado</option>
                                <option value="2nd">2do Grado</option>
                                <option value="3rd">3er Grado</option>
                                <option value="4th">4to Grado</option>
                                <option value="5th">5to Grado</option>
                                <option value="6th">6to Grado</option>
                            </optgroup>
                            <optgroup label="Liceo / Secundaria">
                                <option value="7th">1er Año</option>
                                <option value="8th">2do Año</option>
                                <option value="9th">3er Año</option>
                            </optgroup>
                            <optgroup label="Ciclo Diversificado">
                                <option value="10th">4to Año</option>
                                <option value="11th">5to Año</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Año Escolar *</label>
                        <input type="text" name="academic_year" class="form-control" placeholder="Ej: 2024-2025">
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">Contacto de emergencia *</label>
                        <input type="text" name="emergency_contact_name" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Teléfono de emergencia *</label>
                        <input type="text" name="emergency_phone" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Alergias</label>
                        <input type="text" name="allergies" class="form-control" placeholder="Ej: Penicilina, Mariscos...">
                    </div>
                    <div class="col-12 form-group">
                        <label class="form-label">Condiciones médicas</label>
                        <textarea name="medical_conditions" class="form-control" rows="2" placeholder="Ej: Asma, Diabetes..."></textarea>
                    </div>
                </div>

                <!-- ========== CAMPOS PARA DOCENTE ========== -->
                <div class="teacher-fields" style="display: none;">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Cargo *</label>
                        <input type="text" name="position" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Tipo de docente</label>
                        <select name="teacher_type" class="form-select">
                            <option value="">Seleccionar</option>
                            <option value="regular">Docente Regular</option>
                            <option value="substitute">Docente Suplente</option>
                            <option value="special_education">Educación Especial</option>
                            <option value="part_time">Medio Tiempo</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">Contacto de emergencia</label>
                        <input type="text" name="emergency_contact_name" class="form-control">
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">Teléfono de emergencia</label>
                        <input type="text" name="emergency_phone" class="form-control">
                    </div>
                </div>

                <!-- ========== CAMPOS PARA ADMINISTRATIVO ========== -->
                <div class="administrative-fields" style="display: none;">
                    <div class="col-md-6 form-group">
                        <label class="form-label">Cargo *</label>
                        <input type="text" name="position" class="form-control">
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="form-label">Departamento</label>
                        <select name="department" class="form-select">
                            <option value="">Seleccionar</option>
                            <option value="Dirección">Dirección</option>
                            <option value="Secretaría">Secretaría</option>
                            <option value="Contabilidad">Contabilidad</option>
                            <option value="Orientación">Orientación</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">Contacto de emergencia</label>
                        <input type="text" name="emergency_contact_name" class="form-control">
                    </div>
                    <div class="col-md-12 form-group">
                        <label class="form-label">Teléfono de emergencia</label>
                        <input type="text" name="emergency_phone" class="form-control">
                    </div>
                </div>

                <!-- Crear usuario -->
                <div class="col-12 form-group">
                    <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="create_user" value="1">
                        <span>Crear usuario para acceso al sistema</span>
                    </label>
                    <small class="form-text">Se creará un usuario con rol según la categoría seleccionada</small>
                </div>

                <!-- Asignación de Tarjeta NFC -->
                <div class="col-12 form-group">
                    <label class="form-label">Asignar Tarjeta NFC</label>
                    @if($availableCards->count() > 0)
                        <select name="card_id" class="form-select">
                            <option value="">-- Sin tarjeta (asignar después) --</option>
                            @foreach($availableCards as $card)
                                <option value="{{ $card->id }}">
                                    {{ $card->card_code }} 
                                    @if($card->notes) - {{ $card->notes }} @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text">
                            <i class="fas fa-info-circle"></i> 
                            Seleccione una tarjeta NFC disponible para asignar a esta persona
                        </small>
                    @else
                        <div class="alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            No hay tarjetas NFC disponibles. 
                            <a href="{{ route('admin.nfc-cards.create') }}">Registre una tarjeta</a> primero.
                        </div>
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Registrar Persona
                </button>
                <a href="{{ route('admin.access-control.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons-form.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
    // Manejo de categorías
    const categorySelect = document.getElementById('personCategory');
    const subcategoryGroup = document.getElementById('subcategoryGroup');
    const subcategorySelect = document.getElementById('personSubcategory');
    const companyLabel = document.getElementById('companyLabel');
    
    // Campos por tipo
    const employeeFields = document.querySelector('.employee-fields');
    const studentFields = document.querySelector('.student-fields');
    const teacherFields = document.querySelector('.teacher-fields');
    const administrativeFields = document.querySelector('.administrative-fields');
    
    function hideAllFields() {
        if (employeeFields) employeeFields.style.display = 'none';
        if (studentFields) studentFields.style.display = 'none';
        if (teacherFields) teacherFields.style.display = 'none';
        if (administrativeFields) administrativeFields.style.display = 'none';
    }
    
    function updateFormByCategory() {
        const category = categorySelect.value;
        const subcategory = subcategorySelect.value;
        
        hideAllFields();
        
        if (category === 'employee') {
            companyLabel.innerHTML = 'Empresa *';
            subcategoryGroup.style.display = 'none';
            subcategorySelect.value = '';
            if (employeeFields) employeeFields.style.display = 'block';
        } else if (category === 'school') {
            companyLabel.innerHTML = 'Colegio *';
            subcategoryGroup.style.display = 'block';
            
            if (subcategory === 'student') {
                if (studentFields) studentFields.style.display = 'block';
            } else if (subcategory === 'teacher') {
                if (teacherFields) teacherFields.style.display = 'block';
            } else if (subcategory === 'administrative') {
                if (administrativeFields) administrativeFields.style.display = 'block';
            }
        }
    }
    
    categorySelect?.addEventListener('change', updateFormByCategory);
    subcategorySelect?.addEventListener('change', updateFormByCategory);
    
    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        updateFormByCategory();
    });
</script>
@endpush