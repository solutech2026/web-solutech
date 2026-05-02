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

                <!-- Logo de la Organización -->
                <div class="form-section organization-logo-section" style="display: none;">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3>Logo de la Organización</h3>
                    </div>
                    <div class="photo-upload-container">
                        <div class="current-photo" id="organizationLogoPreview">
                            <div class="photo-placeholder">
                                <i class="fas fa-building"></i>
                                <span>Logo de la organización</span>
                            </div>
                        </div>
                        <div class="photo-upload-controls">
                            <input type="file" name="organization_logo" id="organizationLogoInput" accept="image/*" style="display: none;">
                            <button type="button" class="btn-secondary-modern" onclick="document.getElementById('organizationLogoInput').click()">
                                <i class="fas fa-upload"></i> Seleccionar logo
                            </button>
                        </div>
                        <small class="form-text">Formatos permitidos: JPG, PNG. Tamaño recomendado: 200x200px</small>
                    </div>
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
                            <label>Cédula / ID *</label>
                            <input type="text" name="document_id" class="input-modern @error('document_id') is-invalid @enderror"
                                value="{{ old('document_id', $person->document_id ?? '') }}" required>
                            @error('document_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Correo electrónico *</label>
                            <input type="email" name="email" class="input-modern @error('email') is-invalid @enderror"
                                value="{{ old('email', $person->email ?? '') }}" required>
                            @error('email')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Teléfono *</label>
                            <input type="text" name="phone" class="input-modern @error('phone') is-invalid @enderror"
                                value="{{ old('phone', $person->phone ?? '') }}" required>
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

                <!-- Tipo de Institución -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-building"></i>
                        <h3>Tipo de Organización</h3>
                    </div>
                    <div class="category-selector" id="institutionTypeSelector">
                        <div class="category-option {{ old('institution_type', $person->institution_type ?? '') == 'company' ? 'active' : '' }}" data-institution="company">
                            <div class="category-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="category-info">
                                <h4>Empresa</h4>
                                <p>Registro para empleados de empresa privada</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="institution_type" value="company" id="institutionCompany"
                                    {{ old('institution_type', $person->institution_type ?? '') == 'company' ? 'checked' : '' }}>
                                <label for="institutionCompany"></label>
                            </div>
                        </div>

                        <div class="category-option {{ old('institution_type', $person->institution_type ?? '') == 'school' ? 'active' : '' }}" data-institution="school">
                            <div class="category-icon">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="category-info">
                                <h4>Colegio</h4>
                                <p>Registro para personal escolar</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="institution_type" value="school" id="institutionSchool"
                                    {{ old('institution_type', $person->institution_type ?? '') == 'school' ? 'checked' : '' }}>
                                <label for="institutionSchool"></label>
                            </div>
                        </div>

                        <div class="category-option {{ old('institution_type', $person->institution_type ?? '') == 'ngo_rescue' ? 'active' : '' }}" data-institution="ngo_rescue">
                            <div class="category-icon">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="category-info">
                                <h4>ONG de Seguridad y Rescate</h4>
                                <p>Bomberos, Protección Civil, Defensa Civil, Cruz Roja, Voluntarios</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="institution_type" value="ngo_rescue" id="institutionNgoRescue"
                                    {{ old('institution_type', $person->institution_type ?? '') == 'ngo_rescue' ? 'checked' : '' }}>
                                <label for="institutionNgoRescue"></label>
                            </div>
                        </div>

                        <div class="category-option {{ old('institution_type', $person->institution_type ?? '') =='government' ? 'active' : '' }}" data-institution="government">
                            <div class="category-icon">
                                <i class="fas fa-landmark"></i>
                            </div>
                            <div class="category-info">
                                <h4>Organización Gubernamental</h4>
                                <p>Ministerios, Gobernaciones, Alcaldías, Poder Público, Asamblea Nacional</p>
                            </div>
                            <div class="category-radio">
                                <input type="radio" name="institution_type" value="government" id="institutionGovernment"
                                    {{ old('institution_type', $person->institution_type ?? '') == 'government' ? 'checked' : '' }}>
                                <label for="institutionGovernment"></label>
                            </div>
                        </div>
                    </div>
                    @error('institution_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Selección de Institución específica -->
                <div class="form-section" id="companySelectionSection">
                    <div class="form-group-modern">
                        <label id="companySelectLabel">Seleccione la institución</label>
                        <select name="company_id" class="input-modern @error('company_id') is-invalid @enderror" required>
                            <option value="">Seleccione</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" data-type="{{ $company->type }}"
                                    {{ old('company_id', $person->company_id ?? '') == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Campos para EMPRESA -->
                <div class="form-section company-fields" style="{{ old('institution_type', $person->institution_type ?? '') == 'company' ? 'display: block;' : 'display: none;' }}">
    <div class="section-title">
        <i class="fas fa-briefcase"></i>
        <h3>Información Laboral</h3>
    </div>
    <div class="form-grid-2cols">
        <div class="form-group-modern">
            <label>Cargo <span class="text-muted">(Opcional)</span></label>
            <select name="position" class="input-modern @error('position') is-invalid @enderror">
                <option value="">Sin especificar</option>
                @php
                    $positions = [
                        'Gerente General', 'Gerente de Ventas', 'Gerente de Marketing', 'Gerente de Operaciones',
                        'Coordinador', 'Supervisor', 'Analista Senior', 'Analista', 'Asistente',
                        'Desarrollador', 'Programador', 'Ingeniero de Software', 'Arquitecto de Software',
                        'Diseñador UX/UI', 'Administrador de Sistemas', 'Soporte Técnico',
                        'Contador', 'Asistente Contable', 'Recursos Humanos', 'Reclutador',
                        'Vendedor', 'Ejecutivo de Cuentas', 'Consultor', 'Especialista en Marketing',
                        'Community Manager', 'Diseñador Gráfico', 'Director de Proyectos'
                    ];
                @endphp
                @foreach($positions as $pos)
                    <option value="{{ $pos }}" {{ old('position', $person->position ?? '') == $pos ? 'selected' : '' }}>
                        {{ $pos }}
                    </option>
                @endforeach
            </select>
            <div class="input-action">
                <button type="button" class="btn-add-option" onclick="openAddModal('position', 'Cargo')">
                    <i class="fas fa-plus"></i> Agregar nuevo
                </button>
            </div>
            @error('position')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group-modern">
            <label>Área / Departamento <span class="text-muted">(Opcional)</span></label>
            <select name="department" class="input-modern @error('department') is-invalid @enderror">
                <option value="">Sin especificar</option>
                @php
                    $departments = [
                        'Administración', 'Recursos Humanos', 'Tecnología', 'Sistemas', 'TI',
                        'Ventas', 'Marketing', 'Operaciones', 'Logística', 'Compras',
                        'Finanzas', 'Contabilidad', 'Atención al Cliente', 'Soporte', 'Calidad',
                        'Producción', 'Mantenimiento', 'Seguridad', 'Legal', 'Investigación y Desarrollo'
                    ];
                @endphp
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ old('department', $person->department ?? '') == $dept ? 'selected' : '' }}>
                        {{ $dept }}
                    </option>
                @endforeach
            </select>
            <div class="input-action">
                <button type="button" class="btn-add-option" onclick="openAddModal('department', 'Departamento')">
                    <i class="fas fa-plus"></i> Agregar nuevo
                </button>
            </div>
            @error('department')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

                <!-- SECCIÓN DE EMERGENCIA (para todos) -->
                <div class="form-section emergency-section">
                    <div class="section-title">
                        <i class="fas fa-ambulance"></i>
                        <h3>Contacto de Emergencia</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Nombre del contacto *</label>
                            <input type="text" name="emergency_contact_name" class="input-modern @error('emergency_contact_name') is-invalid @enderror"
                                value="{{ old('emergency_contact_name', $person->emergency_contact_name ?? '') }}" required
                                placeholder="Nombre completo">
                            @error('emergency_contact_name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Número de contacto *</label>
                            <input type="text" name="emergency_phone" class="input-modern @error('emergency_phone') is-invalid @enderror"
                                value="{{ old('emergency_phone', $person->emergency_phone ?? '') }}" required
                                placeholder="Ej: 0412-1234567">
                            @error('emergency_phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Parentesco *</label>
                            <input type="text" name="emergency_relationship" class="input-modern @error('emergency_relationship') is-invalid @enderror"
                                value="{{ old('emergency_relationship', $person->emergency_relationship ?? '') }}" required
                                placeholder="Ej: Padre, Madre, Hermano, Tío">
                            @error('emergency_relationship')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DE SALUD (para todos) -->
                <div class="form-section health-section">
                    <div class="section-title">
                        <i class="fas fa-heartbeat"></i>
                        <h3>Información de Salud</h3>
                    </div>
                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Tipo de Sangre</label>
                            <select name="blood_type" class="input-modern @error('blood_type') is-invalid @enderror">
                                <option value="">Seleccionar</option>
                                <option value="A+" {{ old('blood_type', $person->blood_type ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type', $person->blood_type ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type', $person->blood_type ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type', $person->blood_type ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type', $person->blood_type ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type', $person->blood_type ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type', $person->blood_type ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type', $person->blood_type ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_type')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern">
                            <label>Enfermedades / Condiciones</label>
                            <textarea name="medical_conditions" class="input-modern @error('medical_conditions') is-invalid @enderror" rows="3"
                                placeholder="Ej: Diabetes, Hipertensión, Asma, Cardiopatías...">{{ old('medical_conditions', $person->medical_conditions ?? '') }}</textarea>
                            @error('medical_conditions')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group-modern full-width">
                            <label>Alergias</label>
                            <textarea name="allergies" class="input-modern @error('allergies') is-invalid @enderror" rows="2"
                                placeholder="Ej: Penicilina, Mariscos, Polen, Látex...">{{ old('allergies', $person->allergies ?? '') }}</textarea>
                            @error('allergies')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN PARA ONG DE SEGURIDAD Y RESCATE -->
                <div class="form-section orh-card-section" style="{{ old('institution_type', $person->institution_type ?? '') == 'ngo_rescue' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-id-card"></i>
                        <h3>Carnet de Identificación - Organización de Rescate</h3>
                        <small class="text-muted">Datos para la credencial de miembro</small>
                    </div>

                    <div class="carnet-style-preview">
                        <div class="carnet-header">
                            <i class="fas fa-shield-alt"></i>
                            <span>ORGANIZACIÓN DE RESCATE</span>
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="carnet-body-info">
                            <div class="carnet-row">
                                <span class="carnet-label">MIEMBRO N°:</span>
                                <span class="carnet-value" id="previewMemberNumber">________</span>
                            </div>
                            <div class="carnet-row">
                                <span class="carnet-label">CATEGORÍA:</span>
                                <span class="carnet-value" id="previewCategory">________</span>
                            </div>
                            <div class="carnet-row">
                                <span class="carnet-label">VENCE:</span>
                                <span class="carnet-value" id="previewExpiry">________</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Número de Miembro</label>
                            <input type="text" name="rescue_member_number" class="input-modern @error('rescue_member_number') is-invalid @enderror"
                                value="{{ old('rescue_member_number', $person->rescue_member_number ?? '') }}"
                                placeholder="Ej: 20, 001, 105" 
                                onkeyup="document.getElementById('previewMemberNumber').innerText = this.value || '________'">
                            <small class="form-text">Número único de identificación en la organización</small>
                            @error('rescue_member_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Categoría de Miembro</label>
                            <select name="rescue_member_category" class="input-modern @error('rescue_member_category') is-invalid @enderror" 
                                onchange="updatePreviewCategory(this)">
                                <option value="">Seleccionar categoría</option>
                                <option value="Operativo" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Operativo' ? 'selected' : '' }}>Operativo</option>
                                <option value="Técnico" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Técnico' ? 'selected' : '' }}>Técnico Especializado</option>
                                <option value="Médico" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Médico' ? 'selected' : '' }}>Médico / Paramédico</option>
                                <option value="Instructor" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Instructor' ? 'selected' : '' }}>Instructor / Formador</option>
                                <option value="Coordinador" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Coordinador' ? 'selected' : '' }}>Coordinador de Área</option>
                                <option value="Administrativo" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Administrativo' ? 'selected' : '' }}>Administrativo / Logístico</option>
                                <option value="Voluntario" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Voluntario' ? 'selected' : '' }}>Voluntario Activo</option>
                                <option value="Honorario" {{ old('rescue_member_category', $person->rescue_member_category ?? '') == 'Honorario' ? 'selected' : '' }}>Miembro Honorario</option>
                            </select>
                            <small class="form-text">Categoría o rol dentro de la organización de rescate</small>
                            @error('rescue_member_category')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Fecha de Vencimiento</label>
                            <input type="month" name="rescue_expiry_date" class="input-modern @error('rescue_expiry_date') is-invalid @enderror"
                                value="{{ old('rescue_expiry_date', $person->rescue_expiry_date ?? '') }}"
                                onchange="document.getElementById('previewExpiry').innerText = this.value.replace('-', '/') || '________'">
                            <small class="form-text">Mes y año de vencimiento del carnet</small>
                            @error('rescue_expiry_date')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Especialidad / Área de Trabajo</label>
                            <input type="text" name="rescue_specialty_area" class="input-modern @error('rescue_specialty_area') is-invalid @enderror"
                                value="{{ old('rescue_specialty_area', $person->rescue_specialty_area ?? '') }}"
                                placeholder="Ej: Rescate en Alturas, Búsqueda y Salvamento">
                            <small class="form-text">Especialidad o área específica de trabajo</small>
                            @error('rescue_specialty_area')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern full-width">
                            <label>Certificaciones</label>
                            <textarea name="rescue_certifications" class="input-modern @error('rescue_certifications') is-invalid @enderror" rows="2"
                                placeholder="Ej: RCP Avanzado, Manejo de Extintores, Rescate en Estructuras Colapsadas">{{ old('rescue_certifications', $person->rescue_certifications ?? '') }}</textarea>
                            @error('rescue_certifications')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN PARA ORGANIZACIONES GUBERNAMENTALES -->
                <div class="form-section government-fields" style="{{ old('institution_type', $person->institution_type ?? '') == 'government' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-landmark"></i>
                        <h3>Información Gubernamental</h3>
                    </div>

                    <div class="government-badge">
                        <i class="fas fa-flag-checkered"></i>
                        <div class="government-badge-content">
                            <strong>República Bolivariana de Venezuela</strong>
                            <small>Datos para el registro de funcionarios públicos</small>
                        </div>
                    </div>

                    <div class="form-grid-2cols">
                        <div class="form-group-modern">
                            <label>Nivel del Gobierno</label>
                            <select name="government_level" class="input-modern @error('government_level') is-invalid @enderror">
                                <option value="">Seleccionar nivel</option>
                                <option value="national" {{ old('government_level', $person->government_level ?? '') == 'national' ? 'selected' : '' }}>Nacional</option>
                                <option value="regional" {{ old('government_level', $person->government_level ?? '') == 'regional' ? 'selected' : '' }}>Regional</option>
                                <option value="municipal" {{ old('government_level', $person->government_level ?? '') == 'municipal' ? 'selected' : '' }}>Municipal</option>
                                <option value="parish" {{ old('government_level', $person->government_level ?? '') == 'parish' ? 'selected' : '' }}>Parroquial</option>
                            </select>
                            @error('government_level')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Rama del Poder Público</label>
                            <select name="government_branch" class="input-modern @error('government_branch') is-invalid @enderror">
                                <option value="">Seleccionar rama</option>
                                <option value="executive" {{ old('government_branch', $person->government_branch ?? '') == 'executive' ? 'selected' : '' }}>Poder Ejecutivo</option>
                                <option value="legislative" {{ old('government_branch', $person->government_branch ?? '') == 'legislative' ? 'selected' : '' }}>Poder Legislativo</option>
                                <option value="judicial" {{ old('government_branch', $person->government_branch ?? '') == 'judicial' ? 'selected' : '' }}>Poder Judicial</option>
                                <option value="citizen" {{ old('government_branch', $person->government_branch ?? '') == 'citizen' ? 'selected' : '' }}>Poder Ciudadano</option>
                                <option value="electoral" {{ old('government_branch', $person->government_branch ?? '') == 'electoral' ? 'selected' : '' }}>Poder Electoral</option>
                            </select>
                            @error('government_branch')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Ministerio / Ente / Instituto</label>
                            <select name="government_entity" class="input-modern @error('government_entity') is-invalid @enderror">
                                <option value="">Seleccionar ente</option>
                                <optgroup label="Ministerios">
                                    <option value="min_interior" {{ old('government_entity', $person->government_entity ?? '') == 'min_interior' ? 'selected' : '' }}>Ministerio del Interior</option>
                                    <option value="min_defensa" {{ old('government_entity', $person->government_entity ?? '') == 'min_defensa' ? 'selected' : '' }}>Ministerio de la Defensa</option>
                                    <option value="min_educacion" {{ old('government_entity', $person->government_entity ?? '') == 'min_educacion' ? 'selected' : '' }}>Ministerio de Educación</option>
                                    <option value="min_salud" {{ old('government_entity', $person->government_entity ?? '') == 'min_salud' ? 'selected' : '' }}>Ministerio de Salud</option>
                                </optgroup>
                                <optgroup label="Poder Legislativo">
                                    <option value="asamblea_nacional" {{ old('government_entity', $person->government_entity ?? '') == 'asamblea_nacional' ? 'selected' : '' }}>Asamblea Nacional</option>
                                    <option value="consejo_legislativo" {{ old('government_entity', $person->government_entity ?? '') == 'consejo_legislativo' ? 'selected' : '' }}>Consejo Legislativo</option>
                                </optgroup>
                                <optgroup label="Fuerza Armada">
                                    <option value="fanb" {{ old('government_entity', $person->government_entity ?? '') == 'fanb' ? 'selected' : '' }}>FANB</option>
                                    <option value="gnb" {{ old('government_entity', $person->government_entity ?? '') == 'gnb' ? 'selected' : '' }}>Guardia Nacional</option>
                                    <option value="cicpc" {{ old('government_entity', $person->government_entity ?? '') == 'cicpc' ? 'selected' : '' }}>CICPC</option>
                                </optgroup>
                            </select>
                            @error('government_entity')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Cargo / Jerarquía</label>
                            <select name="government_position" class="input-modern @error('government_position') is-invalid @enderror">
                                <option value="">Seleccionar cargo</option>
                                <option value="minister" {{ old('government_position', $person->government_position ?? '') == 'minister' ? 'selected' : '' }}>Ministro / Viceministro</option>
                                <option value="director" {{ old('government_position', $person->government_position ?? '') == 'director' ? 'selected' : '' }}>Director General</option>
                                <option value="governor" {{ old('government_position', $person->government_position ?? '') == 'governor' ? 'selected' : '' }}>Gobernador</option>
                                <option value="mayor" {{ old('government_position', $person->government_position ?? '') == 'mayor' ? 'selected' : '' }}>Alcalde</option>
                                <option value="deputy" {{ old('government_position', $person->government_position ?? '') == 'deputy' ? 'selected' : '' }}>Diputado</option>
                                <option value="official" {{ old('government_position', $person->government_position ?? '') == 'official' ? 'selected' : '' }}>Funcionario Público</option>
                            </select>
                            @error('government_position')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Número de Carnet / Credencial</label>
                            <input type="text" name="government_card_number" class="input-modern @error('government_card_number') is-invalid @enderror"
                                value="{{ old('government_card_number', $person->government_card_number ?? '') }}"
                                placeholder="Ej: 123456, GNB-001">
                            <small class="form-text">Número de identificación oficial</small>
                            @error('government_card_number')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group-modern">
                            <label>Fecha de Ingreso</label>
                            <input type="date" name="government_joining_date" class="input-modern @error('government_joining_date') is-invalid @enderror"
                                value="{{ old('government_joining_date', $person->government_joining_date ?? '') }}">
                            <small class="form-text">Fecha de ingreso al cargo</small>
                            @error('government_joining_date')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SECCIÓN DE COLEGIOS -->
                <div class="form-section school-fields" style="{{ old('institution_type', $person->institution_type ?? '') == 'school' ? 'display: block;' : 'display: none;' }}">
                    <div class="section-title">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Información Escolar</h3>
                    </div>
                    
                    <div class="form-group-modern">
                        <label>Rol en el colegio *</label>
                        <div class="subcategory-selector">
                            <div class="subcategory-option {{ old('subcategory', $person->subcategory ?? '') == 'student' ? 'active' : '' }}" data-subcategory="student">
                                <i class="fas fa-user-graduate"></i>
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

                    <div class="student-specific-fields" style="{{ old('subcategory', $person->subcategory ?? '') == 'student' ? 'display: block;' : 'display: none;' }}">
                        <div class="form-grid-2cols">
                            <div class="form-group-modern">
                                <label>Nivel / Grado</label>
                                <select name="grade_level" class="input-modern @error('grade_level') is-invalid @enderror">
                                    <option value="">Seleccionar grado</option>
                                    <optgroup label="EDUCACIÓN PRIMARIA">
                                        <option value="1er_grado" {{ old('grade_level', $person->grade_level ?? '') == '1er_grado' ? 'selected' : '' }}>1er Grado</option>
                                        <option value="2do_grado" {{ old('grade_level', $person->grade_level ?? '') == '2do_grado' ? 'selected' : '' }}>2do Grado</option>
                                        <option value="3er_grado" {{ old('grade_level', $person->grade_level ?? '') == '3er_grado' ? 'selected' : '' }}>3er Grado</option>
                                        <option value="4to_grado" {{ old('grade_level', $person->grade_level ?? '') == '4to_grado' ? 'selected' : '' }}>4to Grado</option>
                                        <option value="5to_grado" {{ old('grade_level', $person->grade_level ?? '') == '5to_grado' ? 'selected' : '' }}>5to Grado</option>
                                        <option value="6to_grado" {{ old('grade_level', $person->grade_level ?? '') == '6to_grado' ? 'selected' : '' }}>6to Grado</option>
                                    </optgroup>
                                    <optgroup label="EDUCACIÓN MEDIA GENERAL">
                                        <option value="7mo_grado" {{ old('grade_level', $person->grade_level ?? '') == '7mo_grado' ? 'selected' : '' }}>7mo Grado</option>
                                        <option value="8vo_grado" {{ old('grade_level', $person->grade_level ?? '') == '8vo_grado' ? 'selected' : '' }}>8vo Grado</option>
                                        <option value="9no_grado" {{ old('grade_level', $person->grade_level ?? '') == '9no_grado' ? 'selected' : '' }}>9no Grado</option>
                                    </optgroup>
                                    <optgroup label="EDUCACIÓN MEDIA DIVERSIFICADA">
                                        <option value="4to_ano" {{ old('grade_level', $person->grade_level ?? '') == '4to_ano' ? 'selected' : '' }}>4to Año</option>
                                        <option value="5to_ano" {{ old('grade_level', $person->grade_level ?? '') == '5to_ano' ? 'selected' : '' }}>5to Año</option>
                                    </optgroup>
                                </select>
                                @error('grade_level')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group-modern">
                                <label>Sección</label>
                                <input type="text" name="section" class="input-modern @error('section') is-invalid @enderror"
                                    placeholder="Ej: A, B, C" value="{{ old('section', $person->section ?? '') }}">
                                @error('section')
                                    <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group-modern">
                                <label>Año Escolar</label>
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
                            <i class="fas fa-file-alt"></i>
                            <h3>Boletines de Notas</h3>
                        </div>
                        <div class="grades-section">
                            <div class="form-grid-3cols">
                                <div class="form-group-modern">
                                    <label>1er Lapso</label>
                                    <input type="file" name="grade_report_first" accept=".pdf,.jpg,.png" class="input-modern">
                                </div>
                                <div class="form-group-modern">
                                    <label>2do Lapso</label>
                                    <input type="file" name="grade_report_second" accept=".pdf,.jpg,.png" class="input-modern">
                                </div>
                                <div class="form-group-modern">
                                    <label>3er Lapso</label>
                                    <input type="file" name="grade_report_third" accept=".pdf,.jpg,.png" class="input-modern">
                                </div>
                            </div>
                            <small class="form-text">Formatos: PDF, JPG, PNG. Máx: 5MB</small>
                        </div>
                    </div>

                    <div class="teacher-admin-fields" style="{{ in_array(old('subcategory', $person->subcategory ?? ''), ['teacher', 'administrative']) ? 'display: block;' : 'display: none;' }}">
                        <div class="form-group-modern">
                            <label>Cargo</label>
                            <input type="text" name="position" class="input-modern"
                                value="{{ old('position', $person->position ?? '') }}"
                                placeholder="Ej: Docente de Matemáticas, Coordinador">
                            @error('position')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
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

    <!-- Modal para agregar nueva opción -->
    <div class="modal fade" id="addOptionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Agregar nuevo
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group-modern">
                        <label id="modalFieldLabel">Nombre</label>
                        <input type="text" id="newOptionValue" class="input-modern" placeholder="Ingrese el valor">
                        <input type="hidden" id="currentFieldType" value="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn-primary-modern" onclick="saveNewOption()">Agregar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let currentField = '';
    let currentFieldLabel = '';
    
    function updatePreviewCategory(select) {
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('previewCategory').innerText = selectedOption.text || '________';
    }

    function initCarnetPreview() {
        const memberNumber = document.querySelector('input[name="rescue_member_number"]');
        const categorySelect = document.querySelector('select[name="rescue_member_category"]');
        const expiryDate = document.querySelector('input[name="rescue_expiry_date"]');
        
        if (memberNumber && memberNumber.value) {
            document.getElementById('previewMemberNumber').innerText = memberNumber.value;
        }
        if (categorySelect && categorySelect.value) {
            document.getElementById('previewCategory').innerText = categorySelect.options[categorySelect.selectedIndex]?.text || '________';
        }
        if (expiryDate && expiryDate.value) {
            document.getElementById('previewExpiry').innerText = expiryDate.value.replace('-', '/');
        }
    }
    
    function openAddModal(field, label) {
        currentField = field;
        currentFieldLabel = label;
        document.getElementById('modalFieldLabel').innerText = `Nuevo ${label}`;
        document.getElementById('newOptionValue').value = '';
        document.getElementById('currentFieldType').value = field;
        new bootstrap.Modal(document.getElementById('addOptionModal')).show();
    }
    
    function saveNewOption() {
        const newValue = document.getElementById('newOptionValue').value.trim();
        
        if (!newValue) {
            alert('Por favor ingrese un valor');
            return;
        }
        
        const select = document.querySelector(`select[name="${currentField}"]`);
        if (select) {
            const option = document.createElement('option');
            option.value = newValue;
            option.textContent = newValue;
            select.appendChild(option);
            select.value = newValue;
        }
        
        bootstrap.Modal.getInstance(document.getElementById('addOptionModal')).hide();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const institutionRadios = document.querySelectorAll('input[name="institution_type"]');
        const companyFields = document.querySelector('.company-fields');
        const schoolFields = document.querySelector('.school-fields');
        const ngoRescueFields = document.querySelector('.orh-card-section');
        const governmentFields = document.querySelector('.government-fields');
        const organizationLogoSection = document.querySelector('.organization-logo-section');
        const companySelectLabel = document.getElementById('companySelectLabel');
        
        function updateFormByInstitution() {
            const selectedInstitution = document.querySelector('input[name="institution_type"]:checked')?.value;
            
            if (companyFields) companyFields.style.display = 'none';
            if (schoolFields) schoolFields.style.display = 'none';
            if (ngoRescueFields) ngoRescueFields.style.display = 'none';
            if (governmentFields) governmentFields.style.display = 'none';
            
            if (selectedInstitution === 'company') {
                if (companyFields) companyFields.style.display = 'block';
                if (organizationLogoSection) organizationLogoSection.style.display = 'block';
                if (companySelectLabel) companySelectLabel.textContent = 'Seleccione la empresa';
            } else if (selectedInstitution === 'school') {
                if (schoolFields) schoolFields.style.display = 'block';
                if (organizationLogoSection) organizationLogoSection.style.display = 'none';
                if (companySelectLabel) companySelectLabel.textContent = 'Seleccione el colegio';
            } else if (selectedInstitution === 'ngo_rescue') {
                if (ngoRescueFields) ngoRescueFields.style.display = 'block';
                if (organizationLogoSection) organizationLogoSection.style.display = 'block';
                if (companySelectLabel) companySelectLabel.textContent = 'Seleccione la ONG de Rescate';
                initCarnetPreview();
            } else if (selectedInstitution === 'government') {
                if (governmentFields) governmentFields.style.display = 'block';
                if (organizationLogoSection) organizationLogoSection.style.display = 'block';
                if (companySelectLabel) companySelectLabel.textContent = 'Seleccione el ente gubernamental';
            }
        }
        
        institutionRadios.forEach(radio => {
            radio.addEventListener('change', updateFormByInstitution);
        });
        
        const subcategoryOptions = document.querySelectorAll('.subcategory-option');
        const subcategoryInput = document.getElementById('subcategoryInput');
        const studentFields = document.querySelector('.student-specific-fields');
        const teacherAdminFields = document.querySelector('.teacher-admin-fields');
        
        function updateSubcategoryFields() {
            const selectedSubcategory = subcategoryInput.value;
            
            if (selectedSubcategory === 'student') {
                if (studentFields) studentFields.style.display = 'block';
                if (teacherAdminFields) teacherAdminFields.style.display = 'none';
            } else if (selectedSubcategory === 'teacher' || selectedSubcategory === 'administrative') {
                if (studentFields) studentFields.style.display = 'none';
                if (teacherAdminFields) teacherAdminFields.style.display = 'block';
            } else {
                if (studentFields) studentFields.style.display = 'none';
                if (teacherAdminFields) teacherAdminFields.style.display = 'none';
            }
        }
        
        subcategoryOptions.forEach(option => {
            option.addEventListener('click', function() {
                const subcategory = this.dataset.subcategory;
                subcategoryInput.value = subcategory;
                subcategoryOptions.forEach(opt => opt.classList.remove('active'));
                this.classList.add('active');
                updateSubcategoryFields();
            });
        });
        
        const organizationLogoInput = document.getElementById('organizationLogoInput');
        const organizationLogoPreview = document.getElementById('organizationLogoPreview');
        
        if (organizationLogoInput) {
            organizationLogoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        organizationLogoPreview.innerHTML = `<img src="${event.target.result}" alt="Logo preview">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        if (document.querySelector('input[name="institution_type"]:checked')) {
            updateFormByInstitution();
        }
        updateSubcategoryFields();
        initCarnetPreview();
    });
    
    const photoInput = document.getElementById('photoInput');
    const currentPhotoPreview = document.getElementById('currentPhotoPreview');
    
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    currentPhotoPreview.innerHTML = `<img src="${event.target.result}" alt="Vista previa">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    function removePhoto() {
        if (confirm('¿Estás seguro de eliminar la foto?')) {
            currentPhotoPreview.innerHTML = `<div class="photo-placeholder">
                <i class="fas fa-camera"></i>
                <span>Sin foto</span>
            </div>`;
        }
    }
</script>
@endpush
