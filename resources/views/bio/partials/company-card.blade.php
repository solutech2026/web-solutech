<div class="company-card">
    <!-- Background decorativo -->
    <div class="card-bg"></div>
    
    <!-- Logo -->
    <div class="company-logo">
        @if($person->company_logo)
            <img src="{{ asset('storage/' . $person->company_logo) }}" alt="{{ $person->company->name ?? 'Solutech' }}">
        @else
            <div class="logo-placeholder">
                <i class="fas fa-building"></i>
            </div>
        @endif
    </div>
    
    <!-- Foto de perfil -->
    <div class="profile-photo">
        @if($person->photo)
            <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
        @else
            <div class="photo-placeholder">
                {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
            </div>
        @endif
        <div class="photo-ring"></div>
    </div>
    
    <!-- Nombre y cargo -->
    <h1 class="employee-name">{{ $person->full_name }}</h1>
    <div class="employee-position">
        <i class="fas fa-briefcase"></i>
        {{ $person->position ?? 'Colaborador' }}
    </div>
    
    <!-- Stats rápidos -->
    <div class="stats-row">
        <div class="stat-item">
            <span class="stat-value">{{ $person->created_at->format('Y') }}</span>
            <span class="stat-label">Miembro desde</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-value">@if($person->nfc_card_id) Activo @else Inactivo @endif</span>
            <span class="stat-label">NFC Status</span>
        </div>
    </div>
    
    <!-- Pestañas -->
    <div class="profile-tabs">
        <button class="tab-btn active" data-tab="personal">
            <i class="fas fa-user-circle"></i>
            <span>Personal</span>
        </button>
        <button class="tab-btn" data-tab="laboral">
            <i class="fas fa-briefcase"></i>
            <span>Laboral</span>
        </button>
        <button class="tab-btn" data-tab="salud">
            <i class="fas fa-heartbeat"></i>
            <span>Salud</span>
        </button>
        <button class="tab-btn" data-tab="emergencia">
            <i class="fas fa-phone-alt"></i>
            <span>Emergencia</span>
        </button>
    </div>
    
    <!-- Contenido pestañas -->
    <div class="tab-content-container">
        
        <!-- Personal -->
        <div class="tab-pane active" id="tab-personal">
            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Cédula / Documento</span>
                        <span class="info-value">{{ $person->document_id ?? 'No registrada' }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Correo electrónico</span>
                        <span class="info-value">{{ $person->email ?? 'No registrado' }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Teléfono</span>
                        <span class="info-value">{{ $person->phone ?? 'No registrado' }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Fecha de nacimiento</span>
                        <span class="info-value">{{ $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->format('d/m/Y') : 'No registrada' }}</span>
                    </div>
                </div>
            </div>
            
            @if($person->bio)
            <div class="bio-card">
                <div class="bio-icon">
                    <i class="fas fa-quote-right"></i>
                </div>
                <p class="bio-text">{{ $person->bio }}</p>
            </div>
            @endif
        </div>
        
        <!-- Laboral -->
        <div class="tab-pane" id="tab-laboral">
            <div class="info-list">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Cargo / Posición</span>
                        <span class="info-value">{{ $person->position ?? 'No especificado' }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Área / Departamento</span>
                        <span class="info-value">{{ $person->department ?? 'No especificado' }}</span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Empresa</span>
                        <span class="info-value">{{ $person->company->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Salud -->
        <div class="tab-pane" id="tab-salud">
            <div class="info-list">
                @if($person->blood_type)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-tint"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Tipo de Sangre</span>
                        <span class="info-value">{{ $person->blood_type }}</span>
                    </div>
                </div>
                @endif
                @if($person->allergies)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-allergies"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Alergias</span>
                        <span class="info-value">{{ $person->allergies }}</span>
                    </div>
                </div>
                @endif
                @if($person->medical_conditions)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Condiciones Médicas</span>
                        <span class="info-value">{{ $person->medical_conditions }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Emergencia -->
        <div class="tab-pane" id="tab-emergencia">
            <div class="info-list">
                @if($person->emergency_contact_name)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Contacto de Emergencia</span>
                        <span class="info-value">{{ $person->emergency_contact_name }}</span>
                    </div>
                </div>
                @endif
                @if($person->emergency_phone)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone-alt"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Teléfono de Emergencia</span>
                        <span class="info-value">{{ $person->emergency_phone }}</span>
                    </div>
                </div>
                @endif
                @if($person->emergency_relationship)
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="info-content">
                        <span class="info-label">Parentesco</span>
                        <span class="info-value">{{ $person->emergency_relationship }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- NFC Status -->
    @if($person->nfc_card_id)
    <div class="nfc-status">
        <div class="nfc-icon">
            <i class="fas fa-microchip"></i>
        </div>
        <div class="nfc-info">
            <span class="nfc-label">Tarjeta NFC Activa</span>
            <span class="nfc-id">ID: {{ $person->nfc_card_id }}</span>
        </div>
        <div class="nfc-check">
            <i class="fas fa-check-circle"></i>
        </div>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="card-footer">
        <div class="footer-brand">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>PROXICARD</span>
        </div>
        <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-vcard">
            <i class="fas fa-download"></i>
            <span>Descargar vCard</span>
        </a>
    </div>
</div>

<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });
</script>