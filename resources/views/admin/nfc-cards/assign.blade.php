@extends('layouts.admin')

@section('title', 'Asignar Tarjeta NFC')

@section('header', 'Asignar Tarjeta NFC')

@section('content')
<div class="nfc-form-container">
    <div class="form-hero">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fas fa-link"></i>
            </div>
            <div class="hero-text">
                <h1>Asignar Tarjeta a Persona</h1>
                <p>Selecciona la persona que usará esta tarjeta NFC</p>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="nfc-icon-large">
            <i class="fas fa-id-card"></i>
        </div>

        <!-- Información de la tarjeta -->
        <div class="card-info-glass">
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-microchip"></i>
                    <span>Código de tarjeta</span>
                </div>
                <div class="info-value">{{ $card->card_code }}</div>
            </div>
            @if($card->card_uid)
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-rss"></i>
                    <span>UID</span>
                </div>
                <div class="info-value">{{ $card->card_uid }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-sticky-note"></i>
                    <span>Notas</span>
                </div>
                <div class="info-value">{{ $card->notes ?? 'Sin notas' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Registrada</span>
                </div>
                <div class="info-value">{{ $card->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.nfc-cards.assign.store', $card->id) }}">
            @csrf
            @method('PUT')

            <div class="form-group-modern">
                <label>Seleccionar Persona *</label>
                <select name="person_id" class="select-modern" required>
                    <option value="">-- Seleccionar persona --</option>
                    @foreach($persons as $person)
                        <option value="{{ $person->id }}" {{ old('person_id') == $person->id ? 'selected' : '' }}>
                            {{ $person->full_name }} 
                            ({{ $person->category_label }})
                            @if($person->company) 
                                - {{ $person->company->name }}
                            @endif
                        </option>
                    @endforeach
                </select>
                <small class="form-text">
                    <i class="fas fa-info-circle"></i> 
                    Solo se muestran personas activas
                </small>
            </div>

            <div class="alert-modern warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Al asignar esta tarjeta, la persona podrá utilizarla para el control de acceso. Si la persona ya tenía otra tarjeta asignada, será reemplazada.</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-modern">
                    <i class="fas fa-link"></i> Asignar Tarjeta
                </button>
                <a href="{{ route('admin.nfc-cards.index') }}" class="btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/nfc-forms.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('select[name="person_id"]').focus();
    });
</script>
@endpush