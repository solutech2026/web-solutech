<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $person->name }} - Perfil | SoluTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bio.css') }}">
</head>
<body>
    <div class="bio-container">
        <div class="bio-card">
            <!-- Header -->
            <div class="bio-header">
                <div class="bio-avatar">
                    {{ substr($person->name, 0, 2) }}
                </div>
                <h1 class="bio-name">{{ $person->name }}</h1>
                <span class="bio-type">
                    <i class="fas {{ $person->type == 'employee' ? 'fa-briefcase' : 'fa-user-friends' }}"></i>
                    {{ $person->type == 'employee' ? 'Empleado' : 'Visitante' }}
                </span>
            </div>
            
            <!-- Body -->
            <div class="bio-body">
                <!-- Información General - Cards -->
                <div class="info-grid">
                    <div class="info-card">
                        <i class="fas fa-building"></i>
                        <strong>Ubicación</strong>
                        <span>{{ $person->company->name ?? 'No especificada' }}</span>
                    </div>
                    @if($person->document_id)
                    <div class="info-card">
                        <i class="fas fa-id-card"></i>
                        <strong>Cédula</strong>
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
                        <strong>Registro</strong>
                        <span>{{ $person->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                <!-- Información Laboral (si es empleado) -->
                @if($person->type == 'employee' && ($person->position || $person->department))
                <div class="bio-section">
                    <h4>
                        <i class="fas fa-briefcase"></i> Información Laboral
                    </h4>
                    <div class="info-grid" style="margin-bottom: 0;">
                        @if($person->position)
                        <div class="info-card" style="margin-bottom: 0;">
                            <i class="fas fa-user-tie"></i>
                            <strong>Cargo</strong>
                            <span>{{ $person->position }}</span>
                        </div>
                        @endif
                        @if($person->department)
                        <div class="info-card" style="margin-bottom: 0;">
                            <i class="fas fa-layer-group"></i>
                            <strong>Departamento</strong>
                            <span>{{ $person->department }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Información de Visita (si es visitante) -->
                @if($person->type == 'visitor' && ($person->companions > 0 || $person->visit_reason))
                <div class="bio-section">
                    <h4>
                        <i class="fas fa-map-marked-alt"></i> Información de Visita
                    </h4>
                    <div class="info-grid" style="margin-bottom: 0;">
                        @if($person->companions > 0)
                        <div class="info-card" style="margin-bottom: 0;">
                            <i class="fas fa-users"></i>
                            <strong>Acompañantes</strong>
                            <span>{{ $person->companions }}</span>
                        </div>
                        @endif
                        @if($person->visit_reason)
                        <div class="info-card" style="margin-bottom: 0;">
                            <i class="fas fa-question-circle"></i>
                            <strong>Motivo</strong>
                            <span>{{ ucfirst($person->visit_reason) }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                <!-- Biografía -->
                @if($person->bio)
                <div class="bio-section">
                    <h4>
                        <i class="fas fa-quote-left"></i> Biografía
                    </h4>
                    <div class="bio-text">
                        {{ $person->bio }}
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
                        <div class="nfc-card-code">{{ $person->nfc_card_id }}</div>
                        <div class="nfc-status">
                            <i class="fas fa-check-circle"></i> Tarjeta Activa
                        </div>
                    @else
                        <div class="nfc-card-code" style="background: rgba(255,255,255,0.15);">
                            Sin tarjeta asignada
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bio-footer">
                <p>
                    <i class="fas fa-shield-alt"></i> Sistema de Control de Acceso SoluTech
                </p>
                <p class="small">
                    <a href="/">← Volver al inicio</a>
                </p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>