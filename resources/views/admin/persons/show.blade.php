@extends('layouts.admin')

@section('title', 'Detalle de Persona')

@section('header', 'Perfil de ' . $person->full_name)

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
                    {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                @endif
            </div>
            <div class="hero-info">
                <h1>{{ $person->full_name }}</h1>
                <div class="hero-badges">
                    <span class="badge-category {{ $person->category }}">
                        <i class="fas {{ $person->category == 'employee' ? 'fa-briefcase' : 'fa-school' }}"></i>
                        {{ $person->category_label }}
                    </span>
                    @if($person->subcategory)
                    <span class="badge-subcategory {{ $person->subcategory }}">
                        <i class="fas {{ $person->subcategory == 'student' ? 'fa-graduation-cap' : ($person->subcategory == 'teacher' ? 'fa-chalkboard-user' : 'fa-building') }}"></i>
                        {{ $person->subcategory_label }}
                    </span>
                    @endif
                    @if($person->average_grade)
                    <span class="badge-average">
                        <i class="fas fa-chart-line"></i>
                        Promedio: {{ number_format($person->average_grade, 2) }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="hero-actions">
                <button class="action-btn-secondary" onclick="openChangePhotoModal()">
                    <i class="fas fa-camera"></i> Cambiar foto
                </button>
                <a href="{{ route('admin.persons.edit', $person->id) }}" class="action-btn-primary">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="action-btn-danger" onclick="deletePerson({{ $person->id }})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    </div>

    <!-- Bio Link Card -->
    @if($person->bio_url)
    <div class="bio-link-card">
        <div class="bio-link-content">
            <div class="bio-link-info">
                <i class="fas fa-link"></i>
                <div>
                    <h4>Perfil Público</h4>
                    <p>Comparte este enlace para mostrar la información de {{ $person->full_name }}</p>
                </div>
            </div>
            <div class="bio-link-actions">
                <div id="qrCodeBio" class="qr-code-bio"></div>
                <a href="{{ $person->bio_full_url }}" target="_blank" class="btn-bio">
                    <i class="fas fa-external-link-alt"></i> Ver perfil
                </a>
                <button class="btn-bio" onclick="copyToClipboard('{{ $person->bio_full_url }}')">
                    <i class="fas fa-copy"></i> Copiar enlace
                </button>
                <a href="{{ route('bio.public.vcard', $person->bio_url) }}" class="btn-bio">
                    <i class="fas fa-download"></i> vCard
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabs -->
    <div class="detail-tabs">
        <button class="tab-btn active" data-tab="info">
            <i class="fas fa-user-circle"></i> Información Personal
        </button>
        <button class="tab-btn" data-tab="academic" id="academicTab" style="display: none;">
            <i class="fas fa-graduation-cap"></i> Información Académica
        </button>
        <button class="tab-btn" data-tab="schedule">
            <i class="fas fa-clock"></i> Horarios
        </button>
        <button class="tab-btn" data-tab="nfc">
            <i class="fas fa-id-card"></i> Tarjeta NFC
        </button>
        <button class="tab-btn" data-tab="access">
            <i class="fas fa-history"></i> Historial de Accesos
        </button>
        <button class="tab-btn" data-tab="emergency">
            <i class="fas fa-ambulance"></i> Emergencias
        </button>
    </div>

    <div class="detail-content">
        <!-- TAB 1: Información Personal -->
        <div class="tab-pane active" id="tab-info">
            <div class="detail-grid">
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-user"></i>
                        <h3>Datos Personales</h3>
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
                                <span>{{ $person->category == 'employee' ? 'Empresa' : 'Colegio' }}</span>
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
                                <span>Departamento</span>
                            </div>
                            <div class="info-value">{{ $person->department }}</div>
                        </div>
                        @endif
                        @if($person->teacher_type)
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-chalkboard-user"></i>
                                <span>Tipo de Docente</span>
                            </div>
                            <div class="info-value">{{ $person->teacher_type_label }}</div>
                        </div>
                        @endif
                    </div>
                </div>

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
                    @if($person->user_id)
                    <div class="stat-mini">
                        <div class="stat-mini-icon purple">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-mini-info">
                            <span class="stat-mini-label">Usuario del sistema</span>
                            <span class="stat-mini-value">Creado</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: Información Académica (solo estudiantes) -->
        <div class="tab-pane" id="tab-academic">
            <div class="academic-grid">
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-graduation-cap"></i>
                        <h3>Datos Académicos Actuales</h3>
                    </div>
                    <div class="info-list">
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-book"></i>
                                <span>Grado</span>
                            </div>
                            <div class="info-value">{{ $person->grade_level_label ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-calendar-week"></i>
                                <span>Año Escolar</span>
                            </div>
                            <div class="info-value">{{ $person->academic_year ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-chart-line"></i>
                                <span>Periodo Actual</span>
                            </div>
                            <div class="info-value">{{ $person->period_label ?? 'No especificado' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">
                                <i class="fas fa-star"></i>
                                <span>Promedio General</span>
                            </div>
                            <div class="info-value">
                                @if($person->average_grade)
                                    <span class="average-badge">{{ number_format($person->average_grade, 2) }}</span>
                                @else
                                    No calculado
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-file-pdf"></i>
                        <h3>Boletines de Notas</h3>
                        <button class="btn-upload-report" onclick="openUploadReportModal()">
                            <i class="fas fa-upload"></i> Subir Boletín
                        </button>
                    </div>
                    <div class="report-cards-list" id="reportCardsList">
                        <div class="loading-spinner">Cargando boletines...</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Horarios -->
        <div class="tab-pane" id="tab-schedule">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-clock"></i>
                    <h3>Horario Semanal</h3>
                </div>
                <div class="schedule-table-container">
                    <table class="schedule-table-modern">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Materia / Actividad</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody id="scheduleTableBody">
                            <tr class="loading-row">
                                <td colspan="5" class="text-center">Cargando horarios...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: Tarjeta NFC -->
        <div class="tab-pane" id="tab-nfc">
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
        </div>

        <!-- TAB 5: Historial de Accesos -->
        <div class="tab-pane" id="tab-access">
            <div class="info-card-glass">
                <div class="card-header-glass">
                    <i class="fas fa-history"></i>
                    <h3>Historial de Accesos</h3>
                    <span class="header-badge" id="accessCount">0 registros</span>
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
                        <tbody id="accessLogsBody">
                            <tr class="loading-row">
                                <td colspan="4" class="text-center">Cargando historial...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 6: Emergencias -->
        <div class="tab-pane" id="tab-emergency">
            <div class="emergency-grid">
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
                    </div>
                </div>

                @if($person->subcategory == 'student')
                <div class="info-card-glass">
                    <div class="card-header-glass">
                        <i class="fas fa-notes-medical"></i>
                        <h3>Información Médica</h3>
                    </div>
                    <div class="info-list">
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
                                <span>Condiciones médicas</span>
                            </div>
                            <div class="info-value">{{ $person->medical_conditions ?? 'No registradas' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
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
                <button type="button" class="btn-primary-modern" onclick="assignNFCCard({{ $person->id }})">Asignar Tarjeta</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Subir Boletín -->
<div class="modal fade modern-modal" id="uploadReportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf"></i> Subir Boletín de Notas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadReportForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group-modern">
                        <label>Periodo *</label>
                        <select name="period" id="reportPeriod" class="form-select-modern" required>
                            <option value="">Seleccionar periodo</option>
                            <option value="first">Primer Lapso</option>
                            <option value="second">Segundo Lapso</option>
                            <option value="third">Tercer Lapso</option>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label>Año Escolar *</label>
                        <input type="text" name="academic_year" class="input-modern" placeholder="Ej: 2024-2025" required>
                    </div>
                    <div class="form-group-modern">
                        <label>Grado *</label>
                        <select name="grade_level" class="form-select-modern" required>
                            <option value="">Seleccionar grado</option>
                            <optgroup label="Primaria">
                                <option value="1st">1er Grado</option>
                                <option value="2nd">2do Grado</option>
                                <option value="3rd">3er Grado</option>
                                <option value="4th">4to Grado</option>
                                <option value="5th">5to Grado</option>
                                <option value="6th">6to Grado</option>
                            </optgroup>
                            <optgroup label="Liceo / Secundaria">
                                <option value="7th">1er Año</option>
                                <option value="8th">2do Año</option>
                                <option value="9th">3er Año</option>
                            </optgroup>
                            <optgroup label="Ciclo Diversificado">
                                <option value="10th">4to Año</option>
                                <option value="11th">5to Año</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label>Promedio (opcional)</label>
                        <input type="number" name="average" class="input-modern" step="0.01" min="0" max="100" placeholder="0 - 100">
                    </div>
                    <div class="form-group-modern">
                        <label>Archivo PDF *</label>
                        <input type="file" name="file" class="input-modern" accept=".pdf" required>
                        <small class="form-text">Máximo 5MB. Solo archivos PDF</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-modern">Subir Boletín</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cambiar Foto -->
<div class="modal fade modern-modal" id="changePhotoModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="changePhotoForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="current-photo-modal" style="text-align: center; margin-bottom: 1rem;">
                        <img id="currentPhotoPreview" src="{{ $person->photo_url }}" alt="Foto actual" style="width: 150px; height: 150px; border-radius: 1rem; object-fit: cover;">
                    </div>
                    <div class="form-group-modern">
                        <label>Seleccionar nueva foto</label>
                        <input type="file" name="photo" id="newPhotoInput" class="input-modern" accept="image/*" required>
                        <small class="form-text">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-modern">Actualizar Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons-show.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
@endpush

@push('scripts')
<script>
    let currentPersonId = {{ $person->id }};

    // Generar QR Code para Bio
    @if($person->bio_url)
    new QRCode(document.getElementById("qrCodeBio"), {
        text: "{{ $person->bio_full_url }}",
        width: 60,
        height: 60,
        colorDark: "#1f2937",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
    @endif

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('URL copiada al portapapeles');
    }

    // ============================================
    // TABS
    // ============================================
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            document.getElementById(`tab-${tabId}`).classList.add('active');
        });
    });

    // Mostrar tabs según categoría
    @if($person->subcategory == 'student')
        document.getElementById('academicTab').style.display = 'flex';
    @endif

    // ============================================
    // CARGAR HORARIOS
    // ============================================
    function loadSchedules() {
        fetch(`/admin/persons/{{ $person->id }}/schedules`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('scheduleTableBody');
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay horarios registrados</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.map(schedule => `
                    <tr>
                        <td>${schedule.day_label}</td>
                        <td>${schedule.start_time}</td>
                        <td>${schedule.end_time}</td>
                        <td>${schedule.subject || '-'}</td>
                        <td>${schedule.classroom || '-'}</td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('scheduleTableBody').innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar horarios</td></tr>';
            });
    }

    // ============================================
    // CARGAR HISTORIAL DE ACCESOS
    // ============================================
    function loadAccessLogs() {
        fetch(`/admin/persons/{{ $person->id }}/access-logs`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('accessLogsBody');
                const countSpan = document.getElementById('accessCount');
                countSpan.textContent = `${data.length} registros`;
                
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center">No hay registros de acceso</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.map(log => `
                    <tr>
                        <td><div class="date-cell"><i class="far fa-calendar-alt"></i> ${log.access_time}</div></td>
                        <td>${log.gate}</td>
                        <td><span class="method-badge ${log.verification_method}"><i class="fas ${log.verification_method == 'nfc' ? 'fa-id-card' : 'fa-key'}"></i> ${log.verification_method_label}</span></td>
                        <td><span class="status-badge ${log.status}">${log.status_label}</span></td>
                    </tr>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('accessLogsBody').innerHTML = '<tr><td colspan="4" class="text-center">Error al cargar historial</td></tr>';
            });
    }

    // ============================================
    // CARGAR BOLETINES
    // ============================================
    function loadReportCards() {
        fetch(`/admin/persons/{{ $person->id }}/report-cards`)
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('reportCardsList');
                
                if (!data.success || data.data.length === 0) {
                    container.innerHTML = '<div class="empty-state-small"><i class="fas fa-file-pdf"></i><p>No hay boletines subidos</p></div>';
                    return;
                }
                
                container.innerHTML = data.data.map(report => `
                    <div class="report-card-item">
                        <div class="report-card-info">
                            <span class="report-card-period">${report.period_label} - ${report.grade_level}</span>
                            <span class="report-card-year">${report.academic_year}</span>
                            ${report.average ? `<span class="report-card-average">Promedio: ${report.average}</span>` : ''}
                        </div>
                        <div class="report-card-actions">
                            <a href="${report.file_url}" class="btn-view-pdf" target="_blank"><i class="fas fa-eye"></i> Ver</a>
                            <button class="btn-delete-report" onclick="deleteReportCard(${report.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                `).join('');
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('reportCardsList').innerHTML = '<div class="empty-state-small"><i class="fas fa-exclamation-triangle"></i><p>Error al cargar boletines</p></div>';
            });
    }

    // ============================================
    // ASIGNAR NFC
    // ============================================
    function openAssignNFCModal(personId) {
        new bootstrap.Modal(document.getElementById('assignNFCModal')).show();
    }

    function assignNFCCard(personId) {
        const cardId = document.getElementById('nfcCardSelect').value;
        if (!cardId) {
            alert('Seleccione una tarjeta NFC');
            return;
        }
        
        fetch(`/admin/persons/${personId}/assign-nfc`, {
            method: 'PUT',
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

    // ============================================
    // BOLETINES
    // ============================================
    function openUploadReportModal() {
        new bootstrap.Modal(document.getElementById('uploadReportModal')).show();
    }

    document.getElementById('uploadReportForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/admin/persons/{{ $person->id }}/upload-report-card`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Error: ' + (data.error || 'Error al subir el boletín'));
              }
          })
          .catch(error => {
              alert('Error al subir el boletín');
          });
    });

    function deleteReportCard(reportCardId) {
        if (confirm('¿Eliminar este boletín?')) {
            fetch(`/admin/persons/{{ $person->id }}/report-cards/${reportCardId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => location.reload());
        }
    }

    // ============================================
    // FOTO DE PERFIL
    // ============================================
    function openChangePhotoModal() {
        new bootstrap.Modal(document.getElementById('changePhotoModal')).show();
    }

    document.getElementById('changePhotoForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/admin/persons/{{ $person->id }}/upload-photo`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  location.reload();
              } else {
                  alert('Error: ' + data.message);
              }
          })
          .catch(error => {
              alert('Error al subir la foto');
          });
    });

    // ============================================
    // ELIMINAR PERSONA
    // ============================================
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

    // ============================================
    // INICIALIZAR
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        loadSchedules();
        loadAccessLogs();
        @if($person->subcategory == 'student')
            loadReportCards();
        @endif
    });
</script>
@endpush