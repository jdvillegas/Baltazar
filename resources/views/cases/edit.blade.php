@extends('layouts.app')

@section('title', 'Editar Caso')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Editar Caso</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cases.update', $case) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" 
                           value="{{ old('title', $case->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                              name="description" rows="3" required>{{ old('description', $case->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Estado</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="pendiente" {{ old('status', $case->status) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="en_proceso" {{ old('status', $case->status) === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="completado" {{ old('status', $case->status) === 'completado' ? 'selected' : '' }}>Completado</option>
                        <option value="cancelado" {{ old('status', $case->status) === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('cases.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
