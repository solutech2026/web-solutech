<div class="government-card">
    <div class="gov-header">
        <div class="gov-emblem">
            <i class="fas fa-landmark"></i>
        </div>
        <div class="gov-title">
            <h3>ASAMBLEA NACIONAL DE VENEZUELA</h3>
            <p>PODER LEGISLATIVO FEDERAL</p>
        </div>
    </div>
    
    <div class="gov-functions">
        <div class="function-item">
            <i class="fas fa-gavel"></i>
            <span>LEGISLAR</span>
        </div>
        <div class="function-item">
            <i class="fas fa-chart-line"></i>
            <span>CONTROLAR</span>
            <small>EL GOBIERNO</small>
        </div>
        <div class="function-item">
            <i class="fas fa-users"></i>
            <span>REPRESENTAR</span>
            <small>AL PUEBLO</small>
        </div>
    </div>
    
    <div class="gov-body">
        <div class="gov-photo">
            @if($person->photo)
                <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
            @else
                <div class="photo-placeholder">
                    <i class="fas fa-user-tie"></i>
                </div>
            @endif
        </div>
        
        <h2 class="gov-name">{{ strtoupper($person->full_name) }}</h2>
        
        <div class="gov-position">
            {{ $person->government_position_label ?? $person->government_position ?? $person->position ?? 'DIPUTADO' }}
        </div>
        
        <div class="gov-info">
            <div class="info-row">
                <span class="label">C.I.:</span>
                <span class="value">{{ $person->document_id ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">ESTADO:</span>
                <span class="value">{{ $person->company->state ?? $person->company->city ?? 'N/A' }}</span>
            </div>
        </div>
        
        <div class="gov-barcode">
            <div class="barcode-number">Nº {{ str_pad($person->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="barcode-expiry">VENCE: {{ $person->government_joining_date ? \Carbon\Carbon::parse($person->government_joining_date)->addYears(2)->format('d/m/Y') : '31/12/2026' }}</div>
        </div>
        
        <div class="gov-signature">
            <div class="signature-line"></div>
            <span>{{ strtoupper($person->full_name) }}</span>
        </div>
    </div>
    
    <div class="gov-footer">
        <div class="footer-badge">
            <i class="fas fa-microchip"></i>
            <span>PROXICARD</span>
        </div>
        <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-download">
            <i class="fas fa-download"></i> vCard
        </a>
    </div>
</div>