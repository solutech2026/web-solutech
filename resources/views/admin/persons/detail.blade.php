@extends('layouts.admin')

@section('title', 'Detalle de Persona')

@section('header', 'Perfil de ' . $person->full_name)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons-show.css') }}">
<style>
    /* Estilos adicionales para secciones específicas */
    .rescue-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        border: 1px solid #e94560;
    }
    
    .rescue-card .card-header-glass i {
        color: #e94560;
    }
    
    .government-card {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border: 1px solid #f59e0b;
    }
    
    .government-card .card-header-glass i {
        color: #f59e0b;
    }
    
    .info-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 8px;
    }
    
    .info-badge.rescue {
        background: #e94560;
        color: white;
    }
    
    .info-badge.government {
        background: #f59e0b;
        color: white;
    }
    
    .carnet-mini-preview {
        background: #0f0f23;
        border-radius: 12px;
        padding: 12px;
        margin-top: 15px;
        border-left: 3px solid #e94560;
    }
    
    .carnet-mini-preview p {
        margin: 5px 0;
        font-size: 12px;
    }
    
    .carnet-mini-preview strong {
        color: #e94560;
    }
</style>
@endpush

@section('content')
<div class="person-detail-modern">
    <!-- Hero Section -->
    <div class="detail-hero">
        <div class="hero-backdrop"></div>
        <div class="hero-content">
            <div class="hero-avatar">
                @if($person->photo)
                    <img src="{{ asset('storage/' . $person->photo) }}" alt="{{ $person->full_name }}">
                @else
                    <div class="avatar-initials" style="background: {{ $person->avatar_color ?? 'linear-gradient(135deg, #667eea, #764ba2)' }}">
                        {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <div class="hero-info">
                <h1>{{ $person->full_name }}</h1>
                <div class="hero-badges">
                    <span class="badge-category {{ $person->institution_type ?? $person->category }}">
                        <i class="fas {{ ($person->institution_type ?? $person->category) == 'company' ? 'fa-briefcase' : (($person->institution_type ?? $person->category) == 'ngo_rescue' ? 'fa-heartbeat' : (($person->institution_type ?? $person->category) == 'government' ? 'fa-landmark' : 'fa-school')) }}"></i>
                        @if(($person->institution_type ?? $person->category) == 'company')
                            Empleado
                        @elseif(($person->institution_type ?? $person->category) == 'ngo_rescue')
                            ONG de Rescate
                        @elseif(($person->institution_type ?? $person->category) == 'government')
                            Gobierno
                        @else
                            Personal Escolar
                        @endif
                    </span>
                    @if($person->subcategory)
                    <span class="badge-subcategory {{ $person->subcategory }}">
                        <i class="fas {{ $person->subcategory == 'student' ? 'fa-graduation-cap' : ($person->subcategory == 'teacher' ? 'fa-chalkboard-user' : 'fa-building') }}"></i>
                        @if($person->subcategory == 'student') Estudiante
                        @elseif($person->subcategory == 'teacher') Docente
                        @else Administrativo @endif
                    </span>
                    @endif
                    @if($person->rescue_member_number)
                    <span class="badge-subcategory rescue">
                        <i class="fas fa-id-card"></i>
                        Miembro #{{ $person->rescue_member_number }}
                    </span>
                    @endif
                    @if($person->government_position)
                    <span class="badge-subcategory government">
                        <i class="fas fa-user-tie"></i>
                        {{ $person->government_position_label ?? $person->government_position }}
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
            <!-- Tarjeta de Información Personal -->
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-user-circle"></i>
                    <h3>Información Personal</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i>
                            <span>Cédula / Documento</span>
                        </div>
                        <div class="info-value">{{ $person->document_id ?? 'No registrada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            <span>Correo electrónico</span>
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
                            <i class="fas fa-venus-mars"></i>
                            <span>Género</span>
                        </div>
                        <div class="info-value">
                            @if($person->gender == 'male') Masculino
                            @elseif($person->gender == 'female') Femenino
                            @elseif($person->gender == 'other') Otro
                            @else No especificado @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Fecha de nacimiento</span>
                        </div>
                        <div class="info-value">{{ $person->birth_date ? \Carbon\Carbon::parse($person->birth_date)->format('d/m/Y') : 'No registrada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i>
                            <span>Institución</span>
                        </div>
                        <div class="info-value">{{ $person->company->name ?? 'N/A' }}</div>
                    </div>
                    @if($person->position)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo / Posición</span>
                        </div>
                        <div class="info-value">{{ $person->position }}</div>
                    </div>
                    @endif
                    @if($person->department)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            <span>Departamento / Área</span>
                        </div>
                        <div class="info-value">{{ $person->department }}</div>
                    </div>
                    @endif
                    @if($person->grade_level)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-book"></i>
                            <span>Grado</span>
                        </div>
                        <div class="info-value">{{ $person->grade_level_label ?? $person->grade_level }}</div>
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
                    @if($person->academic_year)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-week"></i>
                            <span>Año Escolar</span>
                        </div>
                        <div class="info-value">{{ $person->academic_year }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tarjeta de Salud y Emergencia -->
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-heartbeat"></i>
                    <h3>Información de Salud</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-tint"></i>
                            <span>Tipo de Sangre</span>
                        </div>
                        <div class="info-value">{{ $person->blood_type ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-allergies"></i>
                            <span>Alergias</span>
                        </div>
                        <div class="info-value">{{ $person->allergies ?? 'No registradas' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-heartbeat"></i>
                            <span>Condiciones Médicas</span>
                        </div>
                        <div class="info-value">{{ $person->medical_conditions ?? 'No registradas' }}</div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Contacto de Emergencia -->
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-ambulance"></i>
                    <h3>Contacto de Emergencia</h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            <span>Nombre del contacto</span>
                        </div>
                        <div class="info-value">{{ $person->emergency_contact_name ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-phone-alt"></i>
                            <span>Teléfono de emergencia</span>
                        </div>
                        <div class="info-value">{{ $person->emergency_phone ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-heart"></i>
                            <span>Parentesco</span>
                        </div>
                        <div class="info-value">{{ $person->emergency_relationship ?? 'No registrado' }}</div>
                    </div>
                </div>
            </div>

            <!-- Biografía -->
            @if($person->bio)
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-quote-left"></i>
                    <h3>Biografía / Notas</h3>
                </div>
                <div class="bio-content">
                    <p>{{ $person->bio }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha -->
        <div class="detail-right">
            <!-- Tarjeta específica para ONG de Rescate -->
            @if(($person->institution_type ?? $person->category) == 'ngo_rescue')
            <div class="info-card-glass rescue-card">
                <div class="card-header-glass">
                    <i class="fas fa-id-card"></i>
                    <h3>🎖️ Carnet de Rescate <span class="info-badge rescue">ONG</span></h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-hashtag"></i>
                            <span>Número de Miembro</span>
                        </div>
                        <div class="info-value"><strong>{{ $person->rescue_member_number ?? 'No asignado' }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            <span>Categoría</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_member_category ?? 'No especificada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Vence</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_expiry_date ? \Carbon\Carbon::parse($person->rescue_expiry_date)->format('m/Y') : 'No registrada' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-md"></i>
                            <span>Especialidad</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_specialty_area ?? 'No especificada' }}</div>
                    </div>
                    @if($person->rescue_certifications)
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-certificate"></i>
                            <span>Certificaciones</span>
                        </div>
                        <div class="info-value">{{ $person->rescue_certifications }}</div>
                    </div>
                    @endif
                </div>
                
                <!-- Mini preview del carnet -->
                @if($person->rescue_member_number)
                <div class="carnet-mini-preview">
                    <p><strong>ORGANIZACIÓN DE RESCATE</strong></p>
                    <p>MIEMBRO N°: {{ $person->rescue_member_number }}</p>
                    <p>CATEGORÍA: {{ $person->rescue_member_category ?? 'N/A' }}</p>
                    <p>VENCE: {{ $person->rescue_expiry_date ? \Carbon\Carbon::parse($person->rescue_expiry_date)->format('m/Y') : 'N/A' }}</p>
                </div>
                @endif
            </div>
            @endif

            <!-- Tarjeta específica para Organizaciones Gubernamentales -->
            @if(($person->institution_type ?? $person->category) == 'government')
            <div class="info-card-glass government-card">
                <div class="card-header-glass">
                    <i class="fas fa-landmark"></i>
                    <h3>🏛️ Información Gubernamental <span class="info-badge government">Gobierno</span></h3>
                </div>
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-layer-group"></i>
                            <span>Nivel del Gobierno</span>
                        </div>
                        <div class="info-value">
                            @if($person->government_level == 'national') 🏛️ Nacional
                            @elseif($person->government_level == 'regional') 🏢 Regional
                            @elseif($person->government_level == 'municipal') 🏘️ Municipal
                            @elseif($person->government_level == 'parish') 📌 Parroquial
                            @else No especificado @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-balance-scale"></i>
                            <span>Rama del Poder</span>
                        </div>
                        <div class="info-value">
                            @if($person->government_branch == 'executive') ⚡ Poder Ejecutivo
                            @elseif($person->government_branch == 'legislative') 📜 Poder Legislativo
                            @elseif($person->government_branch == 'judicial') ⚖️ Poder Judicial
                            @elseif($person->government_branch == 'citizen') 👁️ Poder Ciudadano
                            @elseif($person->government_branch == 'electoral') 🗳️ Poder Electoral
                            @else No especificado @endif
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i>
                            <span>Ministerio / Ente</span>
                        </div>
                        <div class="info-value">{{ $person->government_entity_label ?? $person->government_entity ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user-tie"></i>
                            <span>Cargo / Jerarquía</span>
                        </div>
                        <div class="info-value">{{ $person->government_position_label ?? $person->government_position ?? 'No especificado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-id-card"></i>
                            <span>N° de Carnet</span>
                        </div>
                        <div class="info-value">{{ $person->government_card_number ?? 'No registrado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-check"></i>
                            <span>Fecha de Ingreso</span>
                        </div>
                        <div class="info-value">{{ $person->government_joining_date ? \Carbon\Carbon::parse($person->government_joining_date)->format('d/m/Y') : 'No registrada' }}</div>
                    </div>
                </div>
            </div>
            @endif

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
                @if($person->average_grade)
                <div class="stat-mini">
                    <div class="stat-mini-icon purple">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-mini-info">
                        <span class="stat-mini-label">Promedio</span>
                        <span class="stat-mini-value">{{ number_format($person->average_grade, 2) }}</span>
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
                        <p id="assignPersonName">{{ $person->full_name }}</p>
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
    let currentPersonId = {{ $person->id }};
    
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
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }
    
    function assignNFCCard() {
        const cardId = document.getElementById('nfcCardSelect').value;
        if (!cardId) {
            alert('Seleccione una tarjeta NFC');
            return;
        }
        
        fetch(`/admin/persons/${currentPersonId}/assign-nfc`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ card_id: cardId })
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