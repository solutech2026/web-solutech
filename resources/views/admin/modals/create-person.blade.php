<div class="modal fade" id="createPersonModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="personModalTitle">
                    <i class="fas fa-user-plus"></i> Nueva Persona
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="personForm" method="POST" action="{{ route('admin.persons.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo *</label>
                            <select name="type" id="personType" class="form-select" required>
                                <option value="employee">Empleado</option>
                                <option value="visitor">Visitante</option>
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
                                    <option value="negocios">Negocios</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Persona</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
    
    // Resetear formulario al cerrar modal
    document.getElementById('createPersonModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('personForm').reset();
        document.getElementById('personModalTitle').innerHTML = '<i class="fas fa-user-plus"></i> Nueva Persona';
        
        // Resetear método
        const methodInput = document.querySelector('input[name="_method"]');
        if (methodInput) {
            methodInput.remove();
        }
        
        // Resetear action
        document.getElementById('personForm').action = "{{ route('admin.persons.store') }}";
        document.getElementById('personForm').method = 'POST';
        
        // Ocultar campos extras
        document.querySelector('.employee-fields').style.display = 'none';
        document.querySelector('.visitor-fields').style.display = 'none';
    });
</script>