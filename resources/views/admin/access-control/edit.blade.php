@extends('layouts.admin')

@section('title', 'Editar Persona')

@section('header', 'Editar Persona')

@section('content')
<div class="edit-person-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="edit-card">
                <div class="text-center mb-4">
                    <div class="edit-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Editar Persona</h3>
                    <p class="text-muted">Actualice los datos de {{ $person->name }}</p>
                </div>

                <form method="POST" action="{{ route('admin.access-control.update', $person->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="type" id="personType" class="form-select" required>
                                <option value="employee" {{ $person->type == 'employee' ? 'selected' : '' }}>Empleado</option>
                                <option value="visitor" {{ $person->type == 'visitor' ? 'selected' : '' }}>Visitante / Excursionista</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Empresa / Ubicación *</label>
                            <select name="company_id" class="form-select" required>
                                <option value="">Seleccionar</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" {{ $person->company_id == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre completo *</label>
                            <input type="text" name="name" class="form-control" value="{{ $person->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de cédula</label>
                            <input type="text" name="document_id" class="form-control" value="{{ $person->document_id }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ $person->email }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control" value="{{ $person->phone }}">
                        </div>
                        
                        <div class="employee-fields" {{ $person->type == 'visitor' ? 'style=display:none' : '' }}>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo / Posición</label>
                                <input type="text" name="position" class="form-control" value="{{ $person->position }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento</label>
                                <select name="department" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Administración" {{ $person->department == 'Administración' ? 'selected' : '' }}>Administración</option>
                                    <option value="Tecnología" {{ $person->department == 'Tecnología' ? 'selected' : '' }}>Tecnología</option>
                                    <option value="Ventas" {{ $person->department == 'Ventas' ? 'selected' : '' }}>Ventas</option>
                                    <option value="Marketing" {{ $person->department == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="Recursos Humanos" {{ $person->department == 'Recursos Humanos' ? 'selected' : '' }}>Recursos Humanos</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Biografía</label>
                                <textarea name="bio" class="form-control" rows="3">{{ $person->bio }}</textarea>
                            </div>
                        </div>
                        
                        <div class="visitor-fields" {{ $person->type == 'employee' ? 'style=display:none' : '' }}>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de acompañantes</label>
                                <input type="number" name="companions" class="form-control" value="{{ $person->companions ?? 0 }}" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Motivo de visita</label>
                                <select name="visit_reason" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="recreación" {{ $person->visit_reason == 'recreación' ? 'selected' : '' }}>Recreación</option>
                                    <option value="deporte" {{ $person->visit_reason == 'deporte' ? 'selected' : '' }}>Deporte</option>
                                    <option value="evento" {{ $person->visit_reason == 'evento' ? 'selected' : '' }}>Evento especial</option>
                                    <option value="turismo" {{ $person->visit_reason == 'turismo' ? 'selected' : '' }}>Turismo</option>
                                    <option value="otro" {{ $person->visit_reason == 'otro' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Información de Tarjeta NFC -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Tarjeta NFC</label>
                            @if($person->nfc_card_id)
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-id-card"></i> 
                                    Tarjeta asignada: <strong>{{ $person->nfc_card_id }}</strong>
                                    <button type="button" class="btn btn-sm btn-danger float-end" onclick="unassignCard({{ $person->id }})">
                                        <i class="fas fa-unlink"></i> Desvincular
                                    </button>
                                </div>
                            @else
                                @if($availableCards->count() > 0)
                                    <select name="card_id" class="form-select">
                                        <option value="">-- Asignar tarjeta (opcional) --</option>
                                        @foreach($availableCards as $card)
                                            <option value="{{ $card->id }}">
                                                {{ $card->card_code }} 
                                                @if($card->notes) - {{ $card->notes }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Seleccione una tarjeta NFC para asignar a esta persona</small>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No hay tarjetas NFC disponibles. 
                                        <a href="{{ route('admin.nfc-cards.create') }}">Registre una tarjeta</a> primero.
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-save"></i> Actualizar Persona
                        </button>
                        <a href="{{ route('admin.access-control.index') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<form id="unassignForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>
@endsection

@push('styles')
<style>
    .edit-person-container {
        padding: 20px;
        min-height: calc(100vh - 200px);
    }
    
    .edit-card {
        background: white;
        border-radius: 24px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    
    .edit-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .edit-icon i {
        font-size: 32px;
        color: white;
    }
    
    .form-control, .form-select {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 10px 15px;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.4);
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('personType')?.addEventListener('change', function() {
        const employeeFields = document.querySelector('.employee-fields');
        const visitorFields = document.querySelector('.visitor-fields');
        
        if (this.value === 'employee') {
            employeeFields.style.display = 'block';
            visitorFields.style.display = 'none';
        } else {
            employeeFields.style.display = 'none';
            visitorFields.style.display = 'block';
        }
    });
    
    function unassignCard(personId) {
        if (confirm('¿Desvincular la tarjeta NFC de esta persona?')) {
            const form = document.getElementById('unassignForm');
            form.action = `/admin/access-control/${personId}/unassign-nfc`;
            form.submit();
        }
    }
</script>
@endpush