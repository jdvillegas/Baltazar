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
                                        <td>
                                            <div>
                                                @foreach($notification->users as $user)
                                                    <span class="badge bg-primary me-1">
                                                        {{ $user->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @foreach($notification->users as $user)
                                                    <span class="badge {{ $notification->isReadBy($user) ? 'bg-success' : 'bg-warning' }} me-1">
                                                        {{ $user->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div>
                                                @foreach($notification->users as $user)
                                                    <span class="badge bg-info me-1">
                                                        {{ $notification->isReadBy($user) ? $notification->users->find($user->id)->pivot->read_at->format('Y-m-d H:i') : 'No leída' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @foreach($notification->users as $user)
                                                    @if(!$notification->isReadBy($user))
                                                        <form action="{{ route('admin.notifications.send') }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="notification_id" value="{{ $notification->id }}">
                                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="material-icons">done</i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endforeach
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
