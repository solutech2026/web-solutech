@extends('layouts.admin')

@section('title', 'Detalle de Persona')

@section('header', 'Perfil de ' . $person->name)

@section('content')
<div class="person-detail-modern">
    <!-- Hero Section -->
    <div class="detail-hero">
        <div class="hero-backdrop"></div>
        <div class="hero-content">
            <div class="hero-avatar" style="background: {{ $person->avatar_color ?? 'linear-gradient(135deg, #6366f1, #a855f7)' }}">
                {{ substr($person->name, 0, 2) }}
            </div>
            <div class="hero-info">
                <h1>{{ $person->name }}</h1>
                <div class="hero-badges">
                    <span class="badge-category {{ $person->category }}">
                        <i class="fas {{ $person->category == 'employee' ? 'fa-briefcase' : 'fa-school' }}"></i>
                        {{ $person->category == 'employee' ? 'Empleado' : 'Personal Escolar' }}
                    </span>
                    @if($person->subcategory)
                    <span class="badge-subcategory {{ $person->subcategory }}">
                        <i class="fas {{ $person->subcategory == 'student' ? 'fa-graduation-cap' : ($person->subcategory == 'teacher' ? 'fa-chalkboard-user' : 'fa-building') }}"></i>
                        {{ ucfirst($person->subcategory) }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="hero-actions">
                <button class="action-btn-primary" onclick="editPerson({{ $person->id }})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="action-btn-danger" onclick="deletePerson({{ $person->id }})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Columna Izquierda - Información Principal -->
        <div class="detail-left">
            <!-- Tarjeta de Información -->
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-user-circle"></i>
                    <h3>Información Personal</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i>
                            <span>Cédula</span>
                        </div>
                        <div class="info-value">{{ $person->document_id ?? 'No registrada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            <span>Email</span>
                        </div>
                        <div class="info-value">{{ $person->email ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            <span>Teléfono</span>
                        </div>
                        <div class="info-value">{{ $person->phone ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i>
                            <span>{{ $person->category == 'employee' ? 'Empresa' : 'Colegio' }}</span>
                        </div>
                        <div class="info-value">{{ $person->company->name ?? 'N/A' }}</div>
                    </div>
                    @if($person->position)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo</span>
                        </div>
                        <div class="info-value">{{ $person->position }}</div>
                    </div>
                    @endif
                    @if($person->grade)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-book"></i>
                            <span>Grado</span>
                        </div>
                        <div class="info-value">{{ $person->grade }}</div>
                    </div>
                    @endif
                    @if($person->section)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-users"></i>
                            <span>Sección</span>
                        </div>
                        <div class="info-value">{{ $person->section }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Información Adicional -->
            @if($person->bio)
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-quote-left"></i>
                    <h3>Biografía</h3>
                </div>
                <div class="bio-content">
                    <p>{{ $person->bio }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha -->
        <div class="detail-right">
            <!-- Tarjeta NFC -->
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-id-card"></i>
                    <h3>Tarjeta NFC</h3>
                </div>
                <div class="nfc-content">
                    @if($person->nfc_card_id)
                        <div class="nfc-card-active">
                            <div class="nfc-chip">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="nfc-info">
                                <span class="nfc-label">ID Tarjeta</span>
                                <span class="nfc-code">{{ $person->nfc_card_id }}</span>
                            </div>
                            <div class="nfc-status-badge active">
                                <i class="fas fa-check-circle"></i> Activa
                            </div>
                            <button class="btn-unlink" onclick="unassignNFCCard({{ $person->id }})">
                                <i class="fas fa-unlink"></i> Desvincular
                            </button>
                        </div>
                    @else
                        <div class="nfc-card-empty">
                            <div class="empty-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <p>Sin tarjeta NFC asignada</p>
                            <button class="btn-assign" onclick="openAssignNFCModal({{ $person->id }})">
                                <i class="fas fa-plus"></i> Asignar Tarjeta
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas Rápidas -->
            <div class="stats-mini-grid">
                <div class="stat-mini">
                    <div class="stat-mini-icon blue">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Registro</span>
                        <span class="stat-mini-value">{{ $person->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                @if($person->last_access_at)
                <div class="stat-mini">
                    <div class="stat-mini-icon green">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Último acceso</span>
                        <span class="stat-mini-value">{{ $person->last_access_at->diffForHumans() }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Historial de Accesos -->
    <div class="access-history-card">
        <div class="card-header-glass">
            <i class="fas fa-history"></i>
            <h3>Historial de Accesos</h3>
            <span class="header-badge">{{ $person->accessLogs->count() }} registros</span>
        </div>
        <div class="access-table-container">
            <table class="access-table-modern">
                <thead>
                    <tr>
                        <th>Fecha y Hora</th>
                        <th>Ubicación</th>
                        <th>Método</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($person->accessLogs as $log)
                    <tr>
                        <td>
                            <div class="date-cell">
                                <i class="far fa-calendar-alt"></i>
                                {{ $log->access_time->format('d/m/Y H:i:s') }}
                            </div>
                        </td>
                        <td>{{ $log->gate ?? 'Puerta Principal' }}</td>
                        <td>
                            <span class="method-badge {{ $log->verification_method }}">
                                <i class="fas {{ $log->verification_method == 'nfc' ? 'fa-id-card' : 'fa-key' }}"></i>
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
                        <td colspan="4" class="empty-row">
                            <div class="empty-access">
                                <i class="fas fa-door-open"></i>
                                <p>No hay registros de acceso</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Persona -->
@include('admin.modals.edit-person')

<!-- Modal Asignar NFC -->
<div class="modal fade modern-modal" id="assignNFCModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-id-card"></i> Asignar Tarjeta NFC
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="info-card-modal">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <label>Persona</label>
                        <p id="assignPersonName"></p>
                    </div>
                </div>
                <div class="form-group-modern">
                    <label>Seleccionar Tarjeta NFC</label>
                    <select name="card_id" id="nfcCardSelect" class="form-select-modern" required>
                        <option value="">-- Seleccionar tarjeta --</option>
                        @foreach($availableCards ?? [] as $card)
                            <option value="{{ $card->id }}">{{ $card->card_code }} @if($card->notes) - {{ $card->notes }} @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="alert-modern-info">
                    <i class="fas fa-info-circle"></i>
                    Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-primary-modern" onclick="assignNFCCard()">Asignar Tarjeta</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentPersonId = null;
    
    function editPerson(id) {
        window.location.href = `/admin/persons/${id}/edit`;
    }
    
    function deletePerson(id) {
        if (confirm('¿Eliminar esta persona? Esta acción no se puede deshacer.')) {
            fetch(`/admin/persons/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.href = '{{ route("admin.persons.index") }}');
        }
    }
    
    function openAssignNFCModal(personId) {
        currentPersonId = personId;
        document.getElementById('assignPersonName').innerHTML = document.querySelector('.hero-info h1').innerText;
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }
    
    function assignNFCCard() {
        const cardId = document.getElementById('nfcCardSelect').value;
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
            body: JSON.stringify({ person_id: currentPersonId })
        }).then(response => {
            if (response.ok) {
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
</script>
@endpush