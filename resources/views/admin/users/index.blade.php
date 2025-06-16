@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Gestión de Usuarios</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3">
                        <h4>Lista de Usuarios</h4>
                        @can('users.create')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="material-icons">add</i> Nuevo Usuario
                            </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Membresía</th>
                                    <th>Casos Activos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge bg-primary">{{ $role->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->membership_type === 'premium' ? 'success' : 'info' }}">
                                                {{ ucfirst($user->membership_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $user->cases->count() }} / {{ $user->max_open_cases }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->status === 'inactive')
                                                <span class="badge bg-warning">Inactivo</span>
                                            @else
                                                <span class="badge bg-success">Activo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @if($user->status === 'active')
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary" title="Editar">
                                                        <i class="material-icons">edit</i>
                                                    </a>
                                                    
                                                    <form action="{{ route('users.inactivate', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de inactivar este usuario?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Inactivar">
                                                            <i class="material-icons">lock</i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('users.activate', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de activar este usuario?')">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-success" title="Activar">
                                                            <i class="material-icons">lock_open</i>
                                                        </button>
                                                    </form>
                                                @endif
                                                
                                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
