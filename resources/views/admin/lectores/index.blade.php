@extends('layouts.admin')

@section('title', 'Lectores NFC')
@section('header', 'Lectores NFC')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nfc-readers.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="lectores-container-modern">
    <!-- Hero Section -->
    <div class="hero-section-modern">
        <div class="hero-title">
            <h1><i class="fas fa-microchip"></i> Lectores NFC</h1>
            <p>Gestiona los dispositivos lectores del sistema</p>
        </div>
        <a href="{{ route('lectores.nuevo') }}" class="btn-create">
            <i class="fas fa-plus"></i>
            <span>Nuevo Lector</span>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid-modern">
        <div class="stat-card-modern">
            <div class="stat-icon-modern primary">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="stat-info-modern">
                <h3>{{ count($readersList) }}</h3>
                <p>Total Lectores</p>
            </div>
        </div>
        <div class="stat-card-modern">
            <div class="stat-icon-modern success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info-modern">
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
        <div class="stat-card-modern">
            <div class="stat-icon-modern danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info-modern">
                <h3>{{ count($readersList) - $online }}</h3>
                <p>Desconectados</p>
            </div>
        </div>
        <div class="stat-card-modern">
            <div class="stat-icon-modern warning">
                <i class="fas fa-wifi"></i>
            </div>
            <div class="stat-info-modern">
                <h3>
                    @php
                        $wifiCount = 0;
                        foreach($readersList as $r) {
                            if($r['type'] == 'wifi') $wifiCount++;
                        }
                        echo $wifiCount;
                    @endphp
                </h3>
                <p>Lectores WiFi</p>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="filters-bar-modern">
        <div class="search-wrapper-modern">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Buscar lector por nombre o ubicación...">
        </div>
        <select id="statusFilter" class="filter-select-modern">
            <option value="all">Todos los estados</option>
            <option value="online">🟢 En línea</option>
            <option value="offline">🔴 Desconectados</option>
        </select>
        <select id="typeFilter" class="filter-select-modern">
            <option value="all">Todos los tipos</option>
            <option value="network">🔌 Por IP</option>
            <option value="wifi">📡 WiFi</option>
        </select>
        <button class="btn-refresh-modern" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i> Actualizar
        </button>
    </div>

    <!-- Readers Grid -->
    <div class="readers-grid-modern" id="readersGrid">
        @forelse($readersList as $reader)
        <div class="reader-card-modern" data-status="{{ $reader['is_connected'] ? 'online' : 'offline' }}" data-type="{{ $reader['type'] }}" data-name="{{ strtolower($reader['name']) }}" data-location="{{ strtolower($reader['ubicacion'] ?? '') }}">
            <div class="reader-status-modern {{ $reader['is_connected'] ? 'online' : 'offline' }}">
                <i class="fas fa-circle"></i>
                {{ $reader['is_connected'] ? 'En línea' : 'Desconectado' }}
            </div>
            
            <div class="reader-header-modern">
                <div class="reader-icon-modern {{ $reader['type'] }}">
                    @if($reader['type'] == 'network')
                        <i class="fas fa-network-wired"></i>
                    @else
                        <i class="fas fa-wifi"></i>
                    @endif
                </div>
                <h3 class="reader-name-modern">{{ $reader['name'] }}</h3>
                <span class="reader-type-modern">
                    @if($reader['type'] == 'network')
                        <i class="fas fa-ethernet"></i> Conexión por IP
                    @else
                        <i class="fas fa-wifi"></i> Conexión WiFi
                    @endif
                </span>
            </div>
            
            <div class="reader-body-modern">
                @if($reader['ubicacion'])
                <div class="detail-row-modern">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>{{ $reader['ubicacion'] }}</span>
                </div>
                @endif
                
                <div class="detail-row-modern">
                    @if($reader['type'] == 'network')
                        <i class="fas fa-ethernet"></i>
                        <span>{{ $reader['ip_address'] }}:{{ $reader['port'] }}</span>
                    @else
                        <i class="fas fa-wifi"></i>
                        <span>{{ $reader['ssid'] ?? 'Red no configurada' }}</span>
                    @endif
                </div>
                
                <div class="detail-row-modern">
                    <i class="fas fa-code-branch"></i>
                    <span>Protocolo: {{ strtoupper($reader['protocol']) }}</span>
                </div>
                
                <div class="detail-row-modern">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Creado: {{ $reader['created_at'] }}</span>
                </div>
            </div>
            
            <div class="reader-footer-modern">
                <button class="btn-action-modern btn-test" onclick="testReader('{{ $reader['id'] }}')">
                    <i class="fas fa-plug"></i> Probar
                </button>
                <a href="{{ route('lectores.editar', $reader['id']) }}" class="btn-action-modern btn-edit">
                    <i class="fas fa-edit"></i> Editar
                </a>
                <button class="btn-action-modern btn-delete" onclick="deleteReader('{{ $reader['id'] }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="empty-state-modern">
            <div class="empty-icon-modern">
                <i class="fas fa-microchip"></i>
            </div>
            <h3>No hay lectores configurados</h3>
            <p>Configura tu primer lector NFC para comenzar a gestionar accesos</p>
            <a href="{{ route('lectores.nuevo') }}" class="btn-create">
                <i class="fas fa-plus"></i> Configurar Lector
            </a>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Test -->
<div class="modal fade modal-test" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plug"></i> Probar Conexión
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="testModalBody">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-3">Probando conexión con el lector...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action-modern btn-edit" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// Filtros
function filterReaders() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const status = document.getElementById('statusFilter').value;
    const type = document.getElementById('typeFilter').value;
    
    document.querySelectorAll('.reader-card-modern').forEach(card => {
        let show = true;
        const name = card.dataset.name || '';
        const location = card.dataset.location || '';
        const cardStatus = card.dataset.status;
        const cardType = card.dataset.type;
        
        if (search && !name.includes(search) && !location.includes(search)) show = false;
        if (status !== 'all' && cardStatus !== status) show = false;
        if (type !== 'all' && cardType !== type) show = false;
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Probar conexión
async function testReader(id) {
    const modalBody = document.getElementById('testModalBody');
    modalBody.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
        <p class="mt-3">Probando conexión con el lector...</p>
    `;
    const modal = new bootstrap.Modal(document.getElementById('testModal'));
    modal.show();
    
    try {
        const res = await fetch(`/lectores/${id}/test`, {
            method: 'POST',
            headers: { 
                'X-CSRF-TOKEN': csrfToken, 
                'Content-Type': 'application/json' 
            }
        });
        const data = await res.json();
        
        if (data.success) {
            modalBody.innerHTML = `
                <i class="fas fa-check-circle" style="font-size: 60px; color: #10b981;"></i>
                <h4 class="mt-3" style="color: #10b981;">¡Conexión Exitosa!</h4>
                <p>${data.message}</p>
                <div style="background: #f1f5f9; padding: 12px; border-radius: 12px; margin-top: 15px;">
                    <small class="text-muted">Dispositivo: ${data.device?.name || 'N/A'}</small><br>
                    <small class="text-muted">IP: ${data.device?.ip || 'N/A'}</small><br>
                    <small class="text-muted">Puerto: ${data.device?.port || 'N/A'}</small><br>
                    <small class="text-muted">Tiempo de respuesta: ${data.response_time || '< 100ms'}</small>
                </div>
            `;
        } else {
            modalBody.innerHTML = `
                <i class="fas fa-times-circle" style="font-size: 60px; color: #ef4444;"></i>
                <h4 class="mt-3" style="color: #ef4444;">Conexión Fallida</h4>
                <p>${data.message || 'No se pudo establecer conexión con el lector'}</p>
                <div style="background: #fef2f2; padding: 12px; border-radius: 12px; margin-top: 15px;">
                    <small class="text-danger">Verifique la dirección IP y puerto del dispositivo</small>
                </div>
            `;
        }
    } catch (error) {
        modalBody.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 60px; color: #f59e0b;"></i>
            <h4 class="mt-3" style="color: #f59e0b;">Error</h4>
            <p>No se pudo establecer comunicación con el servidor.</p>
            <div style="background: #fef3c7; padding: 12px; border-radius: 12px; margin-top: 15px;">
                <small class="text-warning">Intente nuevamente más tarde</small>
            </div>
        `;
    }
}

// Eliminar lector
function deleteReader(id) {
    if (confirm('¿Estás seguro de eliminar este lector? Esta acción no se puede deshacer.')) {
        fetch(`/lectores/${id}`, {
            method: 'DELETE',
            headers: { 
                'X-CSRF-TOKEN': csrfToken, 
                'Content-Type': 'application/json' 
            }
        }).then(res => {
            if (res.ok) {
                location.reload();
            } else {
                alert('Error al eliminar el lector');
            }
        }).catch(error => {
            alert('Error al conectar con el servidor');
        });
    }
}

// Event listeners
document.getElementById('searchInput')?.addEventListener('keyup', filterReaders);
document.getElementById('statusFilter')?.addEventListener('change', filterReaders);
document.getElementById('typeFilter')?.addEventListener('change', filterReaders);

// Auto-refresh cada 30 segundos (opcional)
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endpush