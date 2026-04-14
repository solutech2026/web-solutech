@extends('layouts.admin')

@section('title', isset($person) ? 'Editar Persona' : 'Nueva Persona')

@section('header', isset($person) ? 'Editar Persona' : 'Registrar Persona')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/persons-form.css') }}">
@endpush

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
            @if ($errors->any())
                <div class="alert alert-danger-modern">
                    <i class="fas fa-exclamation-circle"></i>
                    <div class="alert-content">
                        <strong>Por favor, corrija los siguientes errores:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

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
                        <div class="category-option {{ old('category', $person->category ?? '') == 'employee' ? 'active' : '' }}" data-category="employee">
                            <div class="category-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="category-info">
                                <h4>Empleado</h4>
                                <p>Para personas que trabajan en empresas</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="category" value="employee" id="categoryEmployee"
                                    {{ old('category', $person->category ?? '') == 'employee' ? 'checked' : '' }} required>
                                <label for="categoryEmployee"></label>
                            </div>
                        </div>
                        <div class="category-option {{ old('category', $person->category ?? '') == 'school' ? 'active' : '' }}" data-category="school">
                            <div class="category-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="category-info">
                                <h4>Personal Escolar</h4>
                                <p>Para estudiantes, docentes o personal administrativo</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="category" value="school" id="categorySchool"
                                    {{ old('category', $person->category ?? '') == 'school' ? 'checked' : '' }} required>
                                <label for="categorySchool"></label>
                            </div>
                        </div>
                    </div>
                    @error('category')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Subcategoría (solo para escolar) -->
                <div class="form-section" id="subcategorySection" style="{{ old('category', $person->category ?? '') == 'school' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-layer-group"></i>
                        <h3>Rol en el Colegio</h3>
                    </div>
                    <div class="subcategory-selector">
                        <div class="subcategory-option {{ old('subcategory', $person->subcategory ?? '') == 'student' ? 'active' : '' }}" data-subcategory="student">
                            <i class="fas fa-graduation-cap"></i>
                            <span>Estudiante</span>
                        </div>
                        <div class="subcategory-option {{ old('subcategory', $person->subcategory ?? '') == 'teacher' ? 'active' : '' }}" data-subcategory="teacher">
                            <i class="fas fa-chalkboard-user"></i>
                            <span>Docente</span>
                        </div>
                        <div class="subcategory-option {{ old('subcategory', $person->subcategory ?? '') == 'administrative' ? 'active' : '' }}" data-subcategory="administrative">
                            <i class="fas fa-building"></i>
                            <span>Administrativo</span>
                        </div>
                    </div>
                    <input type="hidden" name="subcategory" id="subcategoryInput" value="{{ old('subcategory', $person->subcategory ?? '') }}">
                    @error('subcategory')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Foto de Perfil -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-camera"></i>
                        <h3>Foto de Perfil</h3>
                    </div>
                    <div class="photo-upload-container">
                        <div class="current-photo" id="currentPhotoPreview">
                            @if (isset($person) && $person->photo && !old('_token'))
                                <img src="{{ asset('storage/' . $person->photo) }}" alt="Foto actual">
                            @elseif (old('photo'))
                                <img src="{{ old('photo') }}" alt="Vista previa">
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
                    @error('photo')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
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
                            <input type="text" name="name" class="input-modern @error('name') is-invalid @enderror"
                                value="{{ old('name', $person->name ?? '') }}" required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Apellido</label>
                            <input type="text" name="lastname" class="input-modern @error('lastname') is-invalid @enderror"
                                value="{{ old('lastname', $person->lastname ?? '') }}">
                            @error('lastname')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Cédula / Documento</label>
                            <input type="text" name="document_id" class="input-modern @error('document_id') is-invalid @enderror"
                                value="{{ old('document_id', $person->document_id ?? '') }}">
                            @error('document_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Correo electrónico</label>
                            <input type="email" name="email" class="input-modern @error('email') is-invalid @enderror"
                                value="{{ old('email', $person->email ?? '') }}">
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono</label>
                            <input type="text" name="phone" class="input-modern @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $person->phone ?? '') }}">
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Género</label>
                            <select name="gender" class="input-modern @error('gender') is-invalid @enderror">
                                <option value="">Seleccionar</option>
                                <option value="male" {{ old('gender', $person->gender ?? '') == 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ old('gender', $person->gender ?? '') == 'female' ? 'selected' : '' }}>Femenino</option>
                                <option value="other" {{ old('gender', $person->gender ?? '') == 'other' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('gender')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Fecha de nacimiento</label>
                            <input type="date" name="birth_date" class="input-modern @error('birth_date') is-invalid @enderror"
                                value="{{ old('birth_date', $person->birth_date ?? '') }}">
                            @error('birth_date')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Empresa / Colegio -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3 id="companyLabel">{{ old('category', $person->category ?? '') == 'school' ? 'Colegio' : 'Empresa' }}</h3>
                    </div>
                    <div class="form-group-modern">
                        <select name="company_id" class="input-modern @error('company_id') is-invalid @enderror" required>
                            <option value="">Seleccione {{ old('category', $person->category ?? '') == 'school' ? 'el colegio' : 'la empresa' }}</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" data-type="{{ $company->type }}"
                                    {{ old('company_id', $person->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }} ({{ $company->type == 'company' ? 'Empresa' : 'Colegio' }})
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Campos para Empleado -->
                <div class="form-section employee-fields" style="{{ old('category', $person->category ?? '') == 'employee' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-briefcase"></i>
                        <h3>Información Laboral</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo / Posición</label>
                            <input type="text" name="position" class="input-modern @error('position') is-invalid @enderror"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Gerente, Analista">
                            @error('position')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Departamento</label>
                            <select name="department" class="input-modern @error('department') is-invalid @enderror">
                                <option value="">Seleccionar departamento</option>
                                <option value="Administración" {{ old('department', $person->department ?? '') == 'Administración' ? 'selected' : '' }}>Administración</option>
                                <option value="Tecnología" {{ old('department', $person->department ?? '') == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                                <option value="Ventas" {{ old('department', $person->department ?? '') == 'Ventas' ? 'selected' : '' }}>Ventas</option>
                                <option value="Marketing" {{ old('department', $person->department ?? '') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                <option value="Recursos Humanos" {{ old('department', $person->department ?? '') == 'Recursos Humanos' ? 'selected' : '' }}>Recursos Humanos</option>
                                <option value="Operaciones" {{ old('department', $person->department ?? '') == 'Operaciones' ? 'selected' : '' }}>Operaciones</option>
                            </select>
                            @error('department')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern @error('bio') is-invalid @enderror" rows="3"
                                placeholder="Experiencia profesional, educación, logros...">{{ old('bio', $person->bio ?? '') }}</textarea>
                            @error('bio')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Campos para Estudiante -->
                <div class="form-section student-fields" style="{{ old('category', $person->category ?? '') == 'school' && old('subcategory', $person->subcategory ?? '') == 'student' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Información Académica</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Grado *</label>
                            <select name="grade_level" class="input-modern @error('grade_level') is-invalid @enderror">
                                <option value="">Seleccionar grado</option>
                                <optgroup label="Primaria">
                                    <option value="1st" {{ old('grade_level', $person->grade_level ?? '') == '1st' ? 'selected' : '' }}>1er Grado</option>
                                    <option value="2nd" {{ old('grade_level', $person->grade_level ?? '') == '2nd' ? 'selected' : '' }}>2do Grado</option>
                                    <option value="3rd" {{ old('grade_level', $person->grade_level ?? '') == '3rd' ? 'selected' : '' }}>3er Grado</option>
                                    <option value="4th" {{ old('grade_level', $person->grade_level ?? '') == '4th' ? 'selected' : '' }}>4to Grado</option>
                                    <option value="5th" {{ old('grade_level', $person->grade_level ?? '') == '5th' ? 'selected' : '' }}>5to Grado</option>
                                    <option value="6th" {{ old('grade_level', $person->grade_level ?? '') == '6th' ? 'selected' : '' }}>6to Grado</option>
                                </optgroup>
                                <optgroup label="Liceo / Secundaria">
                                    <option value="7th" {{ old('grade_level', $person->grade_level ?? '') == '7th' ? 'selected' : '' }}>1er Año</option>
                                    <option value="8th" {{ old('grade_level', $person->grade_level ?? '') == '8th' ? 'selected' : '' }}>2do Año</option>
                                    <option value="9th" {{ old('grade_level', $person->grade_level ?? '') == '9th' ? 'selected' : '' }}>3er Año</option>
                                </optgroup>
                                <optgroup label="Ciclo Diversificado">
                                    <option value="10th" {{ old('grade_level', $person->grade_level ?? '') == '10th' ? 'selected' : '' }}>4to Año</option>
                                    <option value="11th" {{ old('grade_level', $person->grade_level ?? '') == '11th' ? 'selected' : '' }}>5to Año</option>
                                </optgroup>
                            </select>
                            @error('grade_level')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Año Escolar *</label>
                            <input type="text" name="academic_year" class="input-modern @error('academic_year') is-invalid @enderror" 
                                placeholder="Ej: 2024-2025" value="{{ old('academic_year', $person->academic_year ?? '') }}">
                            @error('academic_year')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Periodo Actual</label>
                            <select name="period" class="input-modern @error('period') is-invalid @enderror">
                                <option value="">Seleccionar periodo</option>
                                <option value="first" {{ old('period', $person->period ?? '') == 'first' ? 'selected' : '' }}>Primer Lapso</option>
                                <option value="second" {{ old('period', $person->period ?? '') == 'second' ? 'selected' : '' }}>Segundo Lapso</option>
                                <option value="third" {{ old('period', $person->period ?? '') == 'third' ? 'selected' : '' }}>Tercer Lapso</option>
                            </select>
                            @error('period')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="section-title mt-4">
                        <i class="fas fa-ambulance"></i>
                        <h3>Información de Emergencia</h3>
                        <small class="text-muted">Opcional pero recomendado</small>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Contacto de emergencia</label>
                            <input type="text" name="emergency_contact_name" class="input-modern @error('emergency_contact_name') is-invalid @enderror"
                                value="{{ old('emergency_contact_name', $person->emergency_contact_name ?? '') }}"
                                placeholder="Ej: Juan Pérez">
                            @error('emergency_contact_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono de emergencia</label>
                            <input type="text" name="emergency_phone" class="input-modern @error('emergency_phone') is-invalid @enderror"
                                value="{{ old('emergency_phone', $person->emergency_phone ?? '') }}"
                                placeholder="Ej: 0412-1234567">
                            @error('emergency_phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Alergias</label>
                            <textarea name="allergies" class="input-modern @error('allergies') is-invalid @enderror" rows="2" 
                                placeholder="Ej: Penicilina, Mariscos, Polen...">{{ old('allergies', $person->allergies ?? '') }}</textarea>
                            @error('allergies')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Condiciones médicas</label>
                            <textarea name="medical_conditions" class="input-modern @error('medical_conditions') is-invalid @enderror" rows="2"
                                placeholder="Ej: Asma, Diabetes, Hipertensión...">{{ old('medical_conditions', $person->medical_conditions ?? '') }}</textarea>
                            @error('medical_conditions')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Campos para Docente -->
                <div class="form-section teacher-fields" style="{{ old('category', $person->category ?? '') == 'school' && old('subcategory', $person->subcategory ?? '') == 'teacher' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-chalkboard-user"></i>
                        <h3>Información Docente</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo / Especialidad</label>
                            <input type="text" name="position" class="input-modern @error('position') is-invalid @enderror"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Matemáticas, Ciencias">
                            @error('position')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Tipo de docente</label>
                            <select name="teacher_type" class="input-modern @error('teacher_type') is-invalid @enderror">
                                <option value="">Seleccionar tipo</option>
                                <option value="regular" {{ old('teacher_type', $person->teacher_type ?? '') == 'regular' ? 'selected' : '' }}>Docente Regular</option>
                                <option value="substitute" {{ old('teacher_type', $person->teacher_type ?? '') == 'substitute' ? 'selected' : '' }}>Docente Suplente</option>
                                <option value="special_education" {{ old('teacher_type', $person->teacher_type ?? '') == 'special_education' ? 'selected' : '' }}>Educación Especial</option>
                                <option value="part_time" {{ old('teacher_type', $person->teacher_type ?? '') == 'part_time' ? 'selected' : '' }}>Medio Tiempo</option>
                            </select>
                            @error('teacher_type')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern @error('bio') is-invalid @enderror" rows="3"
                                placeholder="Experiencia profesional, formación académica...">{{ old('bio', $person->bio ?? '') }}</textarea>
                            @error('bio')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
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
                <div class="form-section administrative-fields" style="{{ old('category', $person->category ?? '') == 'school' && old('subcategory', $person->subcategory ?? '') == 'administrative' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3>Información Administrativa</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Cargo</label>
                            <input type="text" name="position" class="input-modern @error('position') is-invalid @enderror"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Coordinador, Secretario">
                            @error('position')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Departamento</label>
                            <select name="department" class="input-modern @error('department') is-invalid @enderror">
                                <option value="">Seleccionar departamento</option>
                                <option value="Dirección" {{ old('department', $person->department ?? '') == 'Dirección' ? 'selected' : '' }}>Dirección</option>
                                <option value="Secretaría" {{ old('department', $person->department ?? '') == 'Secretaría' ? 'selected' : '' }}>Secretaría</option>
                                <option value="Contabilidad" {{ old('department', $person->department ?? '') == 'Contabilidad' ? 'selected' : '' }}>Contabilidad</option>
                                <option value="Orientación" {{ old('department', $person->department ?? '') == 'Orientación' ? 'selected' : '' }}>Orientación</option>
                                <option value="Mantenimiento" {{ old('department', $person->department ?? '') == 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            </select>
                            @error('department')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Biografía</label>
                            <textarea name="bio" class="input-modern @error('bio') is-invalid @enderror" rows="3" 
                                placeholder="Experiencia profesional...">{{ old('bio', $person->bio ?? '') }}</textarea>
                            @error('bio')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
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
                <div class="form-section schedule-fields" style="{{ (old('category', $person->category ?? '') == 'school' && in_array(old('subcategory', $person->subcategory ?? ''), ['student', 'teacher', 'administrative'])) ? 'display: block;' : 'display: none;' }}">
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
                                        <option value="monday" {{ $schedule->day == 'monday' ? 'selected' : '' }}>Lunes</option>
                                        <option value="tuesday" {{ $schedule->day == 'tuesday' ? 'selected' : '' }}>Martes</option>
                                        <option value="wednesday" {{ $schedule->day == 'wednesday' ? 'selected' : '' }}>Miércoles</option>
                                        <option value="thursday" {{ $schedule->day == 'thursday' ? 'selected' : '' }}>Jueves</option>
                                        <option value="friday" {{ $schedule->day == 'friday' ? 'selected' : '' }}>Viernes</option>
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

@push('scripts')
    <script src="{{ asset('js/person-form.js') }}"></script>
@endpush
