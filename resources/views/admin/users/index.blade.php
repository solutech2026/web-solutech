@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')

@section('header', 'Gestión de Usuarios')

@section('content')
<div class="users-container">
    <!-- Actions Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="users-action-bar">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-user-plus"></i> Nuevo Usuario
                        </button>
                        <button class="btn btn-outline-success" onclick="exportUsers()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                    </div>
                    <div class="d-flex gap-2">
                        <div class="users-search-box">
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar usuario...">
                            <button class="btn-search" onclick="searchUsers()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <select class="users-filter-select" id="roleFilter">
                            <option value="all">Todos los roles</option>
                            <option value="super-admin">Super Administradores</option>
                            <option value="admin">Administradores</option>
                            <option value="operator">Operadores</option>
                            <option value="user">Usuarios</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="users-table-container">
                <div class="table-responsive">
                    <table class="table users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha Registro</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            @php
                                $users = \App\Models\User::with('roles')->get();
                            @endphp
                            
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-sm">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="role-badge role-{{ $role->name }}">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <span class="status-badge active">
                                        <i class="fas fa-circle" style="font-size: 8px;"></i> Activo
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-icon btn-info" onclick="editUser({{ $user->id }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn-icon btn-danger" onclick="deleteUser({{ $user->id }})" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <p>No hay usuarios registrados</p>
                                    </div>
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

<!-- Modal Crear Usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Crear Nuevo Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" class="form-select" required>
                            <option value="">Seleccionar rol</option>
                            <option value="super-admin">Super Administrador</option>
                            <option value="admin">Administrador</option>
                            <option value="operator">Operador</option>
                            <option value="user">Usuario</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i> Editar Usuario
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" id="editUserForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="editUserId">
                    <div class="mb-3">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña (opcional)</label>
                        <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para mantener actual">
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="role" id="editRole" class="form-select" required>
                            <option value="super-admin">Super Administrador</option>
                            <option value="admin">Administrador</option>
                            <option value="operator">Operador</option>
                            <option value="user">Usuario</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/users.css') }}">
@endpush

@push('scripts')
<script>
    function searchUsers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#usersTableBody tr');
        
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function filterByRole() {
        const role = document.getElementById('roleFilter').value;
        const rows = document.querySelectorAll('#usersTableBody tr');
        
        rows.forEach(row => {
            if (role === 'all') {
                row.style.display = '';
                return;
            }
            const roleCell = row.querySelector('td:nth-child(4)');
            if (roleCell && roleCell.innerText.toLowerCase().includes(role)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    
    function exportUsers() {
        alert('Exportando lista de usuarios...');
    }
    
    function editUser(userId) {
        fetch(`/admin/users/${userId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('editUserId').value = data.id;
                document.getElementById('editName').value = data.name;
                document.getElementById('editEmail').value = data.email;
                document.getElementById('editRole').value = data.role || 'user';
                document.getElementById('editUserForm').action = `/admin/users/${userId}`;
                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario');
            });
    }
    
    function deleteUser(userId) {
        if (confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
            fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error al eliminar el usuario');
                }
            });
        }
    }
    
    document.getElementById('searchInput')?.addEventListener('keyup', searchUsers);
    document.getElementById('roleFilter')?.addEventListener('change', filterByRole);
    
    document.getElementById('createUserForm')?.addEventListener('submit', function(e) {
        const password = this.querySelector('input[name="password"]').value;
        if (password && password.length < 6) {
            e.preventDefault();
            alert('La contraseña debe tener al menos 6 caracteres');
        }
    });
</script>
@endpush