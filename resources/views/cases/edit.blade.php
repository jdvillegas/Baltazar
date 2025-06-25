@extends('layouts.app')

@section('title', 'Editar Estado del Caso')

@push('styles')
<style>
    .form-control-plaintext {
        background-color: #f8f9fa;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Editar Estado del Caso
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('cases.update', $case) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Información del Proceso</h5>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Número de Radicación</label>
                        <div class="form-control-plaintext">
                            {{ $case->llave_proceso ?? $case->numero_radicacion ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Tipo de Proceso</label>
                        <div class="form-control-plaintext">
                            {{ $case->tipo_proceso ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Demandante</label>
                        <div class="form-control-plaintext">
                            {{ $case->demandante ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Demandado</label>
                        <div class="form-control-plaintext">
                            {{ $case->demandado ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Despacho</label>
                        <div class="form-control-plaintext">
                            {{ $case->despacho ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Ubicación</label>
                        <div class="form-control-plaintext">
                            {{ ($case->ciudad ?? '') . ', ' . ($case->departamento ?? '') }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Fecha de Radicación</label>
                        <div class="form-control-plaintext">
                            {{ $case->fecha_radicacion ? \Carbon\Carbon::parse($case->fecha_radicacion)->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label detail-label">Fecha de Creación</label>
                        <div class="form-control-plaintext">
                            {{ $case->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Estado del Caso</h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label detail-label">Cambiar Estado</label>
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
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al listado
                    </a>
                    <div>
                        <a href="{{ route('cases.show', $case) }}" class="btn btn-outline-primary me-2">
                            <i class="fas fa-eye me-1"></i> Ver Detalles
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Actualizar Estado
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
