@extends('layouts.app')

@push('styles')
<style>
    .proceso-card {
        transition: all 0.3s ease;
    }
    .proceso-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .loading-spinner {
        width: 3rem;
        height: 3rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Buscar Proceso Judicial') }}</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="buscarProcesoForm" class="needs-validation" novalidate onsubmit="event.preventDefault(); return false;">
                        @csrf
                        <div class="row mb-4">
                            <div class="col-md-8 offset-md-2">
                                <div class="input-group">
                                    <input
                                        id="numero_radicacion"
                                        type="text"
                                        class="form-control form-control-lg @error('numero_radicacion') is-invalid @enderror"
                                        name="numero_radicacion"
                                        value="{{ old('numero_radicacion') }}"
                                        placeholder="Ingrese el número de radicación"
                                        required
                                        autocomplete="off"
                                        autofocus>
                                    <button
                                        class="btn btn-primary btn-lg"
                                        type="submit"
                                        id="btnBuscar">
                                        <i class="fas fa-search me-2"></i> Buscar
                                    </button>
                                    @error('numero_radicacion')
                                        <div class="invalid-feedback">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Ingrese el número de radicación completo para buscar el proceso judicial.
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Sección de resultados -->
                    <div id="resultado" class="mt-4" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">
                                <i class="fas fa-search me-2"></i>Resultado de la búsqueda:
                            </h5>
                            <button id="btnNuevaBusqueda" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-redo me-1"></i>Nueva búsqueda
                            </button>
                        </div>

                        <div id="loading" class="text-center my-5 py-5">
                            <div class="spinner-border text-primary loading-spinner" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-3 text-muted">Buscando proceso, por favor espere...</p>
                        </div>

                        <div id="resultadoContenido" class="proceso-card"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles adicionales -->
<div class="modal fade" id="detallesProcesoModal" tabindex="-1" aria-labelledby="detallesProcesoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="detallesProcesoModalLabel">Detalles del Proceso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetallesContenido">
                <!-- El contenido se cargará dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarProceso">
                    <i class="fas fa-save me-1"></i> Guardar Proceso
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Esperar a que jQuery esté listo
jQuery(document).ready(function($) {
    console.log('jQuery cargado correctamente');
    
    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo = 'info') {
        console.log('Mostrando mensaje:', mensaje);
        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show mt-3">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        
        // Insertar al principio del contenedor principal
        $('.container').prepend(alerta);
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Función para buscar proceso
    function buscarProceso(event) {
        event.preventDefault();
        console.log('Iniciando búsqueda...');
        
        const $btnBuscar = $('#btnBuscar');
        const $input = $('#numero_radicacion');
        const $form = $('#buscarProcesoForm');
        
        // Validar formulario
        if ($form[0].checkValidity() === false) {
            $form.addClass('was-validated');
            return false;
        }
        
        // Deshabilitar botón y mostrar spinner
        const btnOriginal = $btnBuscar.html();
        $btnBuscar.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm"></span> Buscando...');
        
        // Mostrar mensaje
        mostrarMensaje('Buscando proceso, por favor espere...', 'info');
        
        // Simular búsqueda (reemplazar con llamada AJAX real)
        setTimeout(() => {
            console.log('Búsqueda completada');
            mostrarMensaje('Búsqueda completada (simulación)', 'success');
            
            // Restaurar botón
            $btnBuscar.prop('disabled', false).html(btnOriginal);
        }, 2000);
        
        return false;
    }
    
    // Asignar manejador de eventos
    $(document).on('submit', '#buscarProcesoForm', buscarProceso);
    
    // También asignar al botón por si acaso
    $('#btnBuscar').on('click', buscarProceso);
    
    // Mensaje de bienvenida
    mostrarMensaje('Sistema listo para buscar. Ingrese un número de radicación.', 'info');
    
    console.log('Inicialización completada');
});
</script>
@endpush
