@extends('layouts.admin')

@section('title', 'Gestionar Instituciones')
@section('header', 'Empresas, Colegios, ONG y Gobierno')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/companies-index.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="companies-index-modern">
    <!-- Hero Section -->
    <div class="index-hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Instituciones</h1>
                <p>Gestiona empresas, colegios, ONG de rescate y entes gubernamentales</p>
            </div>
            <a href="{{ route('admin.companies.create') }}" class="btn-create">
                <i class="fas fa-plus"></i>
                <span>Nueva Institución</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Instituciones</span>
                <span class="stat-value">{{ $companies->total() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Empresas</span>
                <span class="stat-value">{{ $companies->where('type', 'company')->count() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">
                <i class="fas fa-school"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Colegios</span>
                <span class="stat-value">{{ $companies->where('type', 'school')->count() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">ONG de Rescate</span>
                <span class="stat-value">{{ $companies->where('type', 'ngo_rescue')->count() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon orange">
                <i class="fas fa-landmark"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Gobierno</span>
                <span class="stat-value">{{ $companies->where('type', 'government')->count() }}</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Activas</span>
                <span class="stat-value">{{ $companies->where('is_active', true)->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter"></i>
            <h3>Filtros</h3>
        </div>
        <div class="filters-body">
            <div class="filter-group">
                <label>Tipo</label>
                <select id="filterType" class="filter-select">
                    <option value="all">Todos</option>
                    <option value="company">🏢 Empresas</option>
                    <option value="school">🏫 Colegios</option>
                    <option value="ngo_rescue">🚒 ONG de Rescate</option>
                    <option value="government">🏛️ Gobierno</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Estado</label>
                <select id="filterStatus" class="filter-select">
                    <option value="all">Todos</option>
                    <option value="active">🟢 Activos</option>
                    <option value="inactive">🔴 Inactivos</option>
                </select>
            </div>
            <div class="filter-group search-group">
                <label>Buscar</label>
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="filter-input" placeholder="Nombre, email o teléfono...">
                </div>
            </div>
            <button id="resetFilters" class="btn-reset">
                <i class="fas fa-redo"></i> Reiniciar
            </button>
        </div>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert-success-modern">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert-error-modern">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="this.parentElement.remove()">×</button>
        </div>
    @endif

    <!-- Companies Grid -->
    <div class="companies-grid" id="companiesGrid">
        @forelse($companies as $company)
        <div class="company-card" data-type="{{ $company->type }}" data-status="{{ $company->is_active ? 'active' : 'inactive' }}" data-name="{{ strtolower($company->name) }}" data-email="{{ strtolower($company->email ?? '') }}" data-phone="{{ $company->phone ?? '' }}">
            <div class="card-header {{ $company->type }}">
                <div class="company-logo">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}">
                    @else
                        <div class="logo-placeholder {{ $company->type }}">
                            @if($company->type == 'company')
                                <i class="fas fa-briefcase"></i>
                            @elseif($company->type == 'school')
                                <i class="fas fa-school"></i>
                            @elseif($company->type == 'ngo_rescue')
                                <i class="fas fa-heartbeat"></i>
                            @else
                                <i class="fas fa-landmark"></i>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="company-status">
                    @if($company->is_active)
                        <span class="status-badge active">
                            <i class="fas fa-circle"></i> Activo
                        </span>
                    @else
                        <span class="status-badge inactive">
                            <i class="fas fa-circle"></i> Inactivo
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h3 class="company-name">{{ $company->name }}</h3>
                <div class="company-type">
                    <i class="fas 
                        @if($company->type == 'company') fa-briefcase
                        @elseif($company->type == 'school') fa-school
                        @elseif($company->type == 'ngo_rescue') fa-heartbeat
                        @else fa-landmark @endif
                    "></i>
                    <span>
                        @if($company->type == 'company') Empresa
                        @elseif($company->type == 'school') Colegio
                        @elseif($company->type == 'ngo_rescue') ONG de Rescate
                        @else Organización Gubernamental @endif
                    </span>
                </div>
                @if($company->email)
                <div class="company-contact">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $company->email }}</span>
                </div>
                @endif
                @if($company->phone)
                <div class="company-contact">
                    <i class="fas fa-phone"></i>
                    <span>{{ $company->phone }}</span>
                </div>
                @endif
                @if($company->emergency_line)
                <div class="company-contact emergency">
                    <i class="fas fa-phone-alt"></i>
                    <span>Emergencia: {{ $company->emergency_line }}</span>
                </div>
                @endif
                @if($company->address)
                <div class="company-address">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ Str::limit($company->address, 60) }}</span>
                </div>
                @endif
            </div>
            <div class="card-footer">
                <div class="company-stats">
                    <div class="stat">
                        <i class="fas fa-users"></i>
                        <span>{{ $company->people_count ?? 0 }} personas</span>
                    </div>
                    <div class="stat">
                        <i class="fas fa-calendar-alt"></i>
                        <span>{{ $company->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($company->type == 'ngo_rescue' && $company->coverage_area)
                    <div class="stat">
                        <i class="fas fa-map-marked-alt"></i>
                        <span title="{{ $company->coverage_area }}">{{ Str::limit($company->coverage_area, 20) }}</span>
                    </div>
                    @endif
                    @if($company->type == 'government' && $company->government_level)
                    <div class="stat">
                        <i class="fas fa-layer-group"></i>
                        <span>
                            @if($company->government_level == 'national') Nacional
                            @elseif($company->government_level == 'regional') Regional
                            @elseif($company->government_level == 'municipal') Municipal
                            @else Parroquial @endif
                        </span>
                    </div>
                    @endif
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.companies.edit', $company) }}" class="btn-edit" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn-delete" onclick="confirmDelete({{ $company->id }}, '{{ $company->name }}')" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-building"></i>
            <h3>No hay instituciones registradas</h3>
            <p>Comienza creando una nueva empresa, colegio, ONG de rescate o ente gubernamental</p>
            <a href="{{ route('admin.companies.create') }}" class="btn-create-empty">
                <i class="fas fa-plus"></i> Crear Institución
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $companies->links() }}
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade modern-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-trash-alt"></i> Eliminar Institución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>¿Estás seguro de que deseas eliminar <strong id="deleteCompanyName"></strong>?</p>
                    <small>Esta acción no se puede deshacer y eliminará todos los datos asociados.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger-modern">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filtros dinámicos
    const filterType = document.getElementById('filterType');
    const filterStatus = document.getElementById('filterStatus');
    const searchInput = document.getElementById('searchInput');
    const resetBtn = document.getElementById('resetFilters');
    const companiesGrid = document.getElementById('companiesGrid');
    const cards = document.querySelectorAll('.company-card');

    function filterCompanies() {
        const type = filterType.value;
        const status = filterStatus.value;
        const search = searchInput.value.toLowerCase();
        
        let visibleCount = 0;
        
        cards.forEach(card => {
            const cardType = card.dataset.type;
            const cardStatus = card.dataset.status;
            const cardName = card.dataset.name;
            const cardEmail = card.dataset.email;
            const cardPhone = card.dataset.phone;
            
            let show = true;
            
            if (type !== 'all' && cardType !== type) show = false;
            if (status !== 'all' && cardStatus !== status) show = false;
            if (search && !cardName.includes(search) && !cardEmail.includes(search) && !cardPhone.includes(search)) show = false;
            
            card.style.display = show ? 'block' : 'none';
            if (show) visibleCount++;
        });
        
        // Mostrar mensaje si no hay resultados
        const emptyMessage = document.querySelector('.no-results-message');
        if (visibleCount === 0 && cards.length > 0) {
            if (!emptyMessage) {
                const msg = document.createElement('div');
                msg.className = 'empty-state';
                msg.innerHTML = '<i class="fas fa-search"></i><h3>No se encontraron resultados</h3><p>Intenta con otros filtros de búsqueda</p>';
                companiesGrid.appendChild(msg);
            }
        } else if (emptyMessage) {
            emptyMessage.remove();
        }
    }
    
    filterType.addEventListener('change', filterCompanies);
    filterStatus.addEventListener('change', filterCompanies);
    searchInput.addEventListener('input', filterCompanies);
    
    resetBtn.addEventListener('click', function() {
        filterType.value = 'all';
        filterStatus.value = 'all';
        searchInput.value = '';
        filterCompanies();
    });
    
    // Modal de eliminación
    function confirmDelete(id, name) {
        document.getElementById('deleteCompanyName').textContent = name;
        const form = document.getElementById('deleteForm');
        form.action = `/admin/companies/${id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    // Auto-cerrar alertas después de 5 segundos
    setTimeout(() => {
        document.querySelectorAll('.alert-success-modern, .alert-error-modern').forEach(alert => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        });
    }, 5000);
</script>
@endpush