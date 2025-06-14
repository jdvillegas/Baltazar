@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Gestión de Notificaciones</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3">
                        <h4>Lista de Notificaciones</h4>
                        @can('notifications.create')
                            <a href="{{ route('admin.notifications.create') }}" class="btn btn-primary">
                                <i class="material-icons">add</i> Nueva Notificación
                            </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Fecha de Creación</th>
                                    <th>Fecha de Lectura</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($notifications as $notification)
                                    <tr>
                                        <td>{{ $notification->id }}</td>
                                        <td>{{ $notification->title }}</td>
                                        <td>{{ $notification->user->name }}</td>
                                        <td>
                                            @if($notification->read_at)
                                                <span class="badge bg-success">Leída</span>
                                            @else
                                                <span class="badge bg-warning">No leída</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            {{ $notification->read_at ? $notification->read_at->format('Y-m-d H:i') : '-' }}
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
