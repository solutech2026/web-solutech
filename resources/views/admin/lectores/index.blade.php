@extends('layouts.admin')

@section('title', 'Lectores NFC')
@section('header', 'Lectores NFC')

@section('content')
<div class="lectores-container">
    <!-- Header -->
    <div class="lectores-header">
        <div class="header-title">
            <i class="fas fa-microchip"></i>
            <div>
                <h1>Lectores Configurados</h1>
                <p>Gestiona los lectores NFC del sistema</p>
            </div>
        </div>
        <a href="{{ route('lectores.nuevo') }}" class="btn-primary-modern">
            <i class="fas fa-plus"></i> Nuevo Lector
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($readersList) }}</h3>
                <p>Total Lectores</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3>
                    @php
                        $online = 0;
                        foreach($readersList as $r) {
                            if($r['is_connected']) $online++;
                        }
                        echo $online;
                    @endphp
                </h3>
                <p>En línea</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3>{{ count($readersList) - $online }}</h3>
                <p>Desconectados</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar lector por nombre o ubicación...">
        </div>
        <select id="statusFilter" class="filter-select">
            <option value="all">Todos los estados</option>
            <option value="online">En línea</option>
            <option value="offline">Desconectados</option>
        </select>
        <button class="btn-refresh" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>

    <!-- Readers Grid -->
    <div class="readers-grid" id="readersGrid">
        @forelse($readersList as $reader)
        <div class="reader-card" data-status="{{ $reader['is_connected'] ? 'online' : 'offline' }}" data-name="{{ strtolower($reader['name']) }}">
            <div class="reader-status {{ $reader['is_connected'] ? 'online' : 'offline' }}">
                <i class="fas fa-circle"></i>
                {{ $reader['is_connected'] ? 'Online' : 'Offline' }}
            </div>
            
            <div class="reader-icon">
                @if($reader['type'] == 'network')
                    <i class="fas fa-network-wired"></i>
                @else
                    <i class="fas fa-wifi"></i>
                @endif
            </div>
            
            <div class="reader-info">
                <h3 class="reader-name">{{ $reader['name'] }}</h3>
                <div class="reader-details">
                    <div class="reader-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $reader['ubicacion'] ?? 'Sin ubicación' }}</span>
                    </div>
                    <div class="reader-detail">
                        @if($reader['type'] == 'network')
                            <i class="fas fa-ethernet"></i>
                            <span>{{ $reader['ip_address'] }}:{{ $reader['port'] }}</span>
                        @else
                            <i class="fas fa-wifi"></i>
                            <span>{{ $reader['ssid'] ?? 'N/A' }}</span>
                        @endif
                    </div>
                    <div class="reader-detail">
                        <i class="fas fa-code-branch"></i>
                        <span>Protocolo: {{ strtoupper($reader['protocol']) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="reader-footer">
                <button class="btn-action btn-test" onclick="testReader('{{ $reader['id'] }}')">
                    <i class="fas fa-plug"></i> Probar
                </button>
                <a href="{{ route('lectores.editar', $reader['id']) }}" class="btn-action btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn-action btn-delete" onclick="deleteReader('{{ $reader['id'] }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <h3>No hay lectores configurados</h3>
            <p>Configura tu primer lector NFC para comenzar</p>
            <a href="{{ route('lectores.nuevo') }}" class="btn-primary-modern">
                <i class="fas fa-plus"></i> Configurar Lector
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal -->
<div class="modal fade modal-test" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plug text-primary"></i> Probar Conexión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="testModalBody">
                <div class="spinner-border"></div>
                <p class="mt-3">Probando conexión...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lectores.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// Filtros
function filterReaders() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    
    document.querySelectorAll('.reader-card').forEach(card => {
        let show = true;
        const name = card.dataset.name || '';
        const cardStatus = card.dataset.status;
        
        if (search && !name.includes(search)) show = false;
        if (status !== 'all' && cardStatus !== status) show = false;
        
        card.style.display = show ? '' : 'none';
    });
}

// Probar conexión
async function testReader(id) {
    const modalBody = document.getElementById('testModalBody');
    modalBody.innerHTML = '<div class="spinner-border"></div><p class="mt-3">Probando conexión...</p>';
    const modal = new bootstrap.Modal(document.getElementById('testModal'));
    modal.show();
    
    try {
        const res = await fetch(`/lectores/${id}/test`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        
        if (data.success) {
            modalBody.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 48px; color: #198754;"></i>
                <h4 class="mt-3">Conexión Exitosa</h4>
                <p>${data.message}</p>
                <small class="text-muted">Tiempo de respuesta: ${data.response_time || '< 100ms'}</small>
            `;
        } else {
            modalBody.innerHTML = `
                <i class="fas fa-times-circle" style="font-size: 48px; color: #dc3545;"></i>
                <h4 class="mt-3">Conexión Fallida</h4>
                <p>${data.message}</p>
            `;
        }
    } catch (error) {
        modalBody.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ffc107;"></i>
            <h4 class="mt-3">Error</h4>
            <p>No se pudo establecer comunicación con el servidor.</p>
        `;
    }
}

// Eliminar lector
function deleteReader(id) {
    if (confirm('¿Eliminar este lector? Esta acción no se puede deshacer.')) {
        fetch(`/lectores/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
        }).then(res => {
            if (res.ok) location.reload();
            else alert('Error al eliminar el lector');
        });
    }
}

// Event listeners
document.getElementById('searchInput')?.addEventListener('keyup', filterReaders);
document.getElementById('statusFilter')?.addEventListener('change', filterReaders);
</script>
@endpush