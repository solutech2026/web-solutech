@extends('layouts.admin')

@section('title', 'Detalle de Persona')

@section('header', 'Perfil de ' . $person->full_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons-show.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@section('content')
<div class="person-detail-modern">
    <!-- Hero Section -->
    <div class="detail-hero">
        <div class="hero-backdrop"></div>
        <div class="hero-content">
            <div class="hero-avatar">
                @if($person->photo)
                    <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
                @else
                    <div class="avatar-initials">
                        {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="hero-info">
                <h1>{{ $person->full_name }}</h1>
                <div class="hero-badges">
                    <span class="badge-category {{ $person->institution_type ?? $person->category }}">
                        <i class="fas {{ ($person->institution_type ?? $person->category) == 'company' ? 'fa-briefcase' : (($person->institution_type ?? $person->category) == 'ngo_rescue' ? 'fa-heartbeat' : (($person->institution_type ?? $person->category) == 'government' ? 'fa-landmark' : 'fa-school')) }}"></i>
                        @if(($person->institution_type ?? $person->category) == 'company')
                            Empleado
                        @elseif(($person->institution_type ?? $person->category) == 'ngo_rescue')
                            ONG de Rescate
                        @elseif(($person->institution_type ?? $person->category) == 'government')
                            Gobierno
                        @else
                            Personal Escolar
                        @endif
                    </span>
                    @if($person->subcategory)
                    <span class="badge-subcategory {{ $person->subcategory }}">
                        <i class="fas {{ $person->subcategory == 'student' ? 'fa-graduation-cap' : ($person->subcategory == 'teacher' ? 'fa-chalkboard-user' : 'fa-building') }}"></i>
                        @if($person->subcategory == 'student') Estudiante
                        @elseif($person->subcategory == 'teacher') Docente
                        @else Administrativo @endif
                    </span>
                    @endif
                    @if($person->rescue_member_number)
                    <span class="badge-subcategory rescue">
                        <i class="fas fa-id-card"></i>
                        Miembro #{{ $person->rescue_member_number }}
                    </span>
                    @endif
                    @if($person->government_position)
                    <span class="badge-subcategory government">
                        <i class="fas fa-user-tie"></i>
                        {{ $person->government_position_label ?? $person->government_position }}
                    </span>
                    @endif
                    @if($person->average_grade)
                    <span class="badge-average">
                        <i class="fas fa-chart-line"></i>
                        Promedio: {{ number_format($person->average_grade, 2) }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="hero-actions">
                <button class="action-btn-secondary" onclick="openChangePhotoModal()">
                    <i class="fas fa-camera"></i> Cambiar foto
                </button>
                <a href="{{ route('admin.persons.edit', $person->id) }}" class="action-btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="action-btn-danger" onclick="deletePerson({{ $person->id }})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Bio Link Card -->
    @if($person->bio_url)
    <div class="bio-link-card">
        <div class="bio-link-content">
            <div class="bio-link-info">
                <i class="fas fa-link"></i>
                <div>
                    <h4>Perfil Público</h4>
                    <p>Comparte este enlace para mostrar la información de {{ $person->full_name }}</p>
                </div>
            </div>
            <div class="bio-link-actions">
                <div id="qrCodeBio" class="qr-code-bio"></div>
                <a href="{{ $person->bio_full_url }}" target="_blank" class="btn-bio">
                    <i class="fas fa-external-link-alt"></i> Ver perfil
                </a>
                <button class="btn-bio" onclick="copyToClipboard('{{ $person->bio_full_url }}')">
                    <i class="fas fa-copy"></i> Copiar enlace
                </button>
                <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-bio">
                    <i class="fas fa-download"></i> vCard
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="detail-tabs">
        <button class="tab-btn active" data-tab="info">
            <i class="fas fa-user-circle"></i> Información Personal
        </button>
        @if($person->subcategory == 'student')
        <button class="tab-btn" data-tab="academic">
            <i class="fas fa-graduation-cap"></i> Información Académica
        </button>
        @endif
        @if($person->subcategory == 'employee')
        <button class="tab-btn" data-tab="laboral">
            <i class="fas fa-briefcase"></i> Información Laboral
        </button>
        @endif
        @if(in_array($person->subcategory, ['teacher', 'administrative']))
        <button class="tab-btn" data-tab="school">
            <i class="fas fa-school"></i> Información Escolar
        </button>
        @endif
        @if(($person->institution_type ?? $person->category) == 'ngo_rescue')
        <button class="tab-btn" data-tab="rescue">
            <i class="fas fa-id-card"></i> Carnet de Rescate
        </button>
        @endif
        @if(($person->institution_type ?? $person->category) == 'government')
        <button class="tab-btn" data-tab="government">
            <i class="fas fa-landmark"></i> Datos Gubernamentales
        </button>
        @endif
        <button class="tab-btn" data-tab="health">
            <i class="fas fa-heartbeat"></i> Salud y Emergencias
        </button>
        <button class="tab-btn" data-tab="schedule">
            <i class="fas fa-clock"></i> Horarios
        </button>
        <button class="tab-btn" data-tab="nfc">
            <i class="fas fa-id-card"></i> Tarjeta NFC
        </button>
        <button class="tab-btn" data-tab="access">
            <i class="fas fa-history"></i> Historial de Accesos
        </button>
    </div>

    <div class="detail-content">
        <!-- TAB 1: Información Personal (TODOS) -->
        <div class="tab-pane active" id="tab-info">
            <div class="detail-grid">
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-user"></i>
                        <h3>Datos Personales</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-id-card"></i>
                                <span>Cédula / ID</span>
                            </div>
                            <div class="info-value">{{ $person->document_id ?? 'No registrada' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-envelope"></i>
                                <span>Correo electrónico</span>
                            </div>
                            <div class="info-value">{{ $person->email ?? 'No registrado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone"></i>
                                <span>Teléfono</span>
                            </div>
                            <div class="info-value">{{ $person->phone ?? 'No registrado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-venus-mars"></i>
                                <span>Género</span>
                            </div>
                            <div class="info-value">
                                @if($person->gender == 'male') Masculino
                                @elseif($person->gender == 'female') Femenino
                                @elseif($person->gender == 'other') Otro
                                @else No especificado @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Fecha de nacimiento</span>
                            </div>
                            <div class="info-value">{{ $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->format('d/m/Y') : 'No registrada' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-building"></i>
                                <span>Institución</span>
                            </div>
                            <div class="info-value">{{ $person->company->name ?? 'N/A' }}</div>
                        </div>
                        @if($person->position && ($person->subcategory == 'employee' || $person->subcategory == 'teacher' || $person->subcategory == 'administrative'))
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user-tie"></i>
                                <span>Cargo / Posición</span>
                            </div>
                            <div class="info-value">{{ $person->position }}</div>
                        </div>
                        @endif
                        @if($person->department)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-layer-group"></i>
                                <span>Departamento / Área</span>
                            </div>
                            <div class="info-value">{{ $person->department }}</div>
                        </div>
                        @endif
                        @if($person->company_logo)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-building"></i>
                                <span>Logo</span>
                            </div>
                            <div class="info-value">
                                <img src="{{ asset('storage/' . $person->company_logo) }}" alt="Logo" style="height: 50px;">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($person->bio)
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-quote-left"></i>
                        <h3>Biografía / Notas</h3>
                    </div>
                    <div class="bio-content">
                        <p>{{ $person->bio }}</p>
                    </div>
                </div>
                @endif

                <div class="stats-mini-grid">
                    <div class="stat-mini">
                        <div class="stat-mini-icon blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-mini-info">
                            <span class="stat-mini-label">Registro</span>
                            <span class="stat-mini-value">{{ $person->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @if($person->last_access_at)
                    <div class="stat-mini">
                        <div class="stat-mini-icon green">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-mini-info">
                            <span class="stat-mini-label">Último acceso</span>
                            <span class="stat-mini-value">{{ $person->last_access_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
                    @if($person->user_id)
                    <div class="stat-mini">
                        <div class="stat-mini-icon purple">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-mini-info">
                            <span class="stat-mini-label">Usuario del sistema</span>
                            <span class="stat-mini-value">Creado</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: Información Académica (solo ESTUDIANTES) -->
        @if($person->subcategory == 'student')
        <div class="tab-pane" id="tab-academic">
            <div class="academic-grid">
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Datos Académicos</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-book"></i>
                                <span>Grado</span>
                            </div>
                            <div class="info-value">{{ $person->grade_level_label ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-layer-group"></i>
                                <span>Sección</span>
                            </div>
                            <div class="info-value">{{ $person->section ?? 'No especificada' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-week"></i>
                                <span>Año Escolar</span>
                            </div>
                            <div class="info-value">{{ $person->academic_year ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-chart-line"></i>
                                <span>Periodo Actual</span>
                            </div>
                            <div class="info-value">{{ $person->period_label ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-star"></i>
                                <span>Promedio General</span>
                            </div>
                            <div class="info-value">
                                @if($person->average_grade)
                                    <span class="average-badge">{{ number_format($person->average_grade, 2) }}</span>
                                @else
                                    No calculado
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-file-pdf"></i>
                        <h3>Boletines de Notas</h3>
                        <button class="btn-upload-report" onclick="openUploadReportModal()">
                            <i class="fas fa-upload"></i> Subir Boletín
                        </button>
                    </div>
                    <div class="report-cards-list" id="reportCardsList">
                        <div class="loading-spinner">Cargando boletines...</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 3: Información Laboral (solo EMPLEADOS) -->
        @if(($person->institution_type ?? $person->category) == 'company')
        <div class="tab-pane" id="tab-laboral">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-briefcase"></i>
                    <h3>Información Laboral</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo</span>
                        </div>
                        <div class="info-value">{{ $person->position ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            <span>Área / Departamento</span>
                        </div>
                        <div class="info-value">{{ $person->department ?? 'No especificado' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 4: Información Escolar (DOCENTES y ADMINISTRATIVOS) -->
        @if(in_array($person->subcategory, ['teacher', 'administrative']))
        <div class="tab-pane" id="tab-school">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-school"></i>
                    <h3>Información {{ $person->subcategory == 'teacher' ? 'Docente' : 'Administrativa' }}</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo</span>
                        </div>
                        <div class="info-value">{{ $person->position ?? 'No especificado' }}</div>
                    </div>
                    @if($person->subcategory == 'teacher')
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-chalkboard-user"></i>
                            <span>Tipo de Docente</span>
                        </div>
                        <div class="info-value">{{ $person->teacher_type_label ?? 'No especificado' }}</div>
                    </div>
                    @endif
                    @if($person->subcategory == 'administrative')
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            <span>Departamento</span>
                        </div>
                        <div class="info-value">{{ $person->department ?? 'No especificado' }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 5: Carnet de Rescate (solo ONG RESCATE) -->
        @if(($person->institution_type ?? $person->category) == 'ngo_rescue')
        <div class="tab-pane" id="tab-rescue">
            <div class="info-card-glass rescue-card">
                <div class="card-header-glass">
                    <i class="fas fa-id-card"></i>
                    <h3>🎖️ Carnet de Rescate <span class="info-badge rescue">ONG</span></h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-hashtag"></i>
                            <span>Número de Miembro</span>
                        </div>
                        <div class="info-value"><strong>{{ $person->rescue_member_number ?? 'No asignado' }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            <span>Categoría de Miembro</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_member_category ?? 'No especificada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Fecha de Vencimiento</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_expiry_date ? \Carbon\Carbon::parse($person->rescue_expiry_date)->format('m/Y') : 'No registrada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-md"></i>
                            <span>Especialidad / Área</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_specialty_area ?? 'No especificada' }}</div>
                    </div>
                    @if($person->rescue_certifications)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-certificate"></i>
                            <span>Certificaciones</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_certifications }}</div>
                    </div>
                    @endif
                </div>
                
                @if($person->rescue_member_number)
                <div class="carnet-mini-preview">
                    <p><strong>ORGANIZACIÓN DE RESCATE</strong></p>
                    <p>MIEMBRO N°: {{ $person->rescue_member_number }}</p>
                    <p>CATEGORÍA: {{ $person->rescue_member_category ?? 'N/A' }}</p>
                    <p>VENCE: {{ $person->rescue_expiry_date ? \Carbon\Carbon::parse($person->rescue_expiry_date)->format('m/Y') : 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- TAB 6: Datos Gubernamentales (solo GOBIERNO) -->
        @if(($person->institution_type ?? $person->category) == 'government')
        <div class="tab-pane" id="tab-government">
            <div class="info-card-glass government-card">
                <div class="card-header-glass">
                    <i class="fas fa-landmark"></i>
                    <h3>🏛️ Información Gubernamental <span class="info-badge government">Gobierno</span></h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            <span>Nivel del Gobierno</span>
                        </div>
                        <div class="info-value">
                            @if($person->government_level == 'national') 🏛️ Nacional
                            @elseif($person->government_level == 'regional') 🏢 Regional
                            @elseif($person->government_level == 'municipal') 🏘️ Municipal
                            @elseif($person->government_level == 'parish') 📌 Parroquial
                            @else No especificado @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-balance-scale"></i>
                            <span>Rama del Poder</span>
                        </div>
                        <div class="info-value">
                            @if($person->government_branch == 'executive') ⚡ Poder Ejecutivo
                            @elseif($person->government_branch == 'legislative') 📜 Poder Legislativo
                            @elseif($person->government_branch == 'judicial') ⚖️ Poder Judicial
                            @elseif($person->government_branch == 'citizen') 👁️ Poder Ciudadano
                            @elseif($person->government_branch == 'electoral') 🗳️ Poder Electoral
                            @else No especificado @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i>
                            <span>Ministerio / Ente</span>
                        </div>
                        <div class="info-value">{{ $person->government_entity_label ?? $person->government_entity ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo / Jerarquía</span>
                        </div>
                        <div class="info-value">{{ $person->government_position_label ?? $person->government_position ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i>
                            <span>N° de Carnet / Credencial</span>
                        </div>
                        <div class="info-value">{{ $person->government_card_number ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-check"></i>
                            <span>Fecha de Ingreso</span>
                        </div>
                        <div class="info-value">{{ $person->government_joining_date ? \Carbon\Carbon::parse($person->government_joining_date)->format('d/m/Y') : 'No registrada' }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- TAB 7: Salud y Emergencias (TODOS) -->
        <div class="tab-pane" id="tab-health">
            <div class="emergency-grid">
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-ambulance"></i>
                        <h3>Contacto de Emergencia</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-user"></i>
                                <span>Nombre del contacto</span>
                            </div>
                            <div class="info-value">{{ $person->emergency_contact_name ?? 'No registrado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-phone-alt"></i>
                                <span>Teléfono de emergencia</span>
                            </div>
                            <div class="info-value">{{ $person->emergency_phone ?? 'No registrado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-heart"></i>
                                <span>Parentesco</span>
                            </div>
                            <div class="info-value">{{ $person->emergency_relationship ?? 'No registrado' }}</div>
                        </div>
                    </div>
                </div>

                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-notes-medical"></i>
                        <h3>Información de Salud</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-tint"></i>
                                <span>Tipo de Sangre</span>
                            </div>
                            <div class="info-value">{{ $person->blood_type ?? 'No registrado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-allergies"></i>
                                <span>Alergias</span>
                            </div>
                            <div class="info-value">{{ $person->allergies ?? 'No registradas' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-heartbeat"></i>
                                <span>Enfermedades / Condiciones</span>
                            </div>
                            <div class="info-value">{{ $person->medical_conditions ?? 'No registradas' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 8: Horarios (TODOS) -->
        <div class="tab-pane" id="tab-schedule">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-clock"></i>
                    <h3>Horario Semanal</h3>
                </div>
                <div class="schedule-table-container">
                    <table class="schedule-table-modern">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Materia / Actividad</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            <tr class="loading-row">
                                <td colspan="5" class="text-center">Cargando horarios...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 9: Tarjeta NFC -->
        <div class="tab-pane" id="tab-nfc">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-id-card"></i>
                    <h3>Tarjeta NFC</h3>
                </div>
                <div class="nfc-content">
                    @if($person->nfc_card_id)
                        <div class="nfc-card-active">
                            <div class="nfc-chip">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="nfc-info">
                                <span class="nfc-label">ID Tarjeta</span>
                                <span class="nfc-code">{{ $person->nfc_card_id }}</span>
                            </div>
                            <div class="nfc-status-badge active">
                                <i class="fas fa-check-circle"></i> Activa
                            </div>
                            <button class="btn-unlink" onclick="unassignNFCCard({{ $person->id }})">
                                <i class="fas fa-unlink"></i> Desvincular
                            </button>
                        </div>
                    @else
                        <div class="nfc-card-empty">
                            <div class="empty-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <p>Sin tarjeta NFC asignada</p>
                            <button class="btn-assign" onclick="openAssignNFCModal({{ $person->id }})">
                                <i class="fas fa-plus"></i> Asignar Tarjeta
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 10: Historial de Accesos -->
        <div class="tab-pane" id="tab-access">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-history"></i>
                    <h3>Historial de Accesos</h3>
                    <span class="header-badge" id="accessCount">0 registros</span>
                </div>
                <div class="access-table-container">
                    <table class="access-table-modern">
                        <thead>
                            <tr>
                                <th>Fecha y Hora</th>
                                <th>Ubicación</th>
                                <th>Método</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody id="accessLogsBody">
                            <tr class="loading-row">
                                <td colspan="4" class="text-center">Cargando historial...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar NFC -->
<div class="modal fade modern-modal" id="assignNFCModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card"></i> Asignar Tarjeta NFC
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="info-card-modal">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <label>Persona</label>
                        <p id="assignPersonName">{{ $person->full_name }}</p>
                    </div>
                </div>
                <div class="form-group-modern">
                    <label>Seleccionar Tarjeta NFC</label>
                    <select name="card_id" id="nfcCardSelect" class="form-select-modern" required>
                        <option value="">-- Seleccionar tarjeta --</option>
                        @foreach($availableCards ?? [] as $card)
                            <option value="{{ $card->id }}">{{ $card->card_code }} @if($card->notes) - {{ $card->notes }} @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="alert-modern-info">
                    <i class="fas fa-info-circle"></i>
                    Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-primary-modern" onclick="assignNFCCard({{ $person->id }})">Asignar Tarjeta</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir Boletín -->
<div class="modal fade modern-modal" id="uploadReportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf"></i> Subir Boletín de Notas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadReportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group-modern">
                        <label>Periodo *</label>
                        <select name="period" id="reportPeriod" class="form-select-modern" required>
                            <option value="">Seleccionar periodo</option>
                            <option value="first">Primer Lapso</option>
                            <option value="second">Segundo Lapso</option>
                            <option value="third">Tercer Lapso</option>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label>Año Escolar *</label>
                        <input type="text" name="academic_year" class="input-modern" placeholder="Ej: 2024-2025" required>
                    </div>
                    <div class="form-group-modern">
                        <label>Grado *</label>
                        <select name="grade_level" class="form-select-modern" required>
                            <option value="">Seleccionar grado</option>
                            <optgroup label="EDUCACIÓN PRIMARIA">
                                <option value="1er_grado">1er Grado</option>
                                <option value="2do_grado">2do Grado</option>
                                <option value="3er_grado">3er Grado</option>
                                <option value="4to_grado">4to Grado</option>
                                <option value="5to_grado">5to Grado</option>
                                <option value="6to_grado">6to Grado</option>
                            </optgroup>
                            <optgroup label="EDUCACIÓN MEDIA GENERAL">
                                <option value="7mo_grado">7mo Grado (1er Año)</option>
                                <option value="8vo_grado">8vo Grado (2do Año)</option>
                                <option value="9no_grado">9no Grado (3er Año)</option>
                            </optgroup>
                            <optgroup label="EDUCACIÓN MEDIA DIVERSIFICADA">
                                <option value="4to_ano">4to Año (10° grado)</option>
                                <option value="5to_ano">5to Año (11° grado)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label>Promedio (opcional)</label>
                        <input type="number" name="average" class="input-modern" step="0.01" min="0" max="20" placeholder="0 - 20">
                    </div>
                    <div class="form-group-modern">
                        <label>Archivo *</label>
                        <input type="file" name="file" class="input-modern" accept=".pdf,.jpg,.png" required>
                        <small class="form-text">Máximo 5MB. Formatos: PDF, JPG, PNG</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-modern">Subir Boletín</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambiar Foto -->
<div class="modal fade modern-modal" id="changePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePhotoForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="current-photo-modal" style="text-align: center; margin-bottom: 1rem;">
                        <img id="currentPhotoPreview" src="{{ $person->photo_url ?? asset('images/default-avatar.png') }}" alt="Foto actual" style="width: 150px; height: 150px; border-radius: 1rem; object-fit: cover;">
                    </div>
                    <div class="form-group-modern">
                        <label>Seleccionar nueva foto</label>
                        <input type="file" name="photo" id="newPhotoInput" class="input-modern" accept="image/*" required>
                        <small class="form-text">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-modern">Actualizar Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPersonId = {{ $person->id }};

    // Generar QR Code para Bio
    @if($person->bio_url)
    new QRCode(document.getElementById("qrCodeBio"), {
        text: "{{ $person->bio_full_url }}",
        width: 80,
        height: 80,
        colorDark: "#1f2937",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    @endif

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('URL copiada al portapapeles');
    }

    // TABS
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });

    // Cargar Horarios
    function loadSchedules() {
        fetch(`/admin/persons/{{ $person->id }}/schedules`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('scheduleTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay horarios registrados</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.map(schedule => `
                    <tr>
                        <td>${schedule.day_label}</td>
                        <td>${schedule.start_time}</td>
                        <td>${schedule.end_time}</td>
                        <td>${schedule.subject || '-'}</td>
                        <td>${schedule.classroom || '-'}</td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('scheduleTableBody').innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar horarios</td></tr>';
            });
    }

    // Cargar Historial de Accesos
    function loadAccessLogs() {
        fetch(`/admin/persons/{{ $person->id }}/access-logs`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('accessLogsBody');
                const countSpan = document.getElementById('accessCount');
                countSpan.textContent = `${data.length} registros`;
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros de acceso</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.map(log => `
                    <tr>
                        <td><div class="date-cell"><i class="far fa-calendar-alt"></i> ${log.access_time}</div></td>
                        <td>${log.gate || 'Principal'}</td>
                        <td><span class="method-badge ${log.verification_method}"><i class="fas ${log.verification_method == 'nfc' ? 'fa-id-card' : 'fa-key'}"></i> ${log.verification_method_label}</span></td>
                        <td><span class="status-badge ${log.status}">${log.status_label}</span></td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('accessLogsBody').innerHTML = '<tr><td colspan="4" class="text-center">Error al cargar historial</td></tr>';
            });
    }

    // Cargar Boletines
    @if($person->subcategory == 'student')
    function loadReportCards() {
        fetch(`/admin/persons/{{ $person->id }}/report-cards`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('reportCardsList');
                
                if (!data.success || data.data.length === 0) {
                    container.innerHTML = '<div class="empty-state-small"><i class="fas fa-file-pdf"></i><p>No hay boletines subidos</p></div>';
                    return;
                }
                
                container.innerHTML = data.data.map(report => `
                    <div class="report-card-item">
                        <div class="report-card-info">
                            <span class="report-card-period">${report.period_label} - ${report.grade_level}</span>
                            <span class="report-card-year">${report.academic_year}</span>
                            ${report.average ? `<span class="report-card-average">Promedio: ${report.average}</span>` : ''}
                        </div>
                        <div class="report-card-actions">
                            <a href="${report.file_url}" class="btn-view-pdf" target="_blank"><i class="fas fa-eye"></i> Ver</a>
                            <button class="btn-delete-report" onclick="deleteReportCard(${report.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('reportCardsList').innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>Error al cargar boletines</p></div>';
            });
    }
    @endif

    // Asignar NFC
    function openAssignNFCModal(personId) {
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }

    function assignNFCCard(personId) {
        const cardId = document.getElementById('nfcCardSelect').value;
        if (!cardId) {
            alert('Seleccione una tarjeta NFC');
            return;
        }
        
        fetch(`/admin/persons/${personId}/assign-nfc`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ card_id: cardId })
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Error al asignar la tarjeta');
            }
        });
    }

    function unassignNFCCard(personId) {
        if (confirm('¿Desvincular la tarjeta NFC de esta persona?')) {
            fetch(`/admin/persons/${personId}/unassign-nfc`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }

    // Subir Boletín
    function openUploadReportModal() {
        new bootstrap.Modal(document.getElementById('uploadReportModal')).show();
    }

    document.getElementById('uploadReportForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/admin/persons/{{ $person->id }}/upload-report-card`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Error: ' + (data.error || 'Error al subir el boletín'));
              }
          })
          .catch(error => {
              alert('Error al subir el boletín');
          });
    });

    function deleteReportCard(reportCardId) {
        if (confirm('¿Eliminar este boletín?')) {
            fetch(`/admin/persons/{{ $person->id }}/report-cards/${reportCardId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }

    // Cambiar Foto
    function openChangePhotoModal() {
        new bootstrap.Modal(document.getElementById('changePhotoModal')).show();
    }

    document.getElementById('changePhotoForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/admin/persons/{{ $person->id }}/upload-photo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              alert('Error al subir la foto');
          });
    });

    // Eliminar Persona
    function deletePerson(id) {
        if (confirm('¿Eliminar esta persona? Esta acción no se puede deshacer.')) {
            fetch(`/admin/persons/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.href = '{{ route("admin.persons.index") }}');
        }
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        loadSchedules();
        loadAccessLogs();
        @if($person->subcategory == 'student')
            loadReportCards();
        @endif
    });
</script>
@endpush