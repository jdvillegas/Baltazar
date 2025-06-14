@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Crear Nuevo Ticket de Soporte</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.support.store') }}">
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
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" required>{{ old('description') }}</textarea>

                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Prioridad</label>
                            <select id="priority" class="form-control @error('priority') is-invalid @enderror" name="priority" required>
                                <option value="low">Baja</option>
                                <option value="medium">Media</option>
                                <option value="high">Alta</option>
                            </select>

                            @error('priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.support.index') }}" class="btn btn-secondary me-2">
                                <i class="material-icons">arrow_back</i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons">send</i> Crear Ticket
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
