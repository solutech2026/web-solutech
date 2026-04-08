@extends('layouts.admin')

@section('title', 'Gestión de Personas')

@section('header', 'Personas')

@section('content')
<div class="persons-container">
    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="action-bar">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPersonModal">
                            <i class="fas fa-user-plus"></i> Nueva Persona
                        </button>
                        <button class="btn btn-outline-success" onclick="exportPersons()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nombre, cédula o email...">
                            <button class="btn-search" onclick="searchPersons()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <select class="filter-select" id="typeFilter">
                            <option value="all">Todos</option>
                            <option value="employee">Empleados</option>
                            <option value="visitor">Visitantes</option>
                        </select>
                        <select class="filter-select" id="companyFilter">
                            <option value="all">Todas las empresas</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">Total Personas</div>
                <div class="stat-value">{{ $persons->count() }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon success">
                    <i class="fas fa-briefcase"></i>
                </div>
                <div class="stat-title">Empleados</div>
                <div class="stat-value">{{ $persons->where('type', 'employee')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon warning">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-title">Visitantes</div>
                <div class="stat-value">{{ $persons->where('type', 'visitor')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="stat-icon info">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-title">Con Tarjeta NFC</div>
                <div class="stat-value">{{ $persons->whereNotNull('nfc_card_id')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Persons Grid -->
    <div class="row" id="personsGrid">
        @foreach($persons as $person)
        <div class="col-md-6 col-lg-4 mb-4 person-item" data-type="{{ $person->type }}" data-company="{{ $person->company_id }}" data-id="{{ $person->id }}">
            <div class="person-card">
                <div class="person-header">
                    <div class="person-avatar">
                        {{ substr($person->name, 0, 2) }}
                    </div>
                    <div class="person-info">
                        <h4 class="person-name">{{ $person->name }}</h4>
                        <span class="person-type {{ $person->type }}">
                            {{ $person->type == 'employee' ? 'Empleado' : 'Visitante' }}
                        </span>
                    </div>
                </div>
                <div class="person-details">
                    <div class="detail-item">
                        <i class="fas fa-building"></i>
                        <span>{{ $person->company->name ?? 'N/A' }}</span>
                    </div>
                    @if($person->document_id)
                    <div class="detail-item">
                        <i class="fas fa-id-card"></i>
                        <span>{{ $person->document_id }}</span>
                    </div>
                    @endif
                    @if($person->email)
                    <div class="detail-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ $person->email }}</span>
                    </div>
                    @endif
                    @if($person->phone)
                    <div class="detail-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ $person->phone }}</span>
                    </div>
                    @endif
                    @if($person->position)
                    <div class="detail-item">
                        <i class="fas fa-user-tie"></i>
                        <span>{{ $person->position }}</span>
                    </div>
                    @endif
                    @if($person->nfc_card_id)
                    <div class="detail-item">
                        <i class="fas fa-id-card"></i>
                        <span class="badge-nfc">NFC: {{ $person->nfc_card_id }}</span>
                    </div>
                    @endif
                </div>
                <div class="person-footer">
                    <a href="{{ route('admin.person.detail', $person->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> Ver
                    </a>
                    <button class="btn btn-warning" onclick="editPerson({{ $person->id }})">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    @if(!$person->nfc_card_id)
                        <button class="btn btn-primary" onclick="openAssignNFCModal({{ $person->id }}, '{{ $person->name }}')">
                            <i class="fas fa-id-card"></i> Asignar NFC
                        </button>
                    @endif
                    <button class="btn btn-danger" onclick="deletePerson({{ $person->id }})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Crear Persona -->
@include('admin.modals.create-person')

<!-- Modal Asignar Tarjeta NFC -->
<div class="modal fade" id="assignNFCModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card"></i> Asignar Tarjeta NFC
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="assignNFCForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Persona</label>
                        <p class="form-control-static" id="assignPersonName" style="font-weight: bold;"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Tarjeta NFC *</label>
                        <select name="card_id" id="nfcCardSelect" class="form-select" required>
                            <option value="">-- Seleccionar tarjeta disponible --</option>
                            @foreach($availableCards as $card)
                                <option value="{{ $card->id }}">
                                    {{ $card->card_code }} 
                                    @if($card->notes) - {{ $card->notes }} @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Solo se muestran tarjetas NFC disponibles (sin asignar)
                        </small>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Asignar Tarjeta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($availableCards->isEmpty())
<div class="alert alert-warning mt-3">
    <i class="fas fa-exclamation-triangle"></i>
    No hay tarjetas NFC disponibles para asignar. 
    <a href="{{ route('admin.nfc-cards.create') }}">Registra una nueva tarjeta</a> primero.
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons.css') }}">
<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.4);
    }
    .alert-warning a {
        color: #667eea;
        text-decoration: none;
    }
</style>
@endpush

@push('scripts')
<script>
    let currentPersonId = null;
    
    function searchPersons() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const type = document.getElementById('typeFilter').value;
        const company = document.getElementById('companyFilter').value;
        
        document.querySelectorAll('.person-item').forEach(item => {
            let show = true;
            const text = item.innerText.toLowerCase();
            
            if (search && !text.includes(search)) show = false;
            if (type !== 'all' && item.dataset.type !== type) show = false;
            if (company !== 'all' && item.dataset.company !== company) show = false;
            
            item.style.display = show ? '' : 'none';
        });
    }
    
    function editPerson(id) {
        currentPersonId = id;
        fetch(`/admin/persons/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('personModalTitle').innerHTML = '<i class="fas fa-user-edit"></i> Editar Persona';
                document.getElementById('personForm').action = `/admin/persons/${id}`;
                document.getElementById('personForm').method = 'POST';
                
                let methodInput = document.querySelector('input[name="_method"]');
                if (!methodInput) {
                    methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    document.getElementById('personForm').appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                document.querySelector('select[name="type"]').value = data.type;
                document.querySelector('select[name="company_id"]').value = data.company_id;
                document.querySelector('input[name="name"]').value = data.name;
                document.querySelector('input[name="document_id"]').value = data.document_id;
                document.querySelector('input[name="email"]').value = data.email;
                document.querySelector('input[name="phone"]').value = data.phone;
                document.querySelector('input[name="position"]').value = data.position;
                document.querySelector('select[name="department"]').value = data.department;
                document.querySelector('textarea[name="bio"]').value = data.bio;
                document.querySelector('input[name="companions"]').value = data.companions;
                document.querySelector('select[name="visit_reason"]').value = data.visit_reason;
                
                const event = new Event('change');
                document.getElementById('personType').dispatchEvent(event);
                
                new bootstrap.Modal(document.getElementById('createPersonModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos');
            });
    }
    
    function openAssignNFCModal(personId, personName) {
        document.getElementById('assignPersonName').innerText = personName;
        document.getElementById('assignNFCForm').action = `/admin/persons/${personId}/assign-nfc`;
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }
    
    function deletePerson(id) {
        if (confirm('¿Estás seguro de eliminar esta persona? Esta acción no se puede deshacer.')) {
            fetch(`/admin/persons/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al eliminar la persona');
                }
            });
        }
    }
    
    function exportPersons() {
        alert('Exportando lista de personas...');
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', searchPersons);
    document.getElementById('typeFilter')?.addEventListener('change', searchPersons);
    document.getElementById('companyFilter')?.addEventListener('change', searchPersons);
    
    document.getElementById('assignNFCForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const cardId = document.getElementById('nfcCardSelect').value;
        if (!cardId) {
            alert('Seleccione una tarjeta NFC');
            return;
        }
        this.submit();
    });
</script>
@endpush