@extends('layouts.admin')

@section('title', 'Registrar Tarjeta NFC')

@section('header', 'Registrar Nueva Tarjeta')

@section('content')
<div class="nfc-create-container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="create-card">
                <div class="text-center mb-4">
                    <div class="nfc-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h3>Registrar Tarjeta NFC</h3>
                    <p class="text-muted">Ingresa el código de la tarjeta manualmente o acércala al lector</p>
                </div>

                <form method="POST" action="{{ route('admin.nfc-cards.store') }}" id="nfcForm">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label">Código de la Tarjeta</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">
                                <i class="fas fa-microchip"></i>
                            </span>
                            <input type="text" 
                                   name="card_code" 
                                   id="cardCode" 
                                   class="form-control" 
                                   placeholder="Ingrese código manual o acerque la tarjeta"
                                   required
                                   autofocus>
                            <button class="btn btn-secondary" type="button" onclick="readNFC()">
                                <i class="fas fa-rss"></i> Leer NFC
                            </button>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Ingrese el código manualmente o presione "Leer NFC" y acerque la tarjeta al lector
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Notas (opcional)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Información adicional sobre la tarjeta..."></textarea>
                    </div>

                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="fas fa-save"></i> Registrar Tarjeta
                        </button>
                        <a href="{{ route('admin.nfc-cards') }}" class="btn btn-secondary flex-grow-1">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                    </div>
                </form>

                <div class="mt-4 text-center">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        La tarjeta quedará registrada sin asociar a ninguna persona.
                        Podrás asignarla más tarde desde el listado de tarjetas.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .nfc-create-container {
        padding: 20px;
        min-height: calc(100vh - 200px);
        display: flex;
        align-items: center;
    }
    
    .create-card {
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
    
    .form-control, .input-group-text {
        border-radius: 12px;
    }
    
    .form-control:focus {
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
        border-radius: 16px;
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #0369a1;
    }
</style>
@endpush

@push('scripts')
<script>
    function readNFC() {
        // Simular lectura de NFC
        const readingAlert = document.createElement('div');
        readingAlert.className = 'alert alert-info mt-3';
        readingAlert.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Leyendo tarjeta... Acerca la tarjeta al lector';
        document.getElementById('nfcForm').appendChild(readingAlert);
        
        setTimeout(() => {
            // Simular código leído
            const fakeCode = 'NFC-' + Math.random().toString(36).substring(2, 10).toUpperCase();
            document.getElementById('cardCode').value = fakeCode;
            readingAlert.remove();
            
            // Mostrar confirmación
            const successAlert = document.createElement('div');
            successAlert.className = 'alert alert-success mt-3';
            successAlert.innerHTML = '<i class="fas fa-check-circle"></i> Tarjeta leída correctamente: ' + fakeCode;
            document.getElementById('nfcForm').appendChild(successAlert);
            
            setTimeout(() => {
                successAlert.remove();
            }, 3000);
        }, 2000);
    }
    
    // Enfocar el input al cargar
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cardCode').focus();
    });
</script>
@endpush