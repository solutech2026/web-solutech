@extends('layouts.admin')

@section('title', 'Asignar Tarjeta NFC')
@section('header', 'Asignar Tarjeta NFC')

@section('content')
<div class="assign-container">
    <div class="assign-card">
        <div class="assign-header">
            <div class="header-icon">
                <i class="fas fa-id-card"></i>
            </div>
            <div class="header-text">
                <h1>Asignar Tarjeta NFC</h1>
                <p>Selecciona la persona que recibirá esta tarjeta</p>
            </div>
        </div>
        
        <div class="assign-body">
            <div class="card-info">
                <div class="info-label">
                    <i class="fas fa-microchip"></i>
                    <span>Tarjeta a asignar:</span>
                </div>
                <div class="info-value">
                    <strong>{{ $card->card_code }}</strong>
                    @if($card->card_uid)
                        <span class="uid">(UID: {{ $card->card_uid }})</span>
                    @endif
                </div>
            </div>
            
            <!-- IMPORTANTE: Usa la ruta 'assign', NO 'assign.store' -->
            <form action="{{ route('admin.nfc-cards.assign', $card->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="fas fa-user"></i> Seleccionar Persona *
                    </label>
                    <select name="person_id" class="form-control" required>
                        <option value="">-- Seleccione una persona --</option>
                        @foreach($persons as $person)
                            <option value="{{ $person->id }}" {{ old('person_id') == $person->id ? 'selected' : '' }}>
                                {{ $person->full_name }} 
                                @if($person->document_id) - {{ $person->document_id }} @endif
                                @if($person->company) - {{ $person->company->name }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="alert-info">
                    <i class="fas fa-info-circle"></i>
                    <span>Si la persona seleccionada ya tiene una tarjeta asignada, esta será reemplazada automáticamente.</span>
                </div>
                
                <div class="form-actions">
                    <a href="{{ route('admin.nfc-cards.index') }}" class="btn-cancel">Cancelar</a>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-link"></i> Asignar Tarjeta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .assign-container {
        padding: 40px;
        max-width: 600px;
        margin: 0 auto;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
    }
    
    .assign-card {
        background: white;
        border-radius: 28px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        width: 100%;
    }
    
    .assign-header {
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        padding: 32px;
        text-align: center;
        color: white;
    }
    
    .header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    
    .header-icon i {
        font-size: 32px;
        color: white;
    }
    
    .header-text h1 {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }
    
    .header-text p {
        opacity: 0.9;
        margin: 0;
    }
    
    .assign-body {
        padding: 32px;
    }
    
    .card-info {
        background: #f8f9fa;
        border-radius: 16px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .info-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #6c757d;
        font-size: 14px;
    }
    
    .info-value {
        font-size: 18px;
        color: #1a1f36;
    }
    
    .uid {
        font-size: 12px;
        color: #6c757d;
        margin-left: 8px;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #1a1f36;
        margin-bottom: 8px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0d6efd;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
    }
    
    .alert-info {
        background: rgba(13, 110, 253, 0.1);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }
    
    .alert-info i {
        color: #0d6efd;
        font-size: 18px;
    }
    
    .alert-info span {
        font-size: 13px;
        color: #495057;
    }
    
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 16px;
    }
    
    .btn-cancel {
        padding: 12px 24px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        color: #6c757d;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-cancel:hover {
        background: #e9ecef;
        text-decoration: none;
        color: #495057;
    }
    
    .btn-save {
        padding: 12px 32px;
        background: linear-gradient(135deg, #0d6efd, #0b5ed7);
        border: none;
        border-radius: 12px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    @media (max-width: 768px) {
        .assign-container {
            padding: 20px;
        }
        
        .card-info {
            flex-direction: column;
            text-align: center;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn-cancel, .btn-save {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush