@extends('layouts.admin')

@section('title', 'Control de Acceso')

@section('header', 'Control de Acceso')

@section('content')
<div class="access-control-container">
    <!-- Filtros -->
    <div class="filter-card">
        <h4>
            <i class="fas fa-filter"></i> Filtros de Búsqueda
        </h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Empresa / Colegio</label>
                <select class="form-select" id="companyFilter">
                    <option value="all">Todas las ubicaciones</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Categoría</label>
                <select class="form-select" id="categoryFilter">
                    <option value="all">Todos</option>
                    <option value="employee">Empleados</option>
                    <option value="school">Personal Escolar</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Nombre, apellido, cédula o NFC...">
            </div>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav access-tabs" id="accessTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="persons-tab" data-bs-toggle="tab" data-bs-target="#persons" type="button" role="tab">
                <i class="fas fa-address-card"></i> Personas Registradas
                <span class="badge">{{ $persons->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                <i class="fas fa-history"></i> Historial de Accesos
                <span class="badge">{{ $accessLogs->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Contenido de pestañas -->
    <div class="tab-content">
        <!-- Pestaña: Personas Registradas -->
        <div class="tab-pane fade show active" id="persons" role="tabpanel">
            <div class="persons-grid" id="personsGrid">
                @foreach($persons as $person)
                <div class="person-card" data-category="{{ $person->category }}" data-company="{{ $person->company_id }}">
                    <div class="person-header">
                        <div class="person-avatar">
                            {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                        </div>
                        <div class="person-info">
                            <div class="person-name">{{ $person->full_name }}</div>
                            <span class="person-type {{ $person->category }}">
                                {{ $person->category_label }}
                                @if($person->subcategory)
                                    <small>({{ $person->subcategory_label }})</small>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="person-details">
                        <div class="person-detail-item">
                            <i class="fas fa-building"></i>
                            <span>{{ $person->company->name ?? 'N/A' }}</span>
                        </div>
                        @if($person->document_id)
                        <div class="person-detail-item">
                            <i class="fas fa-id-card"></i>
                            <span>{{ $person->document_id }}</span>
                        </div>
                        @endif
                        @if($person->email)
                        <div class="person-detail-item">
                            <i class="fas fa-envelope"></i>
                            <span>{{ $person->email }}</span>
                        </div>
                        @endif
                        @if($person->phone)
                        <div class="person-detail-item">
                            <i class="fas fa-phone"></i>
                            <span>{{ $person->phone }}</span>
                        </div>
                        @endif
                        @if($person->position)
                        <div class="person-detail-item">
                            <i class="fas fa-user-tie"></i>
                            <span>{{ $person->position }}</span>
                        </div>
                        @endif
                        @if($person->nfc_card_id)
                        <div class="person-detail-item">
                            <i class="fas fa-id-card"></i>
                            <span class="badge-nfc">NFC Asignada</span>
                        </div>
                        @else
                        <div class="person-detail-item">
                            <i class="fas fa-id-card"></i>
                            <span class="badge-nfc-warning">Sin tarjeta NFC</span>
                        </div>
                        @endif
                    </div>
                    <div class="person-footer">
                        <a href="{{ route('admin.persons.show', $person->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                        <a href="{{ route('admin.persons.edit', $person->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        @if(!$person->nfc_card_id && $availableCards->count() > 0)
                            <button class="btn btn-primary" onclick="openAssignNFCModal({{ $person->id }}, '{{ $person->full_name }}')">
                                <i class="fas fa-id-card"></i> Asignar NFC
                            </button>
                        @endif
                        <button class="btn btn-danger" onclick="deletePerson({{ $person->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            @if($persons->isEmpty())
            <div class="empty-state">
                <i class="fas fa-address-book"></i>
                <h3>No hay personas registradas</h3>
                <p>Las personas registradas aparecerán aquí</p>
            </div>
            @endif
        </div>

        <!-- Pestaña: Historial de Accesos -->
        <div class="tab-pane fade" id="logs" role="tabpanel">
            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4><i class="fas fa-history"></i> Registros de Acceso</h4>
                    <button class="btn-outline-primary" onclick="exportLogs()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="logs-table">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Persona</th>
                                <th>Ubicación</th>
                                <th>Método</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accessLogs as $log)
                            <tr>
                                <td>{{ $log->access_time->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->person->full_name ?? 'N/A' }}</td>
                                <td>{{ $log->company->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $log->verification_method == 'nfc' ? 'badge-nfc' : 'badge-secondary' }}">
                                        {{ strtoupper($log->verification_method) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $log->status }}">
                                        {{ $log->status == 'granted' ? 'Permitido' : 'Denegado' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-door-open"></i> No hay registros de acceso
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Asignar Tarjeta NFC -->
<div class="modal fade" id="assignNFCModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
                    <div class="info-card-modal">
                        <i class="fas fa-user-circle"></i>
                        <div>
                            <label>Persona</label>
                            <p id="assignPersonName" style="font-weight: bold;"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Seleccionar Tarjeta NFC *</label>
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
                    <div class="alert-info">
                        <i class="fas fa-info-circle"></i>
                        Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary">Asignar Tarjeta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($availableCards->isEmpty())
<div class="alert-warning">
    <i class="fas fa-exclamation-triangle"></i>
    No hay tarjetas NFC disponibles para asignar. 
    <a href="{{ route('admin.nfc-cards.create') }}">Registra una nueva tarjeta</a> primero.
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/access-control.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
    function filterPersons() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const category = document.getElementById('categoryFilter').value;
        const company = document.getElementById('companyFilter').value;
        
        document.querySelectorAll('.person-card').forEach(card => {
            let show = true;
            const text = card.innerText.toLowerCase();
            const cardCategory = card.dataset.category;
            const cardCompany = card.dataset.company;
            
            if (search && !text.includes(search)) show = false;
            if (category !== 'all' && cardCategory !== category) show = false;
            if (company !== 'all' && cardCompany !== company) show = false;
            
            card.style.display = show ? 'flex' : 'none';
        });
    }
    
    function openAssignNFCModal(personId, personName) {
        document.getElementById('assignPersonName').innerText = personName;
        document.getElementById('assignNFCForm').action = `/admin/access-control/${personId}/assign-nfc`;
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }
    
    function deletePerson(id) {
        if (confirm('¿Eliminar esta persona? Esta acción no se puede deshacer.')) {
            fetch(`/admin/access-control/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }
    
    function exportLogs() {
        const params = new URLSearchParams({
            company: document.getElementById('companyFilter').value,
            search: document.getElementById('searchInput').value
        });
        window.location.href = `/admin/access-control/export-logs?${params.toString()}`;
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', filterPersons);
    document.getElementById('categoryFilter')?.addEventListener('change', filterPersons);
    document.getElementById('companyFilter')?.addEventListener('change', filterPersons);
</script>
@endpush