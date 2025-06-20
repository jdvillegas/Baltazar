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

    // Variables globales
    let procesoActual = null;

    // Función para mostrar mensajes
    function mostrarMensaje(mensaje, tipo = 'info') {
        console.log('Mostrando mensaje:', mensaje);
        // Limpiar mensajes anteriores
        $('.alert-dismissible').alert('close');

        const alerta = `
            <div class="alert alert-${tipo} alert-dismissible fade show mt-3">
                <i class="fas ${tipo === 'danger' ? 'fa-exclamation-circle' : 'fa-info-circle'} me-2"></i>
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
        const $resultado = $('#resultado');
        const $loading = $('#loading');
        const $resultadoContenido = $('#resultadoContenido');

        // Validar formulario
        if ($form[0].checkValidity() === false) {
            $form.addClass('was-validated');
            return false;
        }

        // Mostrar sección de resultados y loading
        $resultado.show();
        $loading.show();
        $resultadoContenido.hide();
        $resultadoContenido.html('');

        // Deshabilitar botón y mostrar spinner
        const btnOriginal = $btnBuscar.html();
        $btnBuscar.prop('disabled', true)
                 .html('<span class="spinner-border spinner-border-sm"></span> Buscando...');

        // Realizar petición AJAX
        $.ajax({
            url: '{{ route("cases.buscar.proceso") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                numero_radicacion: $input.val()
            },
            dataType: 'json',
            success: function(response) {
                console.log('Respuesta recibida:', response);

                if (response.success) {
                    // Guardar datos del proceso para usarlos después
                    procesoActual = response.data[0];

                    // Mostrar los datos del proceso
                    mostrarResultado(procesoActual);

                    // Mostrar mensaje de éxito
                    mostrarMensaje('Proceso encontrado correctamente', 'success');
                } else {
                    mostrarMensaje(response.message || 'No se encontraron resultados', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la petición:', error);
                let errorMessage = 'Error al realizar la búsqueda';

                try {
                    const response = xhr.responseJSON;
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta de error:', e);
                }

                mostrarMensaje(errorMessage, 'danger');
            },
            complete: function() {
                // Ocultar loading y restaurar botón
                $loading.hide();
                $resultadoContenido.show();
                $btnBuscar.prop('disabled', false).html(btnOriginal);

                // Hacer scroll a los resultados
                $('html, body').animate({
                    scrollTop: $resultado.offset().top - 20
                }, 500);
            }
        });

        return false;
    }

    // Función para mostrar el resultado de la búsqueda
    function mostrarResultado(proceso) {
        const $resultadoContenido = $('#resultadoContenido');
        let html = `
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel me-2"></i>
                        ${proceso.tipo_proceso || 'Proceso Judicial'}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Número de Radicación:</strong></p>
                            <p>${proceso.numero_radicacion || 'No disponible'}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Despacho:</strong></p>
                            <p>${proceso.despacho || 'No disponible'}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ubicación:</strong></p>
                            <p>${(proceso.ciudad || '')} ${proceso.departamento ? `, ${proceso.departamento}` : ''}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Fecha del Proceso:</strong></p>
                            <p>${proceso.fecha_proceso ? new Date(proceso.fecha_proceso).toLocaleDateString() : 'No disponible'}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <p class="mb-1"><strong>Información de los Sujetos Procesales:</strong></p>
                            <div class="card">
                                <div class="card-body p-3">
                                    ${JSON.stringify(proceso.sujetos_procesales || [], null, 2)}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary btn-guardar-proceso">
                            <i class="fas fa-save me-1"></i> Guardar Caso para Seguimiento.
                        </button>
                    </div>
                </div>
            </div>`;

        $resultadoContenido.html(html);
    }

    // Función para formatear los sujetos procesales
    function formatearSujetosProcesales(sujetos) {
        if (!Array.isArray(sujetos) || sujetos.length === 0) {
            return 'No hay sujetos procesales registrados';
        }

        return sujetos.map(sujeto => {
            let info = [];
            if (sujeto.nombre) info.push(sujeto.nombre);
            if (sujeto.tipo) info.push(`(${sujeto.tipo})`);
            if (sujeto.documento) info.push(`[${sujeto.documento}]`);

            return info.join(' ');
        }).join('<br>');
    }

    // Función para guardar el proceso
    function guardarProceso() {
        if (!procesoActual) {
            mostrarMensaje('No hay un proceso para guardar', 'warning');
            return;
        }

        const $btnGuardar = $('.btn-guardar-proceso');
        const btnOriginal = $btnGuardar.html();

        // Deshabilitar botón y mostrar spinner
        $btnGuardar.prop('disabled', true)
                  .html('<span class="spinner-border spinner-border-sm"></span> Guardando...');

        // Realizar petición AJAX para guardar
        $.ajax({
            url: '{{ route("cases.guardar_proceso") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                proceso_id: procesoActual.id,
                llave_proceso: procesoActual.llave_proceso,
                numero_radicacion: procesoActual.numero_radicacion,
                tipo_proceso: procesoActual.tipo_proceso,
                departamento: procesoActual.departamento,
                ciudad: procesoActual.ciudad,
                despacho: procesoActual.despacho,
                fecha_proceso: procesoActual.fecha_proceso,
                sujetos_procesales: JSON.stringify(procesoActual.sujetos_procesales || [])
            },
            dataType: 'json',
            success: function(response) {
                console.log('Proceso guardado:', response);
                if (response.success) {
                    mostrarMensaje('Proceso guardado correctamente', 'success');
                    // Redirigir al detalle del caso después de guardar
                    if (response.case_id) {
                        setTimeout(() => {
                            window.location.href = '/cases/' + response.case_id;
                        }, 1500);
                    }
                } else {
                    mostrarMensaje(response.message || 'Error al guardar el proceso', 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar el proceso:', error);
                let errorMessage = 'Error al guardar el proceso';

                try {
                    const response = xhr.responseJSON;
                    if (response && response.message) {
                        errorMessage = response.message;
                    } else if (xhr.status === 422 && response.errors) {
                        // Mostrar el primer error de validación
                        const firstError = Object.values(response.errors)[0][0];
                        if (firstError) {
                            errorMessage = firstError;
                        }
                    }
                } catch (e) {
                    console.error('Error al procesar la respuesta de error:', e);
                }

                mostrarMensaje(errorMessage, 'danger');
            },
            complete: function() {
                // Restaurar botón
                $btnGuardar.prop('disabled', false).html(btnOriginal);
            }
        });
    }

    // Asignar manejadores de eventos
    $(document)
        .on('submit', '#buscarProcesoForm', buscarProceso)
        .on('click', '#btnBuscar', buscarProceso)
        .on('click', '.btn-guardar-proceso', function(e) {
            e.preventDefault();
            guardarProceso();
        })
        .on('click', '#btnNuevaBusqueda', function() {
            $('#resultado').hide();
            $('#numero_radicacion').val('').focus();
            procesoActual = null;
        });

    // Mensaje de bienvenida
    mostrarMensaje('Sistema listo para buscar. Ingrese un número de radicación.', 'info');

    console.log('Inicialización completada');
});
</script>
@endpush
