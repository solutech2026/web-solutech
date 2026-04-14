<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>{{ $person->full_name }} - Perfil Profesional | PROXICARD</title>
    
    <!-- Meta tags -->
    <meta property="og:title" content="{{ $person->full_name }} - Perfil Profesional">
    <meta property="og:description" content="{{ Str::limit($person->bio ?? 'Perfil profesional en PROXICARD', 150) }}">
    <meta property="og:image" content="{{ $person->photo_url }}">
    <meta property="og:type" content="profile">
    <meta name="twitter:card" content="summary_large_image">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            padding: 2rem;
            position: relative;
        }
        
        /* Efecto de fondo */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .bio-container {
            max-width: 1100px;
            margin: 0 auto;
        }
        
        /* Tarjeta principal */
        .bio-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .bio-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.6);
        }
        
        /* Header con diseño de tarjeta de identidad */
        .bio-header {
            position: relative;
            padding: 3rem 2rem 2rem;
            text-align: center;
            overflow: hidden;
        }
        
        .header-bg {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-size: cover;
            background-position: center;
            opacity: 0.1;
        }
        
        /* Degradado para empleados */
        .employee-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }
        
        .employee-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(100, 108, 255, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        
        /* Degradado para personal escolar */
        .school-header {
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        }
        
        .school-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(0, 255, 200, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }
        
        /* Avatar */
        .bio-avatar {
            position: relative;
            width: 130px;
            height: 130px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .bio-avatar:hover {
            transform: scale(1.02);
            box-shadow: 0 25px 40px -10px rgba(0, 0, 0, 0.4);
        }
        
        .bio-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        /* Nombre */
        .bio-name {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -0.5px;
        }
        
        /* Badges */
        .bio-badges {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        
        .bio-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .bio-company {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.9);
            background: rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            backdrop-filter: blur(5px);
        }
        
        /* Tabs estilo PROXICARD */
        .bio-tabs {
            display: flex;
            gap: 0.25rem;
            padding: 0 2rem;
            background: white;
            border-bottom: 1px solid #eef2f6;
        }
        
        .tab-btn {
            padding: 1rem 1.75rem;
            background: none;
            border: none;
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c7a8a;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            letter-spacing: -0.3px;
        }
        
        .tab-btn i {
            margin-right: 0.5rem;
            font-size: 1rem;
        }
        
        .tab-btn:hover {
            color: #4a90e2;
            background: #f8fafc;
        }
        
        .tab-btn.active {
            color: #4a90e2;
        }
        
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 1rem;
            right: 1rem;
            height: 2px;
            background: linear-gradient(90deg, #4a90e2, #764ba2);
            border-radius: 2px;
        }
        
        /* Tab Content */
        .tab-content {
            display: none;
            padding: 2rem;
            animation: fadeIn 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(15px);
            }
            to { 
                opacity: 1; 
                transform: translateY(0);
            }
        }
        
        /* Grids y Cards */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: #f8fafc;
            padding: 1.25rem 1.5rem;
            border-radius: 24px;
            transition: all 0.3s ease;
            border: 1px solid #eef2f6;
        }
        
        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -10px rgba(0, 0, 0, 0.1);
            border-color: #e0e7ff;
        }
        
        .info-card i {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #4a90e2, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.75rem;
            display: inline-block;
        }
        
        .info-card strong {
            display: block;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #8a99aa;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .info-card span {
            font-size: 1rem;
            font-weight: 500;
            color: #1e2a3a;
        }
        
        .info-card.full-width {
            grid-column: 1 / -1;
        }
        
        .info-card.highlight {
            background: linear-gradient(135deg, #e8f0fe 0%, #e0e7ff 100%);
            border-color: #c7d2fe;
        }
        
        .info-card.emergency {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-color: #fcd34d;
        }
        
        .info-card.warning {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border-color: #fca5a5;
        }
        
        /* Estadísticas Académicas */
        .academic-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 24px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.85;
        }
        
        /* Secciones */
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #eef2f6;
        }
        
        .section-header i {
            font-size: 1.25rem;
            background: linear-gradient(135deg, #4a90e2, #764ba2);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .section-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e2a3a;
            letter-spacing: -0.3px;
        }
        
        .section-header h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e2a3a;
        }
        
        /* Horarios */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border-radius: 20px;
            overflow: hidden;
        }
        
        .schedule-table th,
        .schedule-table td {
            padding: 1rem 1.25rem;
            text-align: left;
            border-bottom: 1px solid #eef2f6;
        }
        
        .schedule-table th {
            background: #f1f5f9;
            font-weight: 600;
            color: #1e2a3a;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .schedule-table tr:last-child td {
            border-bottom: none;
        }
        
        .schedule-table tr:hover td {
            background: #fefce8;
        }
        
        /* NFC Section */
        .nfc-section {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            padding: 2rem;
            border-radius: 28px;
            text-align: center;
            margin-top: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .nfc-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(100, 108, 255, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        
        .nfc-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        
        .nfc-icon i {
            font-size: 2rem;
            color: white;
        }
        
        .nfc-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: white;
        }
        
        .nfc-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        
        .nfc-card-code {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            margin: 1rem 0;
            color: white;
            font-family: monospace;
            font-size: 0.9rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .nfc-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.8rem;
        }
        
        .nfc-status.active {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            backdrop-filter: blur(5px);
        }
        
        .btn-vcard {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 100px;
            margin-top: 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
            border: none;
            cursor: pointer;
        }
        
        .btn-vcard:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(102, 126, 234, 0.4);
        }
        
        /* Footer */
        .bio-footer {
            background: #0f172a;
            color: #94a3b8;
            padding: 1.25rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.8rem;
        }
        
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: #667eea;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .bio-tabs {
                overflow-x: auto;
                padding: 0 1rem;
                gap: 0;
            }
            
            .tab-btn {
                padding: 0.75rem 1rem;
                font-size: 0.8rem;
                white-space: nowrap;
            }
            
            .tab-content {
                padding: 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .academic-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .bio-footer {
                flex-direction: column;
                text-align: center;
            }
            
            .schedule-table {
                font-size: 0.75rem;
            }
            
            .schedule-table th,
            .schedule-table td {
                padding: 0.75rem;
            }
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 10px;
        }
        
        /* Animación de carga */
        .bio-card {
            animation: slideUp 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="bio-container">
        <div class="bio-card">
            <!-- Header con diseño de tarjeta de identidad -->
            @if($person->category == 'employee')
                <div class="bio-header employee-header">
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
                        <span class="bio-badge">
                            <i class="fas fa-briefcase"></i> Empleado
                        </span>
                        @if($person->position)
                        <span class="bio-badge">
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
                <div class="bio-header school-header">
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
                        <span class="bio-badge">
                            <i class="fas fa-school"></i> Personal Escolar
                        </span>
                        @if($person->subcategory)
                        <span class="bio-badge">
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
            
            <!-- Tabs -->
            <div class="bio-tabs">
                @if($person->category == 'employee')
                    <button class="tab-btn active" data-tab="personal">
                        <i class="fas fa-user"></i> Información Personal
                    </button>
                    <button class="tab-btn" data-tab="corporate">
                        <i class="fas fa-briefcase"></i> Información Corporativa
                    </button>
                @else
                    <button class="tab-btn active" data-tab="personal">
                        <i class="fas fa-user"></i> Información Personal
                    </button>
                    <button class="tab-btn" data-tab="academic">
                        <i class="fas fa-graduation-cap"></i> Información Académica
                    </button>
                    @if(isset($person->schedules) && $person->schedules->count() > 0)
                    <button class="tab-btn" data-tab="schedule">
                        <i class="fas fa-clock"></i> Horarios
                    </button>
                    @endif
                @endif
                <button class="tab-btn" data-tab="security">
                    <i class="fas fa-shield-alt"></i> Acceso y Seguridad
                </button>
            </div>
            
            <!-- Tab: Información Personal -->
            <div class="tab-content active" id="tab-personal">
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
                        <strong>Correo Electrónico</strong>
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
                    
                    @if($person->birth_date)
                    <div class="info-card">
                        <i class="fas fa-cake-candles"></i>
                        <strong>Fecha de Nacimiento</strong>
                        <span>{{ \Carbon\Carbon::parse($person->birth_date)->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    
                    @if($person->gender)
                    <div class="info-card">
                        <i class="fas fa-venus-mars"></i>
                        <strong>Género</strong>
                        <span>{{ $person->gender == 'male' ? 'Masculino' : ($person->gender == 'female' ? 'Femenino' : 'Otro') }}</span>
                    </div>
                    @endif
                    
                    <div class="info-card">
                        <i class="fas fa-calendar-alt"></i>
                        <strong>Miembro desde</strong>
                        <span>{{ $person->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                <!-- Información de Emergencia -->
                @if($person->emergency_contact_name || $person->emergency_phone || $person->allergies || $person->medical_conditions)
                <div class="section-header">
                    <i class="fas fa-ambulance"></i>
                    <h3>Información de Emergencia</h3>
                </div>
                <div class="info-grid">
                    @if($person->emergency_contact_name)
                    <div class="info-card emergency">
                        <i class="fas fa-user"></i>
                        <strong>Contacto de Emergencia</strong>
                        <span>{{ $person->emergency_contact_name }}</span>
                    </div>
                    @endif
                    @if($person->emergency_phone)
                    <div class="info-card emergency">
                        <i class="fas fa-phone-alt"></i>
                        <strong>Teléfono de Emergencia</strong>
                        <span>{{ $person->emergency_phone }}</span>
                    </div>
                    @endif
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
                        <strong>Condiciones Médicas</strong>
                        <span>{{ $person->medical_conditions }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            
            <!-- Tab: Información Corporativa (solo empleados) -->
            @if($person->category == 'employee')
            <div class="tab-content" id="tab-corporate">
                <div class="info-grid">
                    @if($person->position)
                    <div class="info-card highlight">
                        <i class="fas fa-user-tie"></i>
                        <strong>Cargo / Posición</strong>
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
                    @if($person->company)
                    <div class="info-card highlight">
                        <i class="fas fa-building"></i>
                        <strong>Empresa</strong>
                        <span>{{ $person->company->name }}</span>
                    </div>
                    @endif
                </div>
                
                @if($person->bio)
                <div class="section-header">
                    <i class="fas fa-quote-left"></i>
                    <h3>Perfil Profesional</h3>
                </div>
                <div class="info-card full-width">
                    <span>{{ $person->bio }}</span>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Tab: Información Académica -->
            @if($person->category == 'school')
            <div class="tab-content" id="tab-academic">
                @if($person->subcategory == 'student')
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
                            <div class="stat-label">Promedio General</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value">{{ $person->period_label ?? 'N/A' }}</div>
                            <div class="stat-label">Periodo Actual</div>
                        </div>
                    </div>
                @endif
                
                @if($person->subcategory == 'teacher')
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
                            <strong>Tipo de Docente</strong>
                            <span>{{ $person->teacher_type_label }}</span>
                        </div>
                        @endif
                    </div>
                @endif
                
                @if($person->subcategory == 'administrative')
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
                    </div>
                @endif
                
                @if($person->bio)
                <div class="section-header">
                    <i class="fas fa-quote-left"></i>
                    <h3>Perfil</h3>
                </div>
                <div class="info-card full-width">
                    <span>{{ $person->bio }}</span>
                </div>
                @endif
            </div>
            @endif
            
            <!-- Tab: Horarios -->
            @if(isset($person->schedules) && $person->schedules->count() > 0)
            <div class="tab-content" id="tab-schedule">
                <div class="section-header">
                    <i class="fas fa-clock"></i>
                    <h3>Horario Semanal</h3>
                </div>
                <table class="schedule-table">
                    <thead>
                        <tr>
                            <th>Día</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Materia/Actividad</th>
                            <th>Aula</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($person->schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->day_label }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}</td>
                            <td>{{ $schedule->subject ?? '—' }}</td>
                            <td>{{ $schedule->classroom ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            
            <!-- Tab: Acceso y Seguridad -->
            <div class="tab-content" id="tab-security">
                <div class="nfc-section">
                    <div class="nfc-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3 class="nfc-title">PROXICARD</h3>
                    <p class="nfc-subtitle">Identidad y Acceso Rápido</p>
                    
                    @if($person->nfc_card_id)
                        <div class="nfc-card-code">
                            <i class="fas fa-microchip"></i> {{ $person->nfcCard->card_code ?? 'NFC Activa' }}
                        </div>
                        <div class="nfc-status active">
                            <i class="fas fa-check-circle"></i> Tarjeta Activa
                        </div>
                    @else
                        <div class="nfc-card-code">
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
                    <span>PROXICARD - Sistema de Control de Acceso</span>
                </div>
                <div class="footer-links">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', () => {
                const tabId = button.getAttribute('data-tab');
                
                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                button.classList.add('active');
                
                const activeContent = document.getElementById(`tab-${tabId}`);
                if (activeContent) {
                    activeContent.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>