@extends('layouts.admin')

@section('title', 'Tarjetas NFC')

@section('header', 'Tarjetas NFC')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nfc-cards.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-id-card text-primary me-2"></i>
                Gestión de Tarjetas NFC
            </h1>
            <p class="text-muted mb-0">Administra las tarjetas de acceso</p>
        </div>
        <div>
            <a href="{{ route('admin.nfc-cards.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Tarjeta
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card bg-primary bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Tarjetas</h6>
                            <h2 class="mb-0">{{ $cards->count() }}</h2>
                        </div>
                        <i class="fas fa-id-card fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Asignadas</h6>
                            <h2 class="mb-0">{{ $cards->whereNotNull('assigned_to')->count() }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning bg-opacity-10 border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Sin Asignar</h6>
                            <h2 class="mb-0">{{ $cards->whereNull('assigned_to')->count() }}</h2>
                        </div>
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Buscar por código, UID o persona...">
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="statusFilter" class="form-select">
                        <option value="all">Todas las tarjetas</option>
                        <option value="assigned">Asignadas</option>
                        <option value="unassigned">Sin Asignar</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" onclick="exportCards()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="row" id="cardsGrid">
        @forelse($cards as $card)
        <div class="col-md-6 col-lg-4 mb-4 card-item" data-assigned="{{ $card->assigned_to ? 'assigned' : 'unassigned' }}">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="bg-light rounded p-2">
                            <i class="fas fa-microchip fa-2x text-primary"></i>
                        </div>
                        <span class="badge {{ $card->assigned_to ? 'bg-success' : 'bg-warning' }}">
                            {{ $card->assigned_to ? 'Asignada' : 'Sin Asignar' }}
                        </span>
                    </div>
                    
                    <h5 class="card-title text-center mb-3">{{ $card->card_code }}</h5>
                    
                    @if($card->card_uid)
                    <p class="small text-muted text-center mb-3">
                        <i class="fas fa-key"></i> UID: {{ $card->card_uid }}
                    </p>
                    @endif
                    
                    @if($card->assignedPerson)
                    <hr>
                    <div class="small">
                        <p class="mb-1">
                            <i class="fas fa-user text-primary me-1"></i>
                            <strong>{{ $card->assignedPerson->full_name }}</strong>
                        </p>
                        @if($card->assignedPerson->company)
                        <p class="mb-1 text-muted">
                            <i class="fas fa-building me-1"></i> {{ $card->assignedPerson->company->name }}
                        </p>
                        @endif
                        @if($card->assignedPerson->document_id)
                        <p class="mb-1 text-muted">
                            <i class="fas fa-id-card me-1"></i> {{ $card->assignedPerson->document_id }}
                        </p>
                        @endif
                    </div>
                    @endif
                    
                    @if($card->notes)
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-sticky-note me-1"></i> {{ Str::limit($card->notes, 50) }}
                    </p>
                    @endif
                </div>
                
                <div class="card-footer bg-transparent border-top-0">
                    <div class="btn-group w-100">
                        <button class="btn btn-sm btn-outline-info" onclick="showCardDetails({{ $card->id }})">
                            <i class="fas fa-eye"></i> Ver
                        </button>
                        @if(!$card->assigned_to)
                        <a href="{{ route('admin.nfc-cards.assign.form', $card->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="fas fa-link"></i> Asignar
                        </a>
                        @endif
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCard({{ $card->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-id-card fa-4x text-muted mb-3"></i>
                <h4>No hay tarjetas registradas</h4>
                <p class="text-muted">Comienza registrando tarjetas NFC</p>
                <a href="{{ route('admin.nfc-cards.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Registrar primera tarjeta
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card text-primary me-2"></i>
                    Detalles de la Tarjeta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Cargando...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function filterCards() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('.card-item').forEach(card => {
            let show = true;
            const text = card.innerText.toLowerCase();
            const isAssigned = card.dataset.assigned === 'assigned';
            
            if (search && !text.includes(search)) show = false;
            if (status === 'assigned' && !isAssigned) show = false;
            if (status === 'unassigned' && isAssigned) show = false;
            
            card.style.display = show ? '' : 'none';
        });
    }
    
    function showCardDetails(id) {
        fetch(`/admin/nfc-cards/${id}`)
            .then(response => response.json())
            .then(card => {
                const modalBody = document.getElementById('cardModalBody');
                modalBody.innerHTML = `
                    <div class="text-center">
                        <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                        <h4>${card.card_code}</h4>
                        ${card.card_uid ? `<p><strong>UID:</strong> <code>${card.card_uid}</code></p>` : ''}
                        <hr>
                        <div class="text-start">
                            <p><strong>Estado:</strong> 
                                <span class="badge ${card.assigned_person ? 'bg-success' : 'bg-warning'}">
                                    ${card.assigned_person ? 'Asignada' : 'Sin asignar'}
                                </span>
                            </p>
                            ${card.assigned_person ? `
                                <p><strong>Asignada a:</strong> ${card.assigned_person.full_name}</p>
                                ${card.assigned_person.document_id ? `<p><strong>Cédula:</strong> ${card.assigned_person.document_id}</p>` : ''}
                                ${card.assigned_person.email ? `<p><strong>Email:</strong> ${card.assigned_person.email}</p>` : ''}
                                ${card.assigned_person.company ? `<p><strong>Empresa:</strong> ${card.assigned_person.company.name}</p>` : ''}
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
                document.getElementById('cardModalBody').innerHTML = '<div class="alert alert-danger">Error al cargar los detalles</div>';
            });
    }
    
    function deleteCard(id) {
        if (confirm('¿Eliminar esta tarjeta?')) {
            fetch(`/admin/nfc-cards/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(response => {
                if (response.ok) location.reload();
                else alert('Error al eliminar');
            });
        }
    }
    
    function exportCards() {
        const status = document.getElementById('statusFilter').value;
        window.location.href = `/admin/nfc-cards/export/csv?status=${status}`;
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterCards);
    document.getElementById('statusFilter')?.addEventListener('change', filterCards);
</script>
@endpush