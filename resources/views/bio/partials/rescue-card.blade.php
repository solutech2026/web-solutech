<div class="rescue-card">
    <div class="rescue-header">
        <div class="rescue-badge">
            <i class="fas fa-shield-alt"></i>
            <span>GRUPO DE RESCATISTAS</span>
            <i class="fas fa-heartbeat"></i>
        </div>
    </div>
    
    <div class="rescue-body">
        <div class="rescue-photo">
            @if($person->photo)
                <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
            @else
                <div class="photo-placeholder">
                    <i class="fas fa-user-shield"></i>
                </div>
            @endif
        </div>
        
        <h2 class="rescue-name">{{ strtoupper($person->full_name) }}</h2>
        
        <div class="rescue-info">
            <div class="info-row">
                <span class="info-label">CARGO:</span>
                <span class="info-value">{{ $person->rescue_member_category ?? $person->position ?? 'RESCATISTA' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">ID:</span>
                <span class="info-value">#{{ $person->rescue_member_number ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">VALIDO HASTA:</span>
                <span class="info-value">{{ $person->rescue_expiry_date ? \Carbon\Carbon::parse($person->rescue_expiry_date)->format('m/Y') : 'N/A' }}</span>
            </div>
        </div>
        
        <div class="rescue-critical">
            <h4>INFORMACIÓN CRÍTICA</h4>
            <div class="critical-item">
                <i class="fas fa-tint"></i>
                <span>GRUPO SANGUÍNEO: {{ $person->blood_type ?? 'N/A' }}</span>
            </div>
            <div class="critical-item">
                <i class="fas fa-allergies"></i>
                <span>ALERGIAS: {{ $person->allergies ?? 'NINGUNA' }}</span>
            </div>
        </div>
        
        <div class="rescue-emergency">
            <h4>CONTACTOS DE EMERGENCIA:</h4>
            @if($person->emergency_contact_name)
            <div class="emergency-contact">
                <i class="fas fa-user"></i>
                <span>{{ $person->emergency_contact_name }}: {{ $person->emergency_phone }}</span>
            </div>
            @endif
            <div class="emergency-contact">
                <i class="fas fa-phone-alt"></i>
                <span>ICE: {{ $person->emergency_phone ?? 'N/A' }}</span>
            </div>
        </div>
        
        <div class="rescue-motto">
            <span>★ PARA QUE OTROS VIVAN ★</span>
        </div>
    </div>
    
    <div class="rescue-footer">
        <div class="footer-badge">
            <i class="fas fa-microchip"></i>
            <span>PROXICARD</span>
        </div>
        <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-download">
            <i class="fas fa-download"></i> vCard
        </a>
    </div>
</div>