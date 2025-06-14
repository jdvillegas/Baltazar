@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Enviar Nueva Notificación</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.notifications.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Título</label>
                            <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required>

                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea id="message" class="form-control @error('message') is-invalid @enderror" name="message" required>{{ old('message') }}</textarea>

                            @error('message')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="users" class="form-label">Seleccionar Usuarios</label>
                            <select id="users" class="form-control @error('users') is-invalid @enderror" name="users[]" multiple required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>

                            @error('users')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary me-2">
                                <i class="material-icons">arrow_back</i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons">send</i> Enviar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
