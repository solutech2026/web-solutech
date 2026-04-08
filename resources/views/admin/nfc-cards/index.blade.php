@extends('layouts.admin')

@section('title', 'Tarjetas NFC')

@section('header', 'Tarjetas NFC')

@section('content')
<div class="nfc-container">
    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="action-bar">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <a href="{{ route('admin.nfc-cards.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Tarjeta
                        </a>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por código o asignado a...">
                            <button class="btn-search" onclick="filterCards()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <select class="filter-select" id="statusFilter" onchange="filterCards()">
                            <option value="all">Todas las tarjetas</option>
                            <option value="assigned">Asignadas</option>
                            <option value="unassigned">Sin Asignar</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-title">Total Tarjetas</div>
                <div class="stat-value">{{ $cards->count() }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-title">Asignadas</div>
                <div class="stat-value">{{ $cards->whereNotNull('person_id')->count() }}</div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-title">Sin Asignar</div>
                <div class="stat-value">{{ $cards->whereNull('person_id')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Cards Grid -->
    <div class="cards-grid" id="cardsGrid">
        @foreach($cards as $card)
        <div class="card-item" data-id="{{ $card->id }}" data-assigned="{{ $card->person_id ? 'true' : 'false' }}">
            <div class="card-chip"></div>
            <div class="card-badge {{ $card->person_id ? 'assigned' : 'unassigned' }}">
                {{ $card->person_id ? 'Asignada' : 'Sin Asignar' }}
            </div>
            <div class="card-code">{{ $card->card_code }}</div>
            <div class="card-assigned">
                <i class="fas {{ $card->person_id ? 'fa-user' : 'fa-user-plus' }}"></i>
                {{ $card->person ? $card->person->name : 'Sin asignar' }}
            </div>
            @if($card->person)
            <div class="card-person-info">
                <small>
                    <i class="fas fa-building"></i> {{ $card->person->company->name ?? 'N/A' }}
                </small>
            </div>
            @endif
            @if($card->notes)
            <div class="card-notes">
                <small><i class="fas fa-sticky-note"></i> {{ Str::limit($card->notes, 50) }}</small>
            </div>
            @endif
            <div class="card-footer">
                <button class="btn btn-sm btn-info" onclick="showCardDetails({{ $card->id }})">
                    <i class="fas fa-eye"></i> Ver
                </button>
                @if(!$card->person_id)
                <a href="{{ route('admin.nfc-cards.assign', $card->id) }}" class="btn btn-sm btn-warning">
                    <i class="fas fa-link"></i> Asignar
                </a>
                @endif
                <button class="btn btn-sm btn-danger" onclick="deleteCard({{ $card->id }})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($cards->isEmpty())
    <div class="empty-state">
        <i class="fas fa-id-card fa-4x mb-3"></i>
        <h5>No hay tarjetas registradas</h5>
        <p>Registra tu primera tarjeta NFC</p>
        <a href="{{ route('admin.nfc-cards.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Registrar Tarjeta
        </a>
    </div>
    @endif
</div>

<!-- Modal Detalles -->
<div class="modal fade" id="cardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card"></i> Detalles de la Tarjeta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cardModalBody">
                <!-- Contenido dinámico -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nfc-container {
        padding: 0;
    }
    
    .action-bar {
        background: white;
        border-radius: 20px;
        padding: 20px 25px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 12px;
        padding: 10px 20px;
    }
    
    .search-box {
        position: relative;
    }
    
    .search-box .form-control {
        border-radius: 12px;
        padding: 10px 15px;
        padding-right: 40px;
        min-width: 250px;
    }
    
    .btn-search {
        position: absolute;
        right: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
    }
    
    .filter-select {
        border-radius: 12px;
        padding: 10px 15px;
        border: 1px solid #e5e7eb;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
    }
    
    .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
    .stat-icon.success { background: #10b981; color: white; }
    .stat-icon.warning { background: #f59e0b; color: white; }
    
    .stat-title { font-size: 13px; color: #6b7280; margin-bottom: 5px; }
    .stat-value { font-size: 28px; font-weight: 700; color: #1f2937; }
    
    .cards-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .card-item {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border-radius: 20px;
        padding: 20px;
        color: white;
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .card-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    .card-chip {
        width: 45px;
        height: 35px;
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .card-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .card-badge.assigned {
        background: #10b981;
        color: white;
    }
    
    .card-badge.unassigned {
        background: #f59e0b;
        color: white;
    }
    
    .card-code {
        font-family: monospace;
        font-size: 18px;
        letter-spacing: 1px;
        margin: 10px 0;
    }
    
    .card-assigned, .card-person-info, .card-notes {
        font-size: 12px;
        margin: 8px 0;
        opacity: 0.8;
    }
    
    .card-footer {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.1);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px;
        background: white;
        border-radius: 20px;
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function filterCards() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const status = document.getElementById('statusFilter').value;
        
        document.querySelectorAll('.card-item').forEach(card => {
            let show = true;
            const text = card.innerText.toLowerCase();
            const isAssigned = card.dataset.assigned === 'true';
            
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
                        <div class="card-chip mx-auto mb-3"></div>
                        <h4>${card.card_code}</h4>
                        <hr>
                        <div class="text-start">
                            <p><strong>Estado:</strong> 
                                <span class="badge ${card.person ? 'bg-success' : 'bg-warning'}">
                                    ${card.person ? 'Asignada' : 'Sin asignar'}
                                </span>
                            </p>
                            ${card.person ? `<p><strong>Asignada a:</strong> ${card.person.name}</p>` : ''}
                            ${card.person && card.person.company ? `<p><strong>Empresa:</strong> ${card.person.company.name}</p>` : ''}
                            ${card.person && card.person.type ? `<p><strong>Tipo:</strong> ${card.person.type === 'employee' ? 'Empleado' : 'Visitante'}</p>` : ''}
                            <p><strong>Notas:</strong> ${card.notes || 'Sin notas'}</p>
                            <p><strong>Registrada:</strong> ${new Date(card.created_at).toLocaleString()}</p>
                            ${card.assigned_at ? `<p><strong>Asignada:</strong> ${new Date(card.assigned_at).toLocaleString()}</p>` : ''}
                        </div>
                    </div>
                `;
                new bootstrap.Modal(document.getElementById('cardModal')).show();
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
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterCards);
</script>
@endpush