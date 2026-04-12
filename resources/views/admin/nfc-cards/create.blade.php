@extends('layouts.admin')

@section('title', 'Registrar Tarjeta NFC')

@section('header', 'Registrar Nueva Tarjeta')

@section('content')
<div class="nfc-form-container">
    <div class="form-hero">
        <div class="hero-content">
            <div class="hero-icon">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="hero-text">
                <h1>Registrar Tarjeta NFC</h1>
                <p>Registra una nueva tarjeta para asignar a empleados o estudiantes</p>
            </div>
        </div>
    </div>

    <div class="form-card">
        <div class="nfc-icon-large">
            <i class="fas fa-id-card"></i>
        </div>

        <form method="POST" action="{{ route('admin.nfc-cards.store') }}" id="nfcForm">
            @csrf

            <div class="form-group-modern">
                <label>Código de la tarjeta</label>
                <div class="input-group-modern">
                    <input type="text" 
                           name="card_code" 
                           id="cardCode" 
                           class="input-modern" 
                           placeholder="Ingrese código manual o acerque la tarjeta"
                           value="{{ old('card_code') }}"
                           required>
                    <button type="button" class="btn-reader" onclick="readNFC()" id="readNFCBtn">
                        <i class="fas fa-rss"></i> Leer NFC
                    </button>
                </div>
                <small class="form-text">Ingrese el código manualmente o presione "Leer NFC" y acerque la tarjeta al lector</small>
            </div>

            <div id="readingStatus"></div>

            <div class="form-group-modern">
                <label>Notas (opcional)</label>
                <textarea name="notes" class="input-modern" rows="3" placeholder="Información adicional sobre la tarjeta...">{{ old('notes') }}</textarea>
            </div>

            <div class="alert-modern info">
                <i class="fas fa-info-circle"></i>
                <span>La tarjeta quedará registrada sin asociar a ninguna persona. Podrás asignarla más tarde desde el listado de tarjetas.</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary-modern">
                    <i class="fas fa-save"></i> Registrar Tarjeta
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
    let isReading = false;
    
    async function readNFC() {
        if (isReading) return;
        
        isReading = true;
        const readBtn = document.getElementById('readNFCBtn');
        const statusDiv = document.getElementById('readingStatus');
        
        readBtn.disabled = true;
        readBtn.innerHTML = '<div class="spinner"></div> Leyendo...';
        
        statusDiv.innerHTML = `
            <div class="reading-status info">
                <div class="spinner"></div>
                <span>Leyendo tarjeta... Acerca la tarjeta al lector NFC</span>
            </div>
        `;
        
        try {
            // Simular lectura de NFC (reemplazar con API real)
            const cardData = await simulateNFCRead();
            
            document.getElementById('cardCode').value = cardData.code;
            if (cardData.uid) {
                // Si tienes campo uid, agregarlo
                let uidInput = document.querySelector('input[name="card_uid"]');
                if (!uidInput) {
                    uidInput = document.createElement('input');
                    uidInput.type = 'hidden';
                    uidInput.name = 'card_uid';
                    document.getElementById('nfcForm').appendChild(uidInput);
                }
                uidInput.value = cardData.uid;
            }
            
            statusDiv.innerHTML = `
                <div class="reading-status success">
                    <i class="fas fa-check-circle"></i>
                    <span>Tarjeta leída correctamente: ${cardData.code}</span>
                </div>
            `;
            
            setTimeout(() => {
                statusDiv.innerHTML = '';
            }, 3000);
            
        } catch (error) {
            statusDiv.innerHTML = `
                <div class="reading-status error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Error al leer la tarjeta: ${error.message}</span>
                </div>
            `;
        } finally {
            isReading = false;
            readBtn.disabled = false;
            readBtn.innerHTML = '<i class="fas fa-rss"></i> Leer NFC';
        }
    }
    
    function simulateNFCRead() {
        return new Promise((resolve) => {
            setTimeout(() => {
                const fakeCode = 'NFC-' + Math.random().toString(36).substring(2, 10).toUpperCase();
                const fakeUID = Array.from({length: 8}, () => Math.floor(Math.random() * 256).toString(16).padStart(2, '0').toUpperCase()).join(':');
                resolve({ code: fakeCode, uid: fakeUID });
            }, 2000);
        });
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cardCode').focus();
    });
</script>
@endpush