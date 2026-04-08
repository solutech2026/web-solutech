@extends('layouts.admin')

@section('title', 'Detalle de Persona')

@section('header', 'Perfil de ' . $person->name)

@section('content')
<div class="person-detail-container">
    <div class="row">
        <!-- Columna Izquierda - Perfil -->
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <div class="profile-avatar">
                    {{ substr($person->name, 0, 2) }}
                </div>
                <h3 class="profile-name">{{ $person->name }}</h3>
                <p class="profile-role">
                    <i class="fas {{ $person->type == 'employee' ? 'fa-briefcase' : 'fa-user-friends' }}"></i>
                    {{ $person->type == 'employee' ? 'Empleado' : 'Visitante' }}
                </p>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <i class="fas fa-building"></i>
                        <span>{{ $person->company->name ?? 'N/A' }}</span>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Registro: {{ $person->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($person->last_access_at)
                    <div class="stat-item">
                        <i class="fas fa-clock"></i>
                        <span>Último acceso: {{ $person->last_access_at->diffForHumans() }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="profile-actions">
                    <button class="btn btn-warning w-100 mb-2" onclick="editPerson({{ $person->id }})">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </button>
                    <button class="btn btn-danger w-100" onclick="deletePerson({{ $person->id }})">
                        <i class="fas fa-trash"></i> Eliminar Persona
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Columna Derecha - Información Detallada -->
        <div class="col-md-8 mb-4">
            <!-- Pestañas -->
            <div class="info-card">
                <ul class="nav nav-tabs" id="detailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                            <i class="fas fa-info-circle"></i> Información
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nfc-tab" data-bs-toggle="tab" data-bs-target="#nfc" type="button" role="tab">
                            <i class="fas fa-id-card"></i> Tarjeta NFC
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="access-tab" data-bs-toggle="tab" data-bs-target="#access" type="button" role="tab">
                            <i class="fas fa-history"></i> Historial de Accesos
                        </button>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <!-- Pestaña Información -->
                    <div class="tab-pane fade show active" id="info" role="tabpanel">
                        <div class="info-grid">
                            <div class="info-item">
                                <label><i class="fas fa-id-card"></i> Cédula:</label>
                                <p>{{ $person->document_id ?? 'No registrada' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-envelope"></i> Email:</label>
                                <p>{{ $person->email ?? 'No registrado' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-phone"></i> Teléfono:</label>
                                <p>{{ $person->phone ?? 'No registrado' }}</p>
                            </div>
                            @if($person->type == 'employee')
                            <div class="info-item">
                                <label><i class="fas fa-user-tie"></i> Cargo:</label>
                                <p>{{ $person->position ?? 'No especificado' }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-layer-group"></i> Departamento:</label>
                                <p>{{ $person->department ?? 'No especificado' }}</p>
                            </div>
                            @endif
                            @if($person->type == 'visitor')
                            <div class="info-item">
                                <label><i class="fas fa-users"></i> Acompañantes:</label>
                                <p>{{ $person->companions ?? 0 }}</p>
                            </div>
                            <div class="info-item">
                                <label><i class="fas fa-question-circle"></i> Motivo:</label>
                                <p>{{ ucfirst($person->visit_reason ?? 'No especificado') }}</p>
                            </div>
                            @endif
                        </div>
                        
                        @if($person->bio)
                        <div class="bio-section">
                            <h5><i class="fas fa-quote-left"></i> Biografía</h5>
                            <p>{{ $person->bio }}</p>
                        </div>
                        @endif
                        
                        @if($person->bio_url)
                        <div class="bio-url-section">
                            <i class="fas fa-link"></i>
                            <a href="{{ url($person->bio_url) }}" target="_blank">Ver perfil público</a>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Pestaña Tarjeta NFC -->
                    <div class="tab-pane fade" id="nfc" role="tabpanel">
                        <div class="nfc-management">
                            @if($person->nfc_card_id)
                                <div class="nfc-card-active">
                                    <div class="nfc-icon-large">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <h4>Tarjeta NFC Asignada</h4>
                                    <div class="nfc-card-code">{{ $person->nfc_card_id }}</div>
                                    <div class="nfc-status active">
                                        <i class="fas fa-check-circle"></i> Activa
                                    </div>
                                    <button class="btn btn-danger mt-3" onclick="unassignNFCCard({{ $person->id }})">
                                        <i class="fas fa-unlink"></i> Desvincular Tarjeta
                                    </button>
                                </div>
                            @else
                                <div class="nfc-card-empty">
                                    <div class="nfc-icon-large">
                                        <i class="fas fa-id-card"></i>
                                    </div>
                                    <h4>Sin Tarjeta NFC Asignada</h4>
                                    <p>Asigna una tarjeta NFC para control de acceso</p>
                                    
                                    <div class="assign-nfc-form mt-4">
                                        <label class="form-label">Seleccionar Tarjeta Disponible</label>
                                        <div class="input-group">
                                            <select id="nfcCardSelect" class="form-select">
                                                <option value="">Seleccionar tarjeta...</option>
                                                @foreach($availableCards as $card)
                                                    <option value="{{ $card->id }}" data-code="{{ $card->card_code }}">
                                                        {{ $card->card_code }} 
                                                        @if($card->notes) - {{ $card->notes }} @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button class="btn btn-primary" onclick="assignNFCCard({{ $person->id }})">
                                                <i class="fas fa-link"></i> Asignar
                                            </button>
                                        </div>
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle"></i> 
                                            <a href="{{ route('admin.nfc-cards.create') }}">Crear nueva tarjeta NFC</a> si no hay disponibles
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Pestaña Historial de Accesos -->
                    <div class="tab-pane fade" id="access" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table access-table">
                                <thead>
                                    <tr>
                                        <th>Fecha/Hora</th>
                                        <th>Puerta</th>
                                        <th>Método</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($person->accessLogs as $log)
                                    <tr>
                                        <td>{{ $log->access_time->format('d/m/Y H:i:s') }}</td>
                                        <td>{{ $log->gate ?? 'Puerta Principal' }}</td>
                                        <td>
                                            <span class="badge {{ $log->verification_method == 'nfc' ? 'bg-info' : 'bg-secondary' }}">
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
                                            <td colspan="4" class="text-center">No hay registros de acceso</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Persona -->
<div class="modal fade" id="editPersonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Persona
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPersonForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="type" id="editPersonType" class="form-select" required>
                                <option value="employee">Empleado</option>
                                <option value="visitor">Visitante</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Empresa / Ubicación *</label>
                            <select name="company_id" id="editCompanyId" class="form-select" required>
                                <option value="">Seleccionar</option>
                                @foreach($companies ?? [] as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre completo *</label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de cédula</label>
                            <input type="text" name="document_id" id="editDocumentId" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" id="editPhone" class="form-control">
                        </div>
                        
                        <div class="edit-employee-fields">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo / Posición</label>
                                <input type="text" name="position" id="editPosition" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento</label>
                                <select name="department" id="editDepartment" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Administración">Administración</option>
                                    <option value="Tecnología">Tecnología</option>
                                    <option value="Ventas">Ventas</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Recursos Humanos">Recursos Humanos</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Biografía</label>
                                <textarea name="bio" id="editBio" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="edit-visitor-fields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de acompañantes</label>
                                <input type="number" name="companions" id="editCompanions" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Motivo de visita</label>
                                <select name="visit_reason" id="editVisitReason" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="recreación">Recreación</option>
                                    <option value="deporte">Deporte</option>
                                    <option value="evento">Evento especial</option>
                                    <option value="turismo">Turismo</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Persona</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .person-detail-container {
        padding: 0;
    }
    
    .profile-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 48px;
        font-weight: bold;
        color: white;
    }
    
    .profile-name {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .profile-role {
        color: #667eea;
        margin-bottom: 20px;
    }
    
    .profile-stats {
        text-align: left;
        margin: 20px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 16px;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
    }
    
    .stat-item i {
        width: 20px;
        color: #667eea;
    }
    
    .profile-actions {
        margin-top: 20px;
    }
    
    .info-card {
        background: white;
        border-radius: 24px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border: 1px solid #e5e7eb;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .info-item label {
        font-size: 12px;
        color: #6b7280;
        display: block;
        margin-bottom: 5px;
    }
    
    .info-item p {
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }
    
    .bio-section, .bio-url-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    .nfc-management {
        text-align: center;
        padding: 30px;
    }
    
    .nfc-icon-large {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .nfc-icon-large i {
        font-size: 48px;
        color: white;
    }
    
    .nfc-card-code {
        font-family: monospace;
        font-size: 20px;
        background: #f3f4f6;
        padding: 10px 20px;
        border-radius: 12px;
        display: inline-block;
        margin: 15px 0;
    }
    
    .nfc-status.active {
        display: inline-block;
        padding: 5px 15px;
        background: #10b981;
        color: white;
        border-radius: 20px;
        font-size: 12px;
    }
    
    .status-badge.granted {
        background: #10b981;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
    }
    
    .status-badge.denied {
        background: #ef4444;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
    }
    
    .access-table {
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentPerson = null;
    
    function editPerson(id) {
        fetch(`/admin/persons/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                currentPerson = data;
                
                document.getElementById('editPersonForm').action = `/admin/persons/${id}`;
                document.getElementById('editPersonType').value = data.type;
                document.getElementById('editCompanyId').value = data.company_id;
                document.getElementById('editName').value = data.name;
                document.getElementById('editDocumentId').value = data.document_id;
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editPhone').value = data.phone;
                document.getElementById('editPosition').value = data.position;
                document.getElementById('editDepartment').value = data.department;
                document.getElementById('editBio').value = data.bio;
                document.getElementById('editCompanions').value = data.companions;
                document.getElementById('editVisitReason').value = data.visit_reason;
                
                // Mostrar/ocultar campos según tipo
                const employeeFields = document.querySelector('.edit-employee-fields');
                const visitorFields = document.querySelector('.edit-visitor-fields');
                
                if (data.type === 'employee') {
                    employeeFields.style.display = 'block';
                    visitorFields.style.display = 'none';
                } else {
                    employeeFields.style.display = 'none';
                    visitorFields.style.display = 'block';
                }
                
                new bootstrap.Modal(document.getElementById('editPersonModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos');
            });
    }
    
    function deletePerson(id) {
        if (confirm('¿Eliminar esta persona?')) {
            fetch(`/admin/persons/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.href = '{{ route("admin.persons.index") }}');
        }
    }
    
    function assignNFCCard(personId) {
        const select = document.getElementById('nfcCardSelect');
        const cardId = select.value;
        const cardCode = select.options[select.selectedIndex]?.dataset.code;
        
        if (!cardId) {
            alert('Seleccione una tarjeta NFC');
            return;
        }
        
        fetch(`/admin/nfc-cards/${cardId}/assign`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ person_id: personId })
        }).then(response => {
            if (response.ok) {
                alert(`Tarjeta ${cardCode} asignada correctamente`);
                location.reload();
            } else {
                alert('Error al asignar la tarjeta');
            }
        });
    }
    
    function unassignNFCCard(personId) {
        if (confirm('¿Desvincular la tarjeta NFC de esta persona?')) {
            fetch(`/admin/persons/${personId}/unassign-nfc`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }
    
    document.getElementById('editPersonType')?.addEventListener('change', function() {
        const employeeFields = document.querySelector('.edit-employee-fields');
        const visitorFields = document.querySelector('.edit-visitor-fields');
        
        if (this.value === 'employee') {
            employeeFields.style.display = 'block';
            visitorFields.style.display = 'none';
        } else {
            employeeFields.style.display = 'none';
            visitorFields.style.display = 'block';
        }
    });
</script>
@endpush