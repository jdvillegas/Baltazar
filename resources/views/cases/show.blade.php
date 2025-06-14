@extends('layouts.app')

@section('title', 'Detalles del Caso')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Detalles del Caso</h5>
            <div class="btn-group">
                <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-primary">
                    <i class="material-icons">edit</i> Editar
                </a>
                <form action="{{ route('cases.destroy', $case) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este caso?')">
                        <i class="material-icons">delete</i> Eliminar
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título</label>
                        <p>{{ $case->title }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estado</label>
                        <span class="badge bg-{{ $case->status === 'pendiente' ? 'warning' : ($case->status === 'en_proceso' ? 'info' : ($case->status === 'completado' ? 'success' : 'danger')) }}">
                            {{ ucfirst($case->status) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de Creación</label>
                        <p>{{ $case->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <p>{{ $case->description }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Creado por</label>
                        <p>{{ $case->user->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
