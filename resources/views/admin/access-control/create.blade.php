@extends('layouts.admin')

@section('title', 'Registrar Persona')

@section('header', 'Registrar Nueva Persona')

@section('content')
<div class="create-person-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="create-card">
                <div class="text-center mb-4">
                    <div class="create-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h3>Registrar Nueva Persona</h3>
                    <p class="text-muted">Complete los datos de la persona y asigne una tarjeta NFC si está disponible</p>
                </div>

                <form method="POST" action="{{ route('admin.access-control.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="type" id="personType" class="form-select" required>
                                <option value="employee">Empleado</option>
                                <option value="visitor">Visitante / Excursionista</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Empresa / Ubicación *</label>
                            <select name="company_id" class="form-select" required>
                                <option value="">Seleccionar</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nombre completo *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Número de cédula</label>
                            <input type="text" name="document_id" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Correo electrónico</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        
                        <!-- Campos para Empleados -->
                        <div class="employee-fields">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cargo / Posición</label>
                                <input type="text" name="position" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departamento</label>
                                <select name="department" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="Administración">Administración</option>
                                    <option value="Tecnología">Tecnología</option>
                                    <option value="Ventas">Ventas</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Recursos Humanos">Recursos Humanos</option>
                                    <option value="Operaciones">Operaciones</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Biografía</label>
                                <textarea name="bio" class="form-control" rows="3" placeholder="Experiencia profesional, educación, logros..."></textarea>
                            </div>
                        </div>
                        
                        <!-- Campos para Visitantes -->
                        <div class="visitor-fields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Número de acompañantes</label>
                                <input type="number" name="companions" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Motivo de visita</label>
                                <select name="visit_reason" class="form-select">
                                    <option value="">Seleccionar</option>
                                    <option value="recreación">Recreación</option>
                                    <option value="deporte">Deporte</option>
                                    <option value="evento">Evento especial</option>
                                    <option value="turismo">Turismo</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Asignación de Tarjeta NFC -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Asignar Tarjeta NFC</label>
                            @if($availableCards->count() > 0)
                                <select name="card_id" class="form-select">
                                    <option value="">-- Sin tarjeta (asignar después) --</option>
                                    @foreach($availableCards as $card)
                                        <option value="{{ $card->id }}">
                                            {{ $card->card_code }} 
                                            @if($card->notes) - {{ $card->notes }} @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Seleccione una tarjeta NFC disponible para asignar a esta persona
                                </small>
                            @else
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No hay tarjetas NFC disponibles. 
                                    <a href="{{ route('admin.nfc-cards.create') }}">Registre una tarjeta</a> primero.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-save"></i> Registrar Persona
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
@endsection

@push('styles')
<style>
    .create-person-container {
        padding: 20px;
        min-height: calc(100vh - 200px);
    }
    
    .create-card {
        background: white;
        border-radius: 24px;
        padding: 35px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    
    .create-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .create-icon i {
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
    
    .btn-secondary {
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
    }
    
    .alert-warning a {
        color: #667eea;
        text-decoration: none;
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
</script>
@endpush