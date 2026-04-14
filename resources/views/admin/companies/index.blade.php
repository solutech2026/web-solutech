@extends('layouts.admin')

@section('title', 'Gestionar Compañías')
@section('header', 'Empresas y Colegios')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Listado de Compañías</h3>
        <a href="{{ route('admin.companies.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Compañía
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td>{{ $company->id }}</td>
                    <td>{{ $company->name }}</td>
                    <td>
                        @if($company->type == 'company')
                            <span class="badge bg-primary">Empresa</span>
                        @else
                            <span class="badge bg-success">Colegio</span>
                        @endif
                    </td>
                    <td>{{ $company->email ?? '—' }}</td>
                    <td>{{ $company->phone ?? '—' }}</td>
                    <td>
                        @if($company->is_active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar esta compañía?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{ $companies->links() }}
    </div>
</div>
@endsection