<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $person->full_name }} - Perfil Profesional | Solubase</title>
    
    <!-- Meta tags para redes sociales -->
    <meta property="og:title" content="{{ $person->full_name }} - Perfil Profesional">
    <meta property="og:description" content="{{ Str::limit($person->bio ?? 'Perfil profesional en Solubase', 150) }}">
    <meta property="og:image" content="{{ $person->photo_url }}">
    <meta property="og:type" content="profile">
    <meta name="twitter:card" content="summary_large_image">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/bio.css') }}">
    
    @if($person->category == 'employee')
        <link rel="stylesheet" href="{{ asset('css/bio-employee.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/bio-school.css') }}">
    @endif
</head>
<body class="{{ $person->category == 'employee' ? 'employee-theme' : 'school-theme' }}">
    <div class="bio-container">
        <div class="bio-card">
            <!-- Header - Diseño según categoría -->
            @if($person->category == 'employee')
                <!-- Diseño para EMPLEADOS -->
                <div class="bio-header employee-header">
                    <div class="header-bg"></div>
                    <div class="bio-avatar">
                        @if($person->photo)
                            <img src="{{ $person->photo_url }}" alt="{{ $person->full_name }}">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h1 class="bio-name">{{ $person->full_name }}</h1>
                    <div class="bio-badges">
                        <span class="bio-badge category employee">
                            <i class="fas fa-briefcase"></i> Empleado
                        </span>
                        @if($person->position)
                        <span class="bio-badge position">
                            <i class="fas fa-user-tie"></i> {{ $person->position }}
                        </span>
                        @endif
                    </div>
                    @if($person->company)
                    <div class="bio-company">
                        <i class="fas fa-building"></i> {{ $person->company->name }}
                    </div>
                    @endif
                </div>
            @else
                <!-- Diseño para COLEGIOS -->
                <div class="bio-header school-header">
                    <div class="header-bg"></div>
                    <div class="school-decoration">
                        <div class="school-icon">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                    <div class="bio-avatar">
                        @if($person->photo)
                            <img src="{{ $person->photo_url }}" alt="{{ $person->full_name }}">
                        @else
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <h1 class="bio-name">{{ $person->full_name }}</h1>
                    <div class="bio-badges">
                        <span class="bio-badge category school">
                            <i class="fas fa-school"></i> Personal Escolar
                        </span>
                        @if($person->subcategory)
                        <span class="bio-badge subcategory {{ $person->subcategory }}">
                            <i class="fas {{ $person->subcategory == 'student' ? 'fa-graduation-cap' : ($person->subcategory == 'teacher' ? 'fa-chalkboard-user' : 'fa-building') }}"></i>
                            {{ $person->subcategory_label }}
                        </span>
                        @endif
                    </div>
                    @if($person->company)
                    <div class="bio-company">
                        <i class="fas fa-school"></i> {{ $person->company->name }}
                    </div>
                    @endif
                </div>
            @endif
            
            <!-- Body - Diseño según categoría -->
            <div class="bio-body">
                <!-- Información General - Cards -->
                <div class="info-grid">
                    @if($person->document_id)
                    <div class="info-card">
                        <i class="fas fa-id-card"></i>
                        <strong>Identificación</strong>
                        <span>{{ $person->document_id }}</span>
                    </div>
                    @endif
                    
                    @if($person->email)
                    <div class="info-card">
                        <i class="fas fa-envelope"></i>
                        <strong>Email</strong>
                        <span>{{ $person->email }}</span>
                    </div>
                    @endif
                    
                    @if($person->phone)
                    <div class="info-card">
                        <i class="fas fa-phone"></i>
                        <strong>Teléfono</strong>
                        <span>{{ $person->phone }}</span>
                    </div>
                    @endif
                    
                    <div class="info-card">
                        <i class="fas fa-calendar-alt"></i>
                        <strong>Miembro desde</strong>
                        <span>{{ $person->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                <!-- Sección específica para EMPLEADOS -->
                @if($person->category == 'employee')
                <div class="employee-section">
                    <div class="section-header">
                        <i class="fas fa-briefcase"></i>
                        <h3>Información Profesional</h3>
                    </div>
                    <div class="employee-info-grid">
                        @if($person->position)
                        <div class="info-card highlight">
                            <i class="fas fa-user-tie"></i>
                            <strong>Cargo</strong>
                            <span>{{ $person->position }}</span>
                        </div>
                        @endif
                        @if($person->department)
                        <div class="info-card highlight">
                            <i class="fas fa-layer-group"></i>
                            <strong>Departamento</strong>
                            <span>{{ $person->department }}</span>
                        </div>
                        @endif
                        @if($person->bio)
                        <div class="info-card full-width">
                            <i class="fas fa-quote-left"></i>
                            <strong>Perfil Profesional</strong>
                            <span>{{ $person->bio }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Sección específica para ESTUDIANTES -->
                @if($person->subcategory == 'student')
                <div class="student-section">
                    <div class="section-header">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Rendimiento Académico</h3>
                    </div>
                    <div class="academic-stats">
                        <div class="stat-card">
                            <div class="stat-value">{{ $person->grade_level_label ?? 'N/A' }}</div>
                            <div class="stat-label">Grado</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $person->academic_year ?? 'N/A' }}</div>
                            <div class="stat-label">Año Escolar</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ number_format($person->average_grade ?? 0, 2) }}</div>
                            <div class="stat-label">Promedio</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $person->period_label ?? 'N/A' }}</div>
                            <div class="stat-label">Periodo</div>
                        </div>
                    </div>
                    
                    @if($person->allergies || $person->medical_conditions)
                    <div class="medical-info">
                        <div class="section-header small">
                            <i class="fas fa-notes-medical"></i>
                            <h4>Información Médica</h4>
                        </div>
                        <div class="info-grid">
                            @if($person->allergies)
                            <div class="info-card warning">
                                <i class="fas fa-allergies"></i>
                                <strong>Alergias</strong>
                                <span>{{ $person->allergies }}</span>
                            </div>
                            @endif
                            @if($person->medical_conditions)
                            <div class="info-card warning">
                                <i class="fas fa-heartbeat"></i>
                                <strong>Condiciones</strong>
                                <span>{{ $person->medical_conditions }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif
                
                <!-- Sección específica para DOCENTES -->
                @if($person->subcategory == 'teacher')
                <div class="teacher-section">
                    <div class="section-header">
                        <i class="fas fa-chalkboard-user"></i>
                        <h3>Información Docente</h3>
                    </div>
                    <div class="info-grid">
                        @if($person->position)
                        <div class="info-card highlight">
                            <i class="fas fa-chalkboard"></i>
                            <strong>Especialidad</strong>
                            <span>{{ $person->position }}</span>
                        </div>
                        @endif
                        @if($person->teacher_type)
                        <div class="info-card highlight">
                            <i class="fas fa-user-graduate"></i>
                            <strong>Tipo</strong>
                            <span>{{ $person->teacher_type_label }}</span>
                        </div>
                        @endif
                        @if($person->bio)
                        <div class="info-card full-width">
                            <i class="fas fa-quote-left"></i>
                            <strong>Perfil Docente</strong>
                            <span>{{ $person->bio }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Sección específica para ADMINISTRATIVOS -->
                @if($person->subcategory == 'administrative')
                <div class="administrative-section">
                    <div class="section-header">
                        <i class="fas fa-building"></i>
                        <h3>Información Administrativa</h3>
                    </div>
                    <div class="info-grid">
                        @if($person->position)
                        <div class="info-card highlight">
                            <i class="fas fa-user-tie"></i>
                            <strong>Cargo</strong>
                            <span>{{ $person->position }}</span>
                        </div>
                        @endif
                        @if($person->department)
                        <div class="info-card highlight">
                            <i class="fas fa-layer-group"></i>
                            <strong>Departamento</strong>
                            <span>{{ $person->department }}</span>
                        </div>
                        @endif
                        @if($person->bio)
                        <div class="info-card full-width">
                            <i class="fas fa-quote-left"></i>
                            <strong>Perfil</strong>
                            <span>{{ $person->bio }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Información de Emergencia (común) -->
                @if($person->emergency_contact_name || $person->emergency_phone)
                <div class="emergency-section">
                    <div class="section-header small">
                        <i class="fas fa-ambulance"></i>
                        <h4>Contacto de Emergencia</h4>
                    </div>
                    <div class="info-grid">
                        @if($person->emergency_contact_name)
                        <div class="info-card emergency">
                            <i class="fas fa-user"></i>
                            <strong>Nombre</strong>
                            <span>{{ $person->emergency_contact_name }}</span>
                        </div>
                        @endif
                        @if($person->emergency_phone)
                        <div class="info-card emergency">
                            <i class="fas fa-phone-alt"></i>
                            <strong>Teléfono</strong>
                            <span>{{ $person->emergency_phone }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Sección NFC -->
                <div class="nfc-section">
                    <div class="nfc-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="nfc-title">Acceso Inteligente con NFC</h3>
                    <p class="nfc-subtitle">Acerca tu tarjeta NFC al lector</p>
                    
                    @if($person->nfc_card_id)
                        <div class="nfc-card-code">
                            <i class="fas fa-microchip"></i> {{ $person->nfcCard->card_code ?? 'NFC Activa' }}
                        </div>
                        <div class="nfc-status active">
                            <i class="fas fa-check-circle"></i> Tarjeta Activa
                        </div>
                    @else
                        <div class="nfc-card-code inactive">
                            <i class="fas fa-id-card"></i> Sin tarjeta asignada
                        </div>
                    @endif
                    
                    <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-vcard">
                        <i class="fas fa-download"></i> Descargar vCard
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bio-footer">
                <div class="footer-content">
                    <i class="fas fa-shield-alt"></i>
                    <span>Sistema de Control de Acceso Solubase</span>
                </div>
                <div class="footer-links">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>