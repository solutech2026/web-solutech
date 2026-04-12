@extends('layouts.admin')

@section('title', 'Tarjetas NFC')

@section('header', 'Tarjetas NFC')

@section('content')
<div class="nfc-container">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="hero-text">
                    <h1>Gestión de Tarjetas NFC</h1>
                    <p>Administra las tarjetas de acceso para empleados y estudiantes</p>
                </div>
            </div>
            <div class="hero-right">
                <a href="{{ route('admin.nfc-cards.reader') }}" class="btn-reader-config">
                    <i class="fas fa-rss"></i>
                    <span>Configurar Lector</span>
                </a>
                <a href="{{ route('admin.nfc-cards.create') }}" class="btn-primary-modern">
                    <i class="fas fa-plus"></i>
                    <span>Nueva Tarjeta</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-modern">
        <div class="stat-card-glass">
            <div class="stat-icon-circle blue">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-info-glass">
                <h3>{{ $cards->count() }}</h3>
                <p>Total Tarjetas</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info-glass">
                <h3>{{ $cards->whereNotNull('assigned_to')->count() }}</h3>
                <p>Asignadas</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle orange">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info-glass">
                <h3>{{ $cards->whereNull('assigned_to')->count() }}</h3>
                <p>Sin Asignar</p>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="filters-bar-modern">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar por código, UID o persona asignada...">
        </div>
        
        <div class="filters-group">
            <select id="statusFilter" class="filter-select-modern">
                <option value="all">Todas las tarjetas</option>
                <option value="assigned">Asignadas</option>
                <option value="unassigned">Sin Asignar</option>
            </select>
            <button class="btn-export-modern" onclick="exportCards()">
                <i class="fas fa-download"></i> Exportar
            </button>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="cards-grid-modern" id="cardsGrid">
        @foreach($cards as $card)
        <div class="nfc-card-modern" 
             data-id="{{ $card->id }}"
             data-assigned="{{ $card->assigned_to ? 'true' : 'false' }}">
            
            <div class="card-chip-modern"></div>
            <div class="card-badge-modern {{ $card->assigned_to ? 'assigned' : 'unassigned' }}">
                {{ $card->assigned_to ? 'Asignada' : 'Sin Asignar' }}
            </div>
            
            <div class="card-body-modern">
                <div class="card-code-modern">{{ $card->card_code }}</div>
                @if($card->card_uid)
                <div class="card-uid-modern">UID: {{ $card->card_uid }}</div>
                @endif
                
                @if($card->assignedPerson)
                <div class="card-person-modern">
                    <i class="fas fa-user"></i>
                    <span>{{ $card->assignedPerson->full_name }}</span>
                </div>
                <div class="card-company-modern">
                    <i class="fas fa-building"></i> {{ $card->assignedPerson->company->name ?? 'N/A' }}
                </div>
                @endif
                
                @if($card->notes)
                <div class="card-notes-modern">
                    <i class="fas fa-sticky-note"></i> {{ Str::limit($card->notes, 50) }}
                </div>
                @endif
            </div>
            
            <div class="card-footer-modern">
                <button class="action-btn view" onclick="showCardDetails({{ $card->id }})">
                    <i class="fas fa-eye"></i> Ver
                </button>
                @if(!$card->assigned_to)
                <a href="{{ route('admin.nfc-cards.assign', $card->id) }}" class="action-btn assign">
                    <i class="fas fa-link"></i> Asignar
                </a>
                @endif
                <button class="action-btn delete" onclick="deleteCard({{ $card->id }})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($cards->isEmpty())
    <div class="empty-state-modern">
        <div class="empty-icon">
            <i class="fas fa-id-card"></i>
        </div>
        <h3>No hay tarjetas registradas</h3>
        <p>Comienza registrando tarjetas NFC para el control de acceso</p>
        <div class="empty-actions">
            <a href="{{ route('admin.nfc-cards.reader') }}" class="btn-reader-config">
                <i class="fas fa-rss"></i> Configurar Lector
            </a>
            <a href="{{ route('admin.nfc-cards.create') }}" class="btn-primary-modern">
                <i class="fas fa-plus"></i> Registrar primera tarjeta
            </a>
        </div>
    </div>
    @endif
</div>

<!-- Modal Detalles -->
<div class="modal fade modern-modal" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card"></i> Detalles de la Tarjeta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody">
                <div class="text-center">
                    <div class="loading-spinner">Cargando...</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nfc-cards.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
    function filterCards() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('.nfc-card-modern').forEach(card => {
            let show = true;
            const text = card.innerText.toLowerCase();
            const isAssigned = card.dataset.assigned === 'true';
            
            if (search && !text.includes(search)) show = false;
            if (status === 'assigned' && !isAssigned) show = false;
            if (status === 'unassigned' && isAssigned) show = false;
            
            card.style.display = show ? 'flex' : 'none';
        });
    }
    
    function showCardDetails(id) {
        fetch(`/admin/nfc-cards/${id}`)
            .then(response => response.json())
            .then(card => {
                const modalBody = document.getElementById('cardModalBody');
                modalBody.innerHTML = `
                    <div class="text-center">
                        <div class="card-chip-modern" style="position: relative; margin: 0 auto 1rem;"></div>
                        <h4 class="card-code-modern" style="color: #1f2937;">${card.card_code}</h4>
                        ${card.card_uid ? `<p><strong>UID:</strong> <code>${card.card_uid}</code></p>` : ''}
                        <hr>
                        <div class="text-start">
                            <p><strong>Estado:</strong> 
                                <span class="badge ${card.assigned_person ? 'bg-success' : 'bg-warning'}">
                                    ${card.assigned_person ? 'Asignada' : 'Sin asignar'}
                                </span>
                            </p>
                            ${card.assigned_person ? `
                                <p><strong>Asignada a:</strong> ${card.assigned_person.name} ${card.assigned_person.lastname || ''}</p>
                                ${card.assigned_person.company ? `<p><strong>Empresa/Colegio:</strong> ${card.assigned_person.company.name}</p>` : ''}
                                <p><strong>Cédula:</strong> ${card.assigned_person.document_id || 'No registrada'}</p>
                                <p><strong>Email:</strong> ${card.assigned_person.email || 'No registrado'}</p>
                            ` : ''}
                            <p><strong>Notas:</strong> ${card.notes || 'Sin notas'}</p>
                            <p><strong>Registrada:</strong> ${new Date(card.created_at).toLocaleString()}</p>
                            ${card.assigned_at ? `<p><strong>Asignada el:</strong> ${new Date(card.assigned_at).toLocaleString()}</p>` : ''}
                        </div>
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('cardModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('cardModalBody').innerHTML = '<div class="alert alert-danger">Error al cargar los detalles</div>';
            });
    }
    
    function deleteCard(id) {
        if (confirm('¿Eliminar esta tarjeta? Esta acción no se puede deshacer.')) {
            fetch(`/admin/nfc-cards/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al eliminar la tarjeta');
                }
            });
        }
    }
    
    function exportCards() {
        const status = document.getElementById('statusFilter').value;
        window.location.href = `/admin/nfc-cards/export?status=${status}`;
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterCards);
    document.getElementById('statusFilter')?.addEventListener('change', filterCards);
</script>
@endpush