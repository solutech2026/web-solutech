@extends('layouts.admin')

@section('title', 'Control de Acceso')
@section('header', 'Control de Acceso')

@section('content')
<div class="access-control-container">
    <!-- Filtros -->
    <div class="filter-card">
        <div class="filter-header">
            <i class="fas fa-sliders-h"></i>
            <h4>Filtros de Búsqueda</h4>
        </div>
        <div class="filter-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-modern">
                        <i class="fas fa-building"></i> Empresa / Colegio
                    </label>
                    <select class="form-select-modern" id="companyFilter">
                        <option value="all">Todas las ubicaciones</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-modern">
                        <i class="fas fa-tag"></i> Categoría
                    </label>
                    <select class="form-select-modern" id="categoryFilter">
                        <option value="all">Todos</option>
                        <option value="employee">Empleados</option>
                        <option value="school">Personal Escolar</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-modern">
                        <i class="fas fa-search"></i> Buscar
                    </label>
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="form-control-modern" id="searchInput" placeholder="Nombre, apellido, cédula o NFC...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pestañas -->
    <ul class="nav access-tabs" id="accessTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="persons-tab" data-bs-toggle="tab" data-bs-target="#persons" type="button" role="tab">
                <i class="fas fa-address-card"></i>
                <span>Personas Registradas</span>
                <span class="tab-badge">{{ $persons->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs" type="button" role="tab">
                <i class="fas fa-history"></i>
                <span>Historial de Accesos</span>
                <span class="tab-badge">{{ $accessLogs->count() }}</span>
            </button>
        </li>
    </ul>

    <!-- Contenido de pestañas -->
    <div class="tab-content">
        <!-- Pestaña: Personas Registradas -->
        <div class="tab-pane fade show active" id="persons" role="tabpanel">
            <div class="persons-grid" id="personsGrid">
                @foreach($persons as $person)
                <div class="person-card" data-id="{{ $person->id }}" data-category="{{ $person->category }}" data-company="{{ $person->company_id }}" data-name="{{ strtolower($person->full_name) }}" data-document="{{ strtolower($person->document_id ?? '') }}">
                    <div class="person-card-header">
                        <div class="person-avatar">
                            <span>{{ strtoupper(substr($person->name, 0, 1)) }}{{ strtoupper(substr($person->lastname ?? $person->name, 0, 1)) }}</span>
                            <div class="avatar-status {{ $person->is_active ? 'active' : 'inactive' }}"></div>
                        </div>
                        <div class="person-header-info">
                            <h4 class="person-name">{{ $person->full_name }}</h4>
                            <span class="person-type {{ $person->category }}">
                                <i class="fas {{ $person->category == 'employee' ? 'fa-briefcase' : 'fa-graduation-cap' }}"></i>
                                {{ $person->category_label }}
                                @if($person->subcategory)
                                    <small>({{ $person->subcategory_label }})</small>
                                @endif
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
                        <div class="detail-item">
                            <i class="fas fa-id-card"></i>
                            @if($person->nfc_card_id)
                                <span class="nfc-badge assigned">
                                    <i class="fas fa-check-circle"></i> NFC Asignada
                                </span>
                            @else
                                <span class="nfc-badge unassigned">
                                    <i class="fas fa-times-circle"></i> Sin tarjeta NFC
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="person-card-footer">
                        <a href="{{ route('admin.persons.show', $person->id) }}" class="btn-icon btn-info" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.persons.edit', $person->id) }}" class="btn-icon btn-warning" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(!$person->nfc_card_id && $availableCards->count() > 0)
                            <button type="button" class="btn-icon btn-primary" onclick="openAssignNFCModal({{ $person->id }}, '{{ addslashes($person->full_name) }}')" title="Asignar NFC">
                                <i class="fas fa-id-card"></i>
                            </button>
                        @endif
                        <button type="button" class="btn-icon btn-danger" onclick="deletePerson({{ $person->id }})" title="Eliminar">
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
            <div class="logs-card">
                <div class="logs-header">
                    <div>
                        <i class="fas fa-history"></i>
                        <h4>Registros de Acceso</h4>
                    </div>
                    <button type="button" class="btn-export" onclick="exportLogs()">
                        <i class="fas fa-download"></i> Exportar
                    </button>
                </div>
                <div class="table-responsive-modern">
                    <table class="logs-table-modern">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th>
                                <th>Persona</th>
                                <th>Documento</th>
                                <th>Ubicación</th>
                                <th>Método</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accessLogs as $log)
                            <tr>
                                <td>
                                    <span class="date-time">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $log->access_time->format('d/m/Y H:i:s') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="person-cell">
                                        <div class="cell-avatar">
                                            {{ substr($log->person->full_name ?? 'N/A', 0, 2) }}
                                        </div>
                                        <span>{{ $log->person->full_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>{{ $log->person->document_id ?? 'N/A' }}</td>
                                <td>{{ $log->company->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="method-badge {{ $log->verification_method ?? 'nfc' }}">
                                        <i class="fas {{ $log->verification_method == 'nfc' ? 'fa-microchip' : 'fa-qrcode' }}"></i>
                                        {{ strtoupper($log->verification_method ?? 'NFC') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $log->status }}">
                                        <i class="fas {{ $log->status == 'granted' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $log->status == 'granted' ? 'Permitido' : 'Denegado' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="empty-logs">
                                    <i class="fas fa-door-open"></i>
                                    <p>No hay registros de acceso</p>
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
<div class="modal fade" id="assignNFCModal" tabindex="-1" aria-labelledby="assignNFCModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content-modern">
            <div class="modal-header-modern">
                <h5 id="assignNFCModalLabel">
                    <i class="fas fa-id-card"></i> Asignar Tarjeta NFC
                </h5>
                <button type="button" class="modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="assignNFCForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body-modern">
                    <div class="info-person">
                        <div class="info-icon">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="info-text">
                            <label>Persona seleccionada</label>
                            <p id="assignPersonName">---</p>
                        </div>
                    </div>
                    
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-microchip"></i> Seleccionar Tarjeta NFC
                        </label>
                        <select name="card_id" id="nfcCardSelect" class="form-select-modern" required>
                            <option value="">-- Seleccionar tarjeta disponible --</option>
                            @foreach($availableCards as $card)
                                <option value="{{ $card->id }}">
                                    {{ $card->card_code }} 
                                    @if($card->notes) - {{ $card->notes }} @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text-modern">
                            <i class="fas fa-info-circle"></i> 
                            Solo se muestran tarjetas NFC disponibles (sin asignar)
                        </small>
                    </div>
                    
                    <div class="alert-info-modern">
                        <i class="fas fa-info-circle"></i>
                        <span>Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.</span>
                    </div>
                </div>
                <div class="modal-footer-modern">
                    <button type="button" class="btn-secondary-modern" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-modern" id="submitAssignBtn">
                        <i class="fas fa-id-card"></i> Asignar Tarjeta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($availableCards->isEmpty() && !$persons->isEmpty())
<div class="alert-warning-modern">
    <i class="fas fa-exclamation-triangle"></i>
    <span>No hay tarjetas NFC disponibles para asignar.</span>
    <a href="{{ route('admin.nfc-cards.create') }}">Registra una nueva tarjeta</a>
</div>
@endif
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/access-control.css') }}">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/access-control.js') }}"></script>
@endpush