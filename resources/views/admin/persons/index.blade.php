@extends('layouts.admin')

@section('title', 'Gestión de Personas')

@section('header', 'Personas')

@section('content')
<div class="persons-container-modern">
    <!-- Hero Section -->
    <div class="page-hero">
        <div class="hero-content">
            <div class="hero-left">
                <div class="hero-icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <div class="hero-text">
                    <h1>Gestión de Personas</h1>
                    <p>Administra empleados, estudiantes, personal docente, administrativo, ONG de rescate y gobierno</p>
                </div>
            </div>
            <div class="hero-right">
                <a href="{{ route('admin.persons.create') }}" class="btn-primary-modern">
                    <i class="fas fa-user-plus"></i>
                    <span>Nueva Persona</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-modern">
        <div class="stat-card-glass">
            <div class="stat-icon-circle blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalPersons">{{ $persons->total() }}</h3>
                <p>Total Personas</p>
            </div>
            <div class="stat-trend">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle green">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalEmployees">{{ $persons->where('institution_type', 'company')->whereNull('subcategory')->count() }}</h3>
                <p>Empleados</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle purple">
                <i class="fas fa-school"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalSchool">{{ $persons->where('institution_type', 'school')->count() }}</h3>
                <p>Personal Escolar</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle cyan">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalStudents">{{ $persons->where('subcategory', 'student')->count() }}</h3>
                <p>Estudiantes</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle orange">
                <i class="fas fa-chalkboard-user"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalTeachers">{{ $persons->where('subcategory', 'teacher')->count() }}</h3>
                <p>Docentes</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle red">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalRescue">{{ $persons->where('institution_type', 'ngo_rescue')->count() }}</h3>
                <p>ONG de Rescate</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle gold">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalGovernment">{{ $persons->where('institution_type', 'government')->count() }}</h3>
                <p>Personal Gobierno</p>
            </div>
        </div>

        <div class="stat-card-glass">
            <div class="stat-icon-circle pink">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="stat-info-glass">
                <h3 id="totalWithNfc">{{ $persons->whereNotNull('nfc_card_id')->count() }}</h3>
                <p>Con Tarjeta NFC</p>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="filters-bar-modern">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar por nombre, apellido, cédula o email...">
        </div>
        
        <div class="filters-group">
            <select id="institutionTypeFilter" class="filter-select-modern">
                <option value="all">Todos los tipos</option>
                <option value="company">🏢 Empresas</option>
                <option value="school">🏫 Colegios</option>
                <option value="ngo_rescue">🚒 ONG de Rescate</option>
                <option value="government">🏛️ Organizaciones Gubernamentales</option>
            </select>

            <select id="subcategoryFilter" class="filter-select-modern" disabled>
                <option value="all">Todas las subcategorías</option>
                <option value="student">🎓 Estudiantes</option>
                <option value="teacher">👨‍🏫 Docentes</option>
                <option value="administrative">📋 Administrativo</option>
            </select>

            <select id="companyFilter" class="filter-select-modern">
                <option value="all">Todas las instituciones</option>
                @foreach($companies as $company)
                    <option value="{{ $company->id }}" data-type="{{ $company->type }}">
                        @if($company->type == 'company') 🏢
                        @elseif($company->type == 'school') 🏫
                        @elseif($company->type == 'ngo_rescue') 🚒
                        @else 🏛️ @endif
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>

            <button class="btn-export-modern" onclick="exportPersons()">
                <i class="fas fa-download"></i>
                Exportar
            </button>
        </div>
    </div>

    <!-- Persons Grid -->
    <div class="persons-grid-modern" id="personsGrid">
        @forelse($persons as $person)
        <div class="person-card-modern" 
             data-id="{{ $person->id }}"
             data-institution-type="{{ $person->institution_type }}"
             data-subcategory="{{ $person->subcategory }}"
             data-company="{{ $person->company_id }}">
            
            <div class="card-badge {{ $person->institution_type }}">
                @if($person->institution_type == 'company')
                    <i class="fas fa-briefcase"></i> Empleado
                @elseif($person->institution_type == 'school')
                    <i class="fas fa-school"></i> Personal Escolar
                @elseif($person->institution_type == 'ngo_rescue')
                    <i class="fas fa-heartbeat"></i> ONG de Rescate
                @else
                    <i class="fas fa-landmark"></i> Organización Gubernamental
                @endif
            </div>
            
            <div class="card-header-modern">
                <div class="avatar-modern" style="background: {{ $person->avatar_color ?? 'linear-gradient(135deg, #6366f1, #a855f7)' }}">
                    @if($person->photo)
                        <img src="{{ $person->photo_url }}" alt="{{ $person->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1rem;">
                    @else
                        {{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}
                    @endif
                </div>
                <div class="header-info">
                    <h3>{{ $person->full_name }}</h3>
                    @if($person->subcategory)
                        <span class="subcategory-badge {{ $person->subcategory }}">
                            @if($person->subcategory == 'student')
                                <i class="fas fa-graduation-cap"></i> Estudiante
                            @elseif($person->subcategory == 'teacher')
                                <i class="fas fa-chalkboard-user"></i> Docente
                            @else
                                <i class="fas fa-building"></i> Administrativo
                            @endif
                        </span>
                    @endif
                    @if($person->rescue_member_number)
                        <span class="subcategory-badge rescue">
                            <i class="fas fa-id-card"></i> Miembro #{{ $person->rescue_member_number }}
                        </span>
                    @endif
                    @if($person->government_position)
                        <span class="subcategory-badge government">
                            <i class="fas fa-user-tie"></i> {{ $person->government_position_label ?? $person->government_position }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="card-body-modern">
                <div class="detail-row">
                    <i class="fas fa-building"></i>
                    <span>{{ $person->company->name ?? 'N/A' }}</span>
                </div>
                
                @if($person->document_id)
                <div class="detail-row">
                    <i class="fas fa-id-card"></i>
                    <span>{{ $person->document_id }}</span>
                </div>
                @endif
                
                @if($person->email)
                <div class="detail-row">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $person->email }}</span>
                </div>
                @endif
                
                @if($person->phone)
                <div class="detail-row">
                    <i class="fas fa-phone"></i>
                    <span>{{ $person->phone }}</span>
                </div>
                @endif
                
                @if($person->position && ($person->institution_type == 'company' || $person->subcategory == 'teacher' || $person->subcategory == 'administrative'))
                <div class="detail-row">
                    <i class="fas fa-user-tie"></i>
                    <span>{{ $person->position }}</span>
                </div>
                @endif

                @if($person->grade_level && $person->subcategory == 'student')
                <div class="detail-row">
                    <i class="fas fa-book"></i>
                    <span>Grado: {{ $person->grade_level_label ?? $person->grade_level }}</span>
                </div>
                @endif

                @if($person->average_grade && $person->subcategory == 'student')
                <div class="detail-row">
                    <i class="fas fa-chart-line"></i>
                    <span>Promedio: {{ number_format($person->average_grade, 2) }}</span>
                </div>
                @endif

                @if($person->rescue_member_category && $person->institution_type == 'ngo_rescue')
                <div class="detail-row rescue">
                    <i class="fas fa-tag"></i>
                    <span>Categoría: {{ $person->rescue_member_category }}</span>
                </div>
                @endif

                @if($person->government_level && $person->institution_type == 'government')
                <div class="detail-row government">
                    <i class="fas fa-layer-group"></i>
                    <span>
                        Nivel: 
                        @if($person->government_level == 'national') Nacional
                        @elseif($person->government_level == 'regional') Regional
                        @elseif($person->government_level == 'municipal') Municipal
                        @else Parroquial @endif
                    </span>
                </div>
                @endif
            </div>

            <div class="card-footer-modern">
                @if($person->nfc_card_id)
                    <div class="nfc-status active">
                        <i class="fas fa-id-card"></i>
                        <span>NFC Asignada</span>
                    </div>
                @else
                    <div class="nfc-status inactive">
                        <i class="fas fa-id-card"></i>
                        <span>Sin NFC</span>
                    </div>
                @endif
                
                <div class="action-buttons">
                    <button class="action-btn view" onclick="viewPerson({{ $person->id }})" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn edit" onclick="editPerson({{ $person->id }})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    @if(!$person->nfc_card_id)
                        <button class="action-btn nfc" onclick="openAssignNFCModal({{ $person->id }}, '{{ $person->full_name }}')" title="Asignar NFC">
                            <i class="fas fa-id-card"></i>
                        </button>
                    @endif
                    <button class="action-btn delete" onclick="deletePerson({{ $person->id }})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state-modern">
            <div class="empty-icon">
                <i class="fas fa-address-book"></i>
            </div>
            <h3>No hay personas registradas</h3>
            <p>Comienza registrando empleados, personal escolar, miembros de ONG de rescate o personal gubernamental</p>
            <a href="{{ route('admin.persons.create') }}" class="btn-primary-modern">
                <i class="fas fa-user-plus"></i>
                Registrar primera persona
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $persons->links() }}
    </div>
</div>

<!-- Modal Asignar Tarjeta NFC -->
<div class="modal fade modern-modal" id="assignNFCModal" tabindex="-1">
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
                            <p id="assignPersonName"></p>
                        </div>
                    </div>
                    
                    <div class="form-group-modern">
                        <label>Seleccionar Tarjeta NFC</label>
                        <select name="card_id" id="nfcCardSelect" class="form-select-modern" required>
                            <option value="">-- Seleccionar tarjeta disponible --</option>
                            @foreach($availableCards as $card)
                                <option value="{{ $card->id }}">
                                    {{ $card->card_code }} 
                                    @if($card->notes) - {{ $card->notes }} @endif
                                </option>
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
                    <button type="submit" class="btn-primary-modern">Asignar Tarjeta</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($availableCards->isEmpty())
<div class="alert-warning-modern">
    <i class="fas fa-exclamation-triangle"></i>
    No hay tarjetas NFC disponibles para asignar. 
    <a href="{{ route('admin.nfc-cards.create') }}">Registra una nueva tarjeta</a> primero.
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/persons.css') }}">
<style>
    /* Estilos para notificaciones */
    .notification-success, .notification-error {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .notification-success {
        background: #10b981;
    }
    .notification-error {
        background: #ef4444;
    }
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // ============================================
    // NOTIFICACIONES
    // ============================================
    
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification-${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // Mostrar notificaciones de sesión
    @if(session('success'))
        showNotification('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showNotification('{{ session('error') }}', 'error');
    @endif
    
    // Forzar recarga cuando se vuelve de otra página (bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    });
    
    // ============================================
    // FILTROS
    // ============================================
    
    const institutionTypeFilter = document.getElementById('institutionTypeFilter');
    const subcategoryFilter = document.getElementById('subcategoryFilter');
    const companyFilter = document.getElementById('companyFilter');
    const searchInput = document.getElementById('searchInput');
    
    // Habilitar/deshabilitar subcategoría según tipo de institución
    institutionTypeFilter?.addEventListener('change', function() {
        if (this.value === 'school') {
            subcategoryFilter.disabled = false;
        } else {
            subcategoryFilter.disabled = true;
            subcategoryFilter.value = 'all';
        }
        filterPersons();
    });
    
    function filterPersons() {
        const search = searchInput?.value.toLowerCase() || '';
        const institutionType = institutionTypeFilter?.value || 'all';
        const subcategory = subcategoryFilter?.value || 'all';
        const company = companyFilter?.value || 'all';
        
        document.querySelectorAll('.person-card-modern').forEach(card => {
            let show = true;
            const text = card.innerText.toLowerCase();
            
            if (search && !text.includes(search)) show = false;
            if (institutionType !== 'all' && card.dataset.institutionType !== institutionType) show = false;
            if (subcategory !== 'all' && card.dataset.subcategory !== subcategory) show = false;
            if (company !== 'all' && card.dataset.company !== company) show = false;
            
            card.style.display = show ? 'flex' : 'none';
        });
    }
    
    // ============================================
    // ACCIONES
    // ============================================
    
    function viewPerson(id) {
        window.location.href = `/admin/persons/${id}?t=${Date.now()}`;
    }
    
    function editPerson(id) {
        window.location.href = `/admin/persons/${id}/edit?t=${Date.now()}`;
    }
    
    let currentPersonId = null;
    
    function openAssignNFCModal(personId, personName) {
        currentPersonId = personId;
        document.getElementById('assignPersonName').innerHTML = personName;
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
                    showNotification('Persona eliminada exitosamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification('Error al eliminar la persona', 'error');
                }
            });
        }
    }
    
    function exportPersons() {
        const params = new URLSearchParams({
            institution_type: institutionTypeFilter?.value || 'all',
            subcategory: subcategoryFilter?.value || 'all',
            company: companyFilter?.value || 'all',
            search: searchInput?.value || '',
            t: Date.now()
        });
        window.location.href = `/admin/persons/export?${params.toString()}`;
    }
    
    // ============================================
    // ASIGNAR NFC
    // ============================================
    
    document.getElementById('assignNFCForm')?.addEventListener('submit', function(e) {
        const cardId = document.getElementById('nfcCardSelect').value;
        if (!cardId) {
            e.preventDefault();
            alert('Seleccione una tarjeta NFC');
        }
    });
    
    // ============================================
    // EVENT LISTENERS
    // ============================================
    
    searchInput?.addEventListener('keyup', filterPersons);
    institutionTypeFilter?.addEventListener('change', filterPersons);
    subcategoryFilter?.addEventListener('change', filterPersons);
    companyFilter?.addEventListener('change', filterPersons);
</script>
@endpush