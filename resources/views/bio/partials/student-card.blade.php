<div class="student-card">
    <div class="student-header">
        <div class="school-logo">
            @if($person->company && $person->company->logo)
                <img src="{{ asset('storage/' . $person->company->logo) }}" alt="Logo">
            @else
                <i class="fas fa-school"></i>
            @endif
        </div>
        <div class="school-name">{{ $person->company->name ?? 'FE Y ALEGRÍA VENEZUELA' }}</div>
    </div>
    
    <div class="student-body">
        <div class="student-photo">
            @if($person->photo)
                <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
            @else
                <div class="photo-placeholder">
                    <i class="fas fa-user-graduate"></i>
                </div>
            @endif
        </div>
        
        <h2 class="student-name">{{ strtoupper($person->full_name) }}</h2>
        
        <div class="student-info">
            <div class="info-row">
                <span class="label">C.I.:</span>
                <span class="value">{{ $person->document_id ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">AÑO:</span>
                <span class="value">{{ $person->grade_level_label ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">SECCIÓN:</span>
                <span class="value">{{ $person->section ?? 'B' }}</span>
            </div>
        </div>
        
        <div class="student-medical">
            <div class="medical-item">
                <i class="fas fa-tint"></i>
                <span>GRUPO SANGUÍNEO: {{ $person->blood_type ?? 'O+' }}</span>
            </div>
            @if($person->emergency_phone)
            <div class="medical-item">
                <i class="fas fa-phone-alt"></i>
                <span>CONTACTO DE EMERGENCIA: {{ $person->emergency_phone }}</span>
            </div>
            @endif
        </div>
        
        <div class="student-status">
            <i class="fas fa-check-circle"></i>
            <span>ESTUDIANTE</span>
            @if($person->nfc_card_id)
                <span class="nfc-badge">NFC ACTIVADO</span>
            @endif
        </div>
    </div>
    
    <div class="student-footer">
        <div class="footer-badge">
            <i class="fas fa-microchip"></i>
            <span>PROXICARD: Identidad y Acceso Rápido</span>
        </div>
        <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-download">
            <i class="fas fa-download"></i> vCard
        </a>
    </div>
</div>