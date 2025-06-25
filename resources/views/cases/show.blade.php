@extends('layouts.app')

@section('title', 'Detalles del Caso')

@push('styles')
<style>
    .detail-card {
        border-left: 4px solid #0d6efd;
        transition: all 0.3s ease;
    }
    .detail-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .detail-label {
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    .detail-value {
        font-size: 1.05rem;
        word-break: break-word;
    }
    .badge-status {
        font-size: 0.9rem;
        padding: 0.4em 0.8em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Casos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalles del Caso</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-folder-open text-primary me-2"></i>
                        {{ $case->title ?? 'Caso sin título' }}
                    </h4>
                    <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Editar Estado
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="detail-label">Estado Actual</span>
                                <div>
                                    @php
                                        $statusColors = [
                                            'pendiente' => 'warning',
                                            'en_proceso' => 'info',
                                            'completado' => 'success',
                                            'cancelado' => 'danger',
                                            'anulado' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'pendiente' => 'Pendiente',
                                            'en_proceso' => 'En Proceso',
                                            'completado' => 'Completado',
                                            'cancelado' => 'Cancelado',
                                            'anulado' => 'Anulado'
                                        ];
                                        $color = $statusColors[$case->status] ?? 'secondary';
                                        $label = $statusLabels[$case->status] ?? ucfirst($case->status);
                                    @endphp
                                    <span class="badge bg-{{ $color }} badge-status">{{ $label }}</span>
                                </div>
                            </div>
                            
                            @if($case->llave_proceso || $case->id_proceso)
                            <div class="mb-3">
                                <span class="detail-label">Identificación del Proceso</span>
                                <p class="detail-value">
                                    @if($case->llave_proceso)
                                        <strong>Llave:</strong> {{ $case->llave_proceso }}<br>
                                    @endif
                                    @if($case->id_proceso)
                                        <strong>ID Proceso:</strong> {{ $case->id_proceso }}
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($case->fecha_radicacion || $case->fecha_proceso)
                            <div class="mb-3">
                                <span class="detail-label">Fechas Importantes</span>
                                <p class="detail-value">
                                    @if($case->fecha_radicacion)
                                        <strong>Radicación:</strong> {{ \Carbon\Carbon::parse($case->fecha_radicacion)->format('d/m/Y') }}<br>
                                    @endif
                                    @if($case->fecha_ultima_actuacion)
                                        <strong>Última Actuación:</strong> {{ \Carbon\Carbon::parse($case->fecha_ultima_actuacion)->format('d/m/Y H:i') }}<br>
                                    @endif
                                    @if($case->fecha_proceso)
                                        <strong>Fecha del Proceso:</strong> {{ \Carbon\Carbon::parse($case->fecha_proceso)->format('d/m/Y') }}<br>
                                    @endif
                                    <strong>Creación:</strong> {{ $case->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            @if($case->departamento || $case->ciudad)
                            <div class="mb-3">
                                <span class="detail-label">Ubicación</span>
                                <p class="detail-value">
                                    @if($case->departamento)
                                        <strong>Departamento:</strong> {{ $case->departamento }}<br>
                                    @endif
                                    @if($case->ciudad)
                                        <strong>Ciudad:</strong> {{ $case->ciudad }}
                                    @endif
                                </p>
                            </div>
                            @endif

                            @if($case->despacho)
                            <div class="mb-3">
                                <span class="detail-label">Despacho/Juzgado</span>
                                <p class="detail-value">{{ $case->despacho }}</p>
                            </div>
                            @endif

                            @if($case->user)
                            <div class="mb-3">
                                <span class="detail-label">Responsable</span>
                                <p class="detail-value">
                                    <i class="fas fa-user-tie me-2 text-muted"></i>
                                    {{ $case->user->name }}
                                </p>
                            </div>
                            @endif
                        </div>
                    </div>

                    @if($case->sujetos_procesales)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card detail-card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-users me-2 text-primary"></i>
                                        Sujetos Procesales
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        $sujetos = is_string($case->sujetos_procesales) 
                                                ? json_decode($case->sujetos_procesales, true) 
                                                : $case->sujetos_procesales;
                                        $sujetos = is_array($sujetos) ? $sujetos : [];
                                    @endphp
                                    
                                    @if(count($sujetos) > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Tipo</th>
                                                        <th>Nombre</th>
                                                        <th>Documento</th>
                                                        <th>Teléfono</th>
                                                        <th>Correo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($sujetos as $sujeto)
                                                        <tr>
                                                            <td>{{ $sujeto['tipo'] ?? 'N/A' }}</td>
                                                            <td>{{ $sujeto['nombre'] ?? 'N/A' }}</td>
                                                            <td>{{ $sujeto['documento'] ?? 'N/A' }}</td>
                                                            <td>{{ $sujeto['telefono'] ?? 'N/A' }}</td>
                                                            <td>{{ $sujeto['correo'] ?? 'N/A' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No hay información de sujetos procesales disponible.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($case->description)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card detail-card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        Descripción Adicional
                                    </h5>
                                </div>
                                <div class="card-body">
                                    {!! nl2br(e($case->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cases.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Volver al Listado
                        </a>
                        <div>
                            <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-2"></i> Editar Estado
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
