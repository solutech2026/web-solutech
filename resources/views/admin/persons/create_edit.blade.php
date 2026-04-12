@extends('layouts.admin')

@section('title', isset($person) ? 'Editar Persona' : 'Nueva Persona')

@section('header', isset($person) ? 'Editar Persona' : 'Registrar Persona')

@section('content')
    <div class="person-form-modern">
        <div class="form-hero">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="fas {{ isset($person) ? 'fa-user-edit' : 'fa-user-plus' }}"></i>
                </div>
                <div class="hero-text">
                    <h1>{{ isset($person) ? 'Editar Persona' : 'Registrar Nueva Persona' }}</h1>
                    <p>Complete la información según la categoría seleccionada</p>
                </div>
            </div>
        </div>

        <div class="form-container-glass">
            <form method="POST"
                action="{{ isset($person) ? route('admin.persons.update', $person->id) : route('admin.persons.store') }}"
                id="personForm" enctype="multipart/form-data">
                @csrf
                @if (isset($person))
                    @method('PUT')
                @endif

                <!-- Categoría Principal -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-tag"></i>
                        <h3>Categoría de Persona</h3>
                    </div>
                    <div class="category-selector">
                        <div class="category-option" data-category="employee">
                            <div class="category-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="category-info">
                                <h4>Empleado</h4>
                                <p>Para personas que trabajan en empresas</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="category" value="employee" id="categoryEmployee"
                                    {{ isset($person) && $person->category == 'employee' ? 'checked' : '' }} required>
                                <label for="categoryEmployee"></label>
                            </div>
                        </div>
                        <div class="category-option" data-category="school">
                            <div class="category-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="category-info">
                                <h4>Personal Escolar</h4>
                                <p>Para estudiantes, docentes o personal administrativo</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="category" value="school" id="categorySchool"
                                    {{ isset($person) && $person->category == 'school' ? 'checked' : '' }} required>
                                <label for="categorySchool"></label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subcategoría (solo para escolar) -->
                <div class="form-section" id="subcategorySection" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-layer-group"></i>
                        <h3>Rol en el Colegio</h3>
                    </div>
                    <div class="subcategory-selector">
                        <div class="subcategory-option" data-subcategory="student">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Estudiante</span>
                        </div>
                        <div class="subcategory-option" data-subcategory="teacher">
                            <i class="fas fa-chalkboard-user"></i>
                            <span>Docente</span>
                        </div>
                        <div class="subcategory-option" data-subcategory="administrative">
                            <i class="fas fa-building"></i>
                            <span>Administrativo</span>
                        </div>
                    </div>
                    <input type="hidden" name="subcategory" id="subcategoryInput" value="{{ $person->subcategory ?? '' }}">
                </div>

                <!-- Foto de Perfil -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-camera"></i>
                        <h3>Foto de Perfil</h3>
                    </div>
                    <div class="photo-upload-container">
                        <div class="current-photo" id="currentPhotoPreview">
                            @if (isset($person) && $person->photo)
                                <img src="{{ asset('storage/' . $person->photo) }}" alt="Foto actual">
                            @else
                                <div class="photo-placeholder">
                                    <i class="fas fa-camera"></i>
                                    <span>Sin foto</span>
                                </div>
                            @endif
                        </div>
                        <div class="photo-upload-controls">
                            <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;">
                            <button type="button" class="btn-secondary-modern"
                                onclick="document.getElementById('photoInput').click()">
                                <i class="fas fa-upload"></i> Seleccionar foto
                            </button>
                            @if (isset($person) && $person->photo)
                                <button type="button" class="btn-danger-modern" onclick="removePhoto()">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            @endif
                        </div>
                        <small class="form-text">Formatos permitidos: JPG, PNG, GIF. Máximo 2MB</small>
                    </div>
                </div>

                <!-- Datos Personales -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user"></i>
                        <h3>Datos Personales</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Nombre *</label>
                            <input type="text" name="name" class="input-modern"
                                value="{{ old('name', $person->name ?? '') }}" required>
                        </div>
                        <div class="form-group-modern">
                            <label>Apellido</label>
                            <input type="text" name="lastname" class="input-modern"
                                value="{{ old('lastname', $person->lastname ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Cédula / Documento</label>
                            <input type="text" name="document_id" class="input-modern"
                                value="{{ old('document_id', $person->document_id ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Correo electrónico</label>
                            <input type="email" name="email" class="input-modern"
                                value="{{ old('email', $person->email ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono</label>
                            <input type="text" name="phone" class="input-modern"
                                value="{{ old('phone', $person->phone ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Género</label>
                            <select name="gender" class="input-modern">
                                <option value="">Seleccionar</option>
                                <option value="male"
                                    {{ old('gender', $person->gender ?? '') == 'male' ? 'selected' : '' }}>Masculino
                                </option>
                                <option value="female"
                                    {{ old('gender', $person->gender ?? '') == 'female' ? 'selected' : '' }}>Femenino
                                </option>
                                <option value="other"
                                    {{ old('gender', $person->gender ?? '') == 'other' ? 'selected' : '' }}>Otro</option>
                            </select>
                        </div>
                        <div class="form-group-modern">
                            <label>Fecha de nacimiento</label>
                            <input type="date" name="birth_date" class="input-modern"
                                value="{{ old('birth_date', $person->birth_date ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- Empresa / Colegio -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3 id="companyLabel">Empresa</h3>
                    </div>
                    <div class="form-group-modern">
                        <select name="company_id" class="input-modern" required>
                            <option value="">Seleccione
                                {{ isset($person) && $person->category == 'school' ? 'el colegio' : 'la empresa' }}
                            </option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" data-type="{{ $company->type }}"
                                    {{ old('company_id', $person->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->type == 'company' ? 'Empresa' : 'Colegio' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Campos para Empleado -->
                <div class="form-section employee-fields">
                    <div class="section-title">
                        <i class="fas fa-briefcase"></i>
                        <h3>Información Laboral</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo / Posición</label>
                            <input type="text" name="position" class="input-modern"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Gerente, Analista">
                        </div>
                        <div class="form-group-modern">
                            <label>Departamento</label>
                            <select name="department" class="input-modern">
                                <option value="">Seleccionar departamento</option>
                                <option value="Administración">Administración</option>
                                <option value="Tecnología">Tecnología</option>
                                <option value="Ventas">Ventas</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Recursos Humanos">Recursos Humanos</option>
                                <option value="Operaciones">Operaciones</option>
                            </select>
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern" rows="3"
                                placeholder="Experiencia profesional, educación, logros...">{{ old('bio', $person->bio ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Campos para Estudiante -->
                <div class="form-section student-fields" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Información Académica</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Grado *</label>
                            <select name="grade_level" class="input-modern">
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
                        <div class="form-group-modern">
                            <label>Año Escolar *</label>
                            <input type="text" name="academic_year" class="input-modern" placeholder="Ej: 2024-2025"
                                value="{{ old('academic_year', $person->academic_year ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Periodo Actual</label>
                            <select name="period" class="input-modern">
                                <option value="">Seleccionar periodo</option>
                                <option value="first">Primer Lapso</option>
                                <option value="second">Segundo Lapso</option>
                                <option value="third">Tercer Lapso</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-ambulance"></i>
                        <h3>Información de Emergencia</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Contacto de emergencia *</label>
                            <input type="text" name="emergency_contact_name" class="input-modern"
                                value="{{ old('emergency_contact_name', $person->emergency_contact_name ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono de emergencia *</label>
                            <input type="text" name="emergency_phone" class="input-modern"
                                value="{{ old('emergency_phone', $person->emergency_phone ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Alergias</label>
                            <textarea name="allergies" class="input-modern" rows="2" placeholder="Ej: Penicilina, Mariscos, Polen...">{{ old('allergies', $person->allergies ?? '') }}</textarea>
                        </div>
                        <div class="form-group-modern">
                            <label>Condiciones médicas</label>
                            <textarea name="medical_conditions" class="input-modern" rows="2"
                                placeholder="Ej: Asma, Diabetes, Hipertensión...">{{ old('medical_conditions', $person->medical_conditions ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Campos para Docente -->
                <div class="form-section teacher-fields" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-chalkboard-user"></i>
                        <h3>Información Docente</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo / Especialidad</label>
                            <input type="text" name="position" class="input-modern"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Matemáticas, Ciencias">
                        </div>
                        <div class="form-group-modern">
                            <label>Tipo de docente</label>
                            <select name="teacher_type" class="input-modern">
                                <option value="">Seleccionar tipo</option>
                                <option value="regular">Docente Regular</option>
                                <option value="substitute">Docente Suplente</option>
                                <option value="special_education">Educación Especial</option>
                                <option value="part_time">Medio Tiempo</option>
                            </select>
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern" rows="3"
                                placeholder="Experiencia profesional, formación académica...">{{ old('bio', $person->bio ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-ambulance"></i>
                        <h3>Información de Emergencia</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Contacto de emergencia</label>
                            <input type="text" name="emergency_contact_name" class="input-modern"
                                value="{{ old('emergency_contact_name', $person->emergency_contact_name ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono de emergencia</label>
                            <input type="text" name="emergency_phone" class="input-modern"
                                value="{{ old('emergency_phone', $person->emergency_phone ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- Campos para Administrativo -->
                <div class="form-section administrative-fields" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3>Información Administrativa</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo</label>
                            <input type="text" name="position" class="input-modern"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Coordinador, Secretario">
                        </div>
                        <div class="form-group-modern">
                            <label>Departamento</label>
                            <select name="department" class="input-modern">
                                <option value="">Seleccionar departamento</option>
                                <option value="Dirección">Dirección</option>
                                <option value="Secretaría">Secretaría</option>
                                <option value="Contabilidad">Contabilidad</option>
                                <option value="Orientación">Orientación</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern" rows="3" placeholder="Experiencia profesional...">{{ old('bio', $person->bio ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-ambulance"></i>
                        <h3>Información de Emergencia</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Contacto de emergencia</label>
                            <input type="text" name="emergency_contact_name" class="input-modern"
                                value="{{ old('emergency_contact_name', $person->emergency_contact_name ?? '') }}">
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono de emergencia</label>
                            <input type="text" name="emergency_phone" class="input-modern"
                                value="{{ old('emergency_phone', $person->emergency_phone ?? '') }}">
                        </div>
                    </div>
                </div>

                <!-- Crear Usuario -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-user-plus"></i>
                        <h3>Acceso al Sistema</h3>
                    </div>
                    <div class="form-group-modern">
                        <label class="checkbox-label">
                            <input type="checkbox" name="create_user" value="1"
                                {{ old('create_user') ? 'checked' : '' }}>
                            <span>Crear usuario para acceso al sistema</span>
                        </label>
                        <small class="form-text">Se creará un usuario con rol según la categoría seleccionada</small>
                    </div>
                </div>

                <!-- Horarios -->
                <div class="form-section schedule-fields" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-clock"></i>
                        <h3>Horarios</h3>
                    </div>
                    <div id="scheduleContainer">
                        @if (isset($person) && $person->schedules->count() > 0)
                            @foreach ($person->schedules as $index => $schedule)
                                <div class="schedule-row">
                                    <select name="schedule[{{ $index }}][day]" class="input-modern">
                                        <option value="">Día</option>
                                        <option value="monday" {{ $schedule->day == 'monday' ? 'selected' : '' }}>Lunes
                                        </option>
                                        <option value="tuesday" {{ $schedule->day == 'tuesday' ? 'selected' : '' }}>Martes
                                        </option>
                                        <option value="wednesday" {{ $schedule->day == 'wednesday' ? 'selected' : '' }}>
                                            Miércoles</option>
                                        <option value="thursday" {{ $schedule->day == 'thursday' ? 'selected' : '' }}>
                                            Jueves</option>
                                        <option value="friday" {{ $schedule->day == 'friday' ? 'selected' : '' }}>Viernes
                                        </option>
                                    </select>
                                    <input type="time" name="schedule[{{ $index }}][start_time]"
                                        value="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}"
                                        placeholder="Hora inicio">
                                    <input type="time" name="schedule[{{ $index }}][end_time]"
                                        value="{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                                        placeholder="Hora fin">
                                    <input type="text" name="schedule[{{ $index }}][subject]"
                                        value="{{ $schedule->subject }}" placeholder="Materia/Actividad">
                                    <input type="text" name="schedule[{{ $index }}][classroom]"
                                        value="{{ $schedule->classroom }}" placeholder="Aula">
                                    <button type="button" class="btn-remove-schedule">-</button>
                                </div>
                            @endforeach
                        @else
                            <div class="schedule-row">
                                <select name="schedule[0][day]" class="input-modern">
                                    <option value="">Día</option>
                                    <option value="monday">Lunes</option>
                                    <option value="tuesday">Martes</option>
                                    <option value="wednesday">Miércoles</option>
                                    <option value="thursday">Jueves</option>
                                    <option value="friday">Viernes</option>
                                </select>
                                <input type="time" name="schedule[0][start_time]" placeholder="Hora inicio">
                                <input type="time" name="schedule[0][end_time]" placeholder="Hora fin">
                                <input type="text" name="schedule[0][subject]" placeholder="Materia/Actividad">
                                <input type="text" name="schedule[0][classroom]" placeholder="Aula">
                                <button type="button" class="btn-add-schedule">+</button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="form-actions">
                    <a href="{{ route('admin.persons.index') }}" class="btn-secondary-modern">
                        <i class="fas fa-arrow-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn-primary-modern">
                        <i class="fas fa-save"></i> {{ isset($person) ? 'Actualizar' : 'Registrar' }} Persona
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/persons-form.css') }}">
@endpush

@push('scripts')
    <script>
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

        function hideAllCategoryFields() {
            if (employeeFields) employeeFields.classList.remove('active');
            if (studentFields) studentFields.classList.remove('active');
            if (teacherFields) teacherFields.classList.remove('active');
            if (administrativeFields) administrativeFields.classList.remove('active');
            if (scheduleFields) scheduleFields.classList.remove('active');
        }

        function enableFormFields(container) {
            if (!container) return;
            const fields = container.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                field.disabled = false;
                field.removeAttribute('disabled');
            });
        }

        function disableFormFields(container) {
            if (!container) return;
            const fields = container.querySelectorAll('input, select, textarea');
            fields.forEach(field => {
                field.disabled = true;
            });
        }

        function updateFormByCategory() {
            const selectedCategory = document.querySelector('input[name="category"]:checked')?.value;
            const selectedSubcategory = subcategoryInput.value;

            hideAllCategoryFields();

            if (selectedCategory === 'employee') {
                companyLabel.innerHTML = 'Empresa *';
                if (employeeFields) {
                    employeeFields.classList.add('active');
                    enableFormFields(employeeFields);
                }
                if (scheduleFields) {
                    scheduleFields.classList.remove('active');
                }
                subcategorySection.style.display = 'none';
                subcategoryInput.value = '';
                subcategoryOptions.forEach(opt => opt.classList.remove('active'));
            } else if (selectedCategory === 'school') {
                companyLabel.innerHTML = 'Colegio *';
                subcategorySection.style.display = 'block';

                if (selectedSubcategory === 'student') {
                    if (studentFields) {
                        studentFields.classList.add('active');
                        enableFormFields(studentFields);
                    }
                    if (scheduleFields) {
                        scheduleFields.classList.add('active');
                        enableFormFields(scheduleFields);
                    }
                } else if (selectedSubcategory === 'teacher') {
                    if (teacherFields) {
                        teacherFields.classList.add('active');
                        enableFormFields(teacherFields);
                    }
                    if (scheduleFields) {
                        scheduleFields.classList.add('active');
                        enableFormFields(scheduleFields);
                    }
                } else if (selectedSubcategory === 'administrative') {
                    if (administrativeFields) {
                        administrativeFields.classList.add('active');
                        enableFormFields(administrativeFields);
                    }
                    if (scheduleFields) {
                        scheduleFields.classList.add('active');
                        enableFormFields(scheduleFields);
                    }
                }
            }
        }

        // ============================================
        // EVENTOS
        // ============================================

        categoryRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'school') {
                    subcategorySection.style.display = 'block';
                } else {
                    subcategorySection.style.display = 'none';
                    subcategoryInput.value = '';
                    subcategoryOptions.forEach(opt => opt.classList.remove('active'));
                    updateFormByCategory();
                }
                updateFormByCategory();
            });
        });

        subcategoryOptions.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.dataset.subcategory;
                subcategoryInput.value = value;
                subcategoryOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                updateFormByCategory();
            });
        });

        // ============================================
        // HORARIOS
        // ============================================

        const addButton = document.querySelector('.btn-add-schedule');
        if (addButton) {
            const newAddButton = addButton.cloneNode(true);
            addButton.parentNode.replaceChild(newAddButton, addButton);

            newAddButton.addEventListener('click', function() {
                const container = document.getElementById('scheduleContainer');
                const newRow = document.createElement('div');
                newRow.className = 'schedule-row';
                newRow.innerHTML = `
                <select name="schedule[${scheduleIndex}][day]" class="input-modern">
                    <option value="">Día</option>
                    <option value="monday">Lunes</option>
                    <option value="tuesday">Martes</option>
                    <option value="wednesday">Miércoles</option>
                    <option value="thursday">Jueves</option>
                    <option value="friday">Viernes</option>
                </select>
                <input type="time" name="schedule[${scheduleIndex}][start_time]" placeholder="Hora inicio">
                <input type="time" name="schedule[${scheduleIndex}][end_time]" placeholder="Hora fin">
                <input type="text" name="schedule[${scheduleIndex}][subject]" placeholder="Materia/Actividad">
                <input type="text" name="schedule[${scheduleIndex}][classroom]" placeholder="Aula">
                <button type="button" class="btn-remove-schedule">-</button>
            `;
                container.appendChild(newRow);
                scheduleIndex++;

                const removeBtn = newRow.querySelector('.btn-remove-schedule');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        newRow.remove();
                    });
                }
            });
        }

        document.querySelectorAll('.btn-remove-schedule').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.closest('.schedule-row').remove();
            });
        });

        // ============================================
        // FOTO DE PERFIL
        // ============================================

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
                            preview.innerHTML =
                                `<img src="${event.target.result}" alt="Vista previa" style="width: 100%; height: 100%; object-fit: cover;">`;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function removePhoto() {
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
        }

        // ============================================
        // FORZAR EL ENVÍO DE POSITION Y DEPARTMENT
        // ============================================

        const personForm = document.getElementById('personForm');
        if (personForm) {
            personForm.addEventListener('submit', function(e) {
                console.log('=== FORMULARIO A PUNTO DE ENVIARSE ===');

                // Buscar los campos
                const positionField = document.querySelector('input[name="position"]');
                const departmentField = document.querySelector('select[name="department"]');

                console.log('Position field existe:', !!positionField);
                console.log('Position field value:', positionField ? positionField.value : 'NO EXISTE');
                console.log('Department field existe:', !!departmentField);
                console.log('Department field value:', departmentField ? departmentField.value : 'NO EXISTE');

                // Forzar que los campos estén habilitados
                if (positionField) {
                    positionField.disabled = false;
                    positionField.removeAttribute('disabled');

                    // Si está vacío, asignar un valor por defecto
                    if (!positionField.value || positionField.value.trim() === '') {
                        positionField.value = 'Sin especificar';
                        console.log('Position vacío, asignado: Sin especificar');
                    }
                }

                if (departmentField) {
                    departmentField.disabled = false;
                    departmentField.removeAttribute('disabled');

                    // Si está vacío, asignar un valor por defecto
                    if (!departmentField.value || departmentField.value === '') {
                        departmentField.value = 'Sin especificar';
                        console.log('Department vacío, asignado: Sin especificar');
                    }
                }

                console.log('VALORES FINALES - Position:', positionField?.value);
                console.log('VALORES FINALES - Department:', departmentField?.value);

                return true;
            });
        }

        // ============================================
        // VERIFICAR CAMPOS AL CARGAR
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== VERIFICACIÓN DE CAMPOS AL CARGAR ===');

            const positionField = document.querySelector('input[name="position"]');
            const departmentField = document.querySelector('select[name="department"]');

            console.log('Position field existe:', !!positionField);
            console.log('Position field disabled:', positionField?.disabled);
            console.log('Position field value:', positionField?.value);
            console.log('Department field existe:', !!departmentField);
            console.log('Department field disabled:', departmentField?.disabled);
            console.log('Department field value:', departmentField?.value);

            // Inicializar categoría
            const selectedCategory = document.querySelector('input[name="category"]:checked');
            if (selectedCategory) {
                if (selectedCategory.value === 'school') {
                    subcategorySection.style.display = 'block';
                    const selectedSub = subcategoryInput.value;
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

            // Verificar estructura del formulario
            console.log('=== ESTRUCTURA DEL FORMULARIO ===');
            if (personForm) {
                const allInputs = personForm.querySelectorAll('input, select, textarea');
                allInputs.forEach(input => {
                    if (input.name === 'position' || input.name === 'department') {
                        console.log(
                            `- ${input.name}: ${input.value} (tipo: ${input.tagName}, disabled: ${input.disabled})`
                            );
                    }
                });
            }
        });
    </script>
@endpush
