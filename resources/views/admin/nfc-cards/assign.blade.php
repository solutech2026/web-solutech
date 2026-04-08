@extends('layouts.admin')

@section('title', 'Asignar Tarjeta NFC')

@section('header', 'Asignar Tarjeta NFC')

@section('content')
<div class="nfc-assign-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="assign-card">
                <div class="text-center mb-4">
                    <div class="nfc-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>Asignar Tarjeta a Persona</h3>
                    <p class="text-muted">Selecciona la persona a la que deseas asignar esta tarjeta NFC</p>
                </div>

                <div class="card-info mb-4">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-microchip"></i> Tarjeta:</span>
                        <span class="info-value">{{ $card->card_code }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-sticky-note"></i> Notas:</span>
                        <span class="info-value">{{ $card->notes ?? 'Sin notas' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar"></i> Registrada:</span>
                        <span class="info-value">{{ $card->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.nfc-cards.assign.store', $card->id) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label">Seleccionar Persona *</label>
                        <select name="person_id" class="form-select" required>
                            <option value="">-- Seleccionar persona --</option>
                            @foreach($persons as $person)
                                <option value="{{ $person->id }}" {{ old('person_id') == $person->id ? 'selected' : '' }}>
                                    {{ $person->name }} 
                                    @if($person->type == 'employee') 
                                        (Empleado) 
                                    @else 
                                        (Visitante)
                                    @endif
                                    @if($person->company) 
                                        - {{ $person->company->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Solo se muestran personas activas
                        </small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Nota:</strong> Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso.
                        Si la persona ya tenía otra tarjeta asignada, será reemplazada.
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-link"></i> Asignar Tarjeta
                        </button>
                        <a href="{{ route('admin.nfc-cards') }}" class="btn btn-secondary flex-grow-1">
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
    .nfc-assign-container {
        padding: 20px;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
    }
    
    .assign-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #e5e7eb;
    }
    
    .nfc-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }
    
    .nfc-icon i {
        font-size: 40px;
        color: white;
    }
    
    .card-info {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #4b5563;
    }
    
    .info-value {
        color: #1f2937;
        font-family: monospace;
    }
    
    .form-select, .form-control {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 12px;
    }
    
    .form-select:focus, .form-control:focus {
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
    
    .alert {
        border-radius: 12px;
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #92400e;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enfocar el select
        document.querySelector('select[name="person_id"]').focus();
    });
</script>
@endpush