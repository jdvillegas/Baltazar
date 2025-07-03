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
    
    /* Estilos para la sección de actuaciones */
    .actuacion-card {
        border-left: 3px solid #0d6efd;
        transition: all 0.2s ease;
    }
    
    .actuacion-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .actuacion-date {
        min-width: 100px;
    }
    
    .actuacion-actions {
        min-width: 100px;
        text-align: center;
    }
    
    .actuacion-description {
        max-width: 300px;
    }
    
    @media (max-width: 768px) {
        .actuacion-description {
            max-width: 200px;
        }
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
                    <div class="d-flex gap-2">
                        @if($case->llave_proceso)
                        <button id="btn-actualizar-actuaciones" class="btn btn-outline-primary btn-sm" 
                                data-case-id="{{ $case->id }}">
                            <i class="fas fa-sync-alt me-1"></i> Actualizar Actuaciones
                        </button>
                        @endif
                        <a href="{{ route('cases.edit', $case) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit me-1"></i> Editar Estado
                        </a>
                    </div>
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

                    <!-- Sección de Actuaciones -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card detail-card">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="fas fa-clipboard-list text-primary me-2"></i>
                                            Historial de Actuaciones
                                        </h5>
                                        <span class="badge bg-primary">{{ $case->actuaciones->count() }} registros</span>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    @if($case->actuaciones->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Actuación</th>
                                                        <th>Descripción</th>
                                                        <th>Estado</th>
                                                        <th class="text-center">Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($case->actuaciones as $actuacion)
                                                        <tr>
                                                            <td class="align-middle">
                                                                <div class="d-flex flex-column">
                                                                    <span class="fw-medium">{{ $actuacion->fecha_actuacion ? $actuacion->fecha_actuacion->format('d/m/Y') : 'N/A' }}</span>
                                                                    <small class="text-muted">{{ $actuacion->fecha_actuacion ? $actuacion->fecha_actuacion->format('h:i A') : '' }}</small>
                                                                </div>
                                                            </td>
                                                            <td class="align-middle">
                                                                <div class="fw-medium">{{ $actuacion->actuacion ?? 'N/A' }}</div>
                                                                @if($actuacion->tipo_actuacion)
                                                                    <small class="text-muted">{{ $actuacion->tipo_actuacion }}</small>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle">
                                                                @if($actuacion->anotacion)
                                                                    <div class="text-truncate" style="max-width: 250px;" 
                                                                         data-bs-toggle="tooltip" 
                                                                         title="{{ $actuacion->anotacion }}">
                                                                        {{ $actuacion->anotacion }}
                                                                    </div>
                                                                @else
                                                                    <span class="text-muted">Sin descripción</span>
                                                                @endif
                                                            </td>
                                                            <td class="align-middle">
                                                                @php
                                                                    $badgeClass = [
                                                                        'Activo' => 'bg-success',
                                                                        'Inactivo' => 'bg-secondary',
                                                                        'Finalizado' => 'bg-info',
                                                                        'Suspendido' => 'bg-warning',
                                                                    ][$actuacion->estado] ?? 'bg-secondary';
                                                                @endphp
                                                                <span class="badge {{ $badgeClass }}">
                                                                    {{ $actuacion->estado ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td class="text-center align-middle">
                                                                @if($actuacion->url_archivo)
                                                                    <a href="{{ $actuacion->url_archivo }}" 
                                                                       target="_blank" 
                                                                       class="btn btn-sm btn-outline-primary"
                                                                       data-bs-toggle="tooltip"
                                                                       title="Ver documento">
                                                                        <i class="fas fa-file-pdf"></i>
                                                                    </a>
                                                                @else
                                                                    <span class="text-muted">Sin archivo</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center p-4">
                                            <div class="mb-3">
                                                <i class="fas fa-clipboard-list fa-3x text-muted"></i>
                                            </div>
                                            <h5 class="text-muted">No hay actuaciones registradas</h5>
                                            <p class="text-muted mb-0">Las actuaciones se sincronizarán automáticamente cada 6 horas</p>
                                        </div>
                                    @endif
                                </div>
                                @if($case->actuaciones->count() > 0)
                                    <div class="card-footer bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Última actualización: {{ now()->format('d/m/Y h:i A') }}
                                            </small>
                                            <button id="btn-actualizar-actuaciones" class="btn btn-sm btn-outline-primary" onclick="actualizarActuaciones(); return false;">
                                                <i class="fas fa-sync-alt me-1"></i> Actualizar
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
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
@push('scripts')
<script>
    // Inicializar tooltips de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Manejar clic en el botón de actualizar actuaciones
        const btnActualizar = document.getElementById('btn-actualizar-actuaciones');
        if (btnActualizar) {
            btnActualizar.addEventListener('click', function() {
                const icono = btnActualizar.querySelector('i');
                const textoOriginal = btnActualizar.innerHTML;
                const caseId = btnActualizar.dataset.caseId;
                
                // Mostrar ícono de carga
                btnActualizar.disabled = true;
                btnActualizar.classList.add('disabled');
                icono.className = 'fas fa-spinner fa-spin me-1';
                
                // Hacer la petición al servidor
                fetch(`/cases/${caseId}/actualizar-actuaciones`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Error en la respuesta del servidor');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito con estadísticas
                        let mensaje = data.message;
                        if (data.stats && data.stats.nuevas > 0) {
                            mensaje += `\n${data.stats.nuevas} nuevas actuaciones encontradas.`;
                        } else {
                            mensaje += '\nNo hay nuevas actuaciones.';
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Actualización completada!',
                            text: mensaje,
                            timer: 3000,
                            showConfirmButton: true
                        });
                        
                        // Recargar la página después de 1.5 segundos
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error(data.message || 'Error al actualizar las actuaciones');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al actualizar las actuaciones. Por favor, inténtalo de nuevo más tarde.',
                        showConfirmButton: true
                    });
                })
                .finally(() => {
                    // Restaurar el botón
                    btnActualizar.disabled = false;
                    btnActualizar.classList.remove('disabled');
                    btnActualizar.innerHTML = textoOriginal;
                    const icon = btnActualizar.querySelector('i');
                    if (icon) icon.className = 'fas fa-sync-alt me-1';
                });
            });
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* Estilos para el botón de actualizar */
    .btn-update-actuaciones {
        position: relative;
        overflow: hidden;
    }
    
    .btn-update-actuaciones .spinner-border {
        display: none;
        margin-right: 0.5rem;
    }
    
    .btn-update-actuaciones.loading .spinner-border {
        display: inline-block;
    }
    
    .btn-update-actuaciones.loading span {
        visibility: hidden;
    }
</style>
@endpush

@endsection
