<div class="default-card">
    <div class="default-header">
        <div class="default-avatar">
            @if($person->photo)
                <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
            @else
                <div class="avatar-placeholder">
                    {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                </div>
            @endif
        </div>
        <h1 class="default-name">{{ $person->full_name }}</h1>
        <div class="default-role">Colaborador</div>
    </div>
    
    <div class="default-body">
        <div class="default-info">
            @if($person->document_id)
            <div class="info-row">
                <i class="fas fa-id-card"></i>
                <span>{{ $person->document_id }}</span>
            </div>
            @endif
            @if($person->email)
            <div class="info-row">
                <i class="fas fa-envelope"></i>
                <span>{{ $person->email }}</span>
            </div>
            @endif
            @if($person->phone)
            <div class="info-row">
                <i class="fas fa-phone"></i>
                <span>{{ $person->phone }}</span>
            </div>
            @endif
        </div>
    </div>
    
    <div class="default-footer">
        <div class="footer-badge">
            <i class="fas fa-microchip"></i>
            <span>PROXICARD</span>
        </div>
        <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-download">
            <i class="fas fa-download"></i> vCard
        </a>
    </div>
</div>