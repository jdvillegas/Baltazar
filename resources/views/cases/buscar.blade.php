@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Buscar Proceso Judicial') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form id="buscarProcesoForm">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="numero_radicacion" class="col-md-4 col-form-label text-md-right">
                                {{ __('Número de Radicación') }}
                            </label>

                            <div class="col-md-6">
                                <input id="numero_radicacion" type="text" 
                                    class="form-control @error('numero_radicacion') is-invalid @enderror" 
                                    name="numero_radicacion" 
                                    value="{{ old('numero_radicacion') }}" 
                                    required 
                                    autocomplete="off"
                                    autofocus>

                                @error('numero_radicacion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary" id="btnBuscar">
                                    {{ __('Buscar') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="resultado" class="mt-4" style="display: none;">
                        <h5>Resultado de la búsqueda:</h5>
                        <div id="resultadoContenido" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Verificar si hay errores de sintaxis
try {
    // Código existente
} catch (e) {
    console.error('Error de sintaxis detectado:', e);
}

// Verificar si jQuery está cargado
console.log('jQuery está cargado?', typeof jQuery !== 'undefined');
if (typeof jQuery !== 'undefined') {
    console.log('Versión de jQuery:', jQuery.fn.jquery);
}

// Verificar conflictos con $ de jQuery
(function($) {
    console.log('$ está disponible?', $ === jQuery);
})(jQuery);

// Manejador de errores global
window.onerror = function(message, source, lineno, colno, error) {
    console.error('Error global detectado:', {
        message: message,
        source: source,
        line: lineno,
        column: colno,
        error: error
    });
    return true; // Previene que el error se muestre en la consola por defecto
};

console.log('Script de búsqueda cargado correctamente');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM completamente cargado');
    
    // Verificar elementos del DOM
    const form = document.getElementById('buscarProcesoForm');
    const btnBuscar = document.getElementById('btnBuscar');
    const resultadoDiv = document.getElementById('resultado');
    const resultadoContenido = document.getElementById('resultadoContenido');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    console.log('Elementos del DOM:', {
        form: form ? 'Encontrado' : 'No encontrado',
        btnBuscar: btnBuscar ? 'Encontrado' : 'No encontrado',
        resultadoDiv: resultadoDiv ? 'Encontrado' : 'No encontrado',
        resultadoContenido: resultadoContenido ? 'Encontrado' : 'No encontrado',
        csrfToken: csrfToken ? 'Presente' : 'Ausente'
    });
    
    if (!form) {
        console.error('Error: No se encontró el formulario con ID "buscarProcesoForm"');
        return;
    }

    form.addEventListener('submit', async function(e) {
        console.log('Evento submit del formulario detectado');
        e.preventDefault();
        
        const numeroRadicacion = document.getElementById('numero_radicacion').value.trim();
        console.log('Iniciando búsqueda para:', numeroRadicacion);
        console.log('Valor del campo número de radicación:', numeroRadicacion);
        
        if (!numeroRadicacion) {
            const errorMsg = 'Por favor ingrese un número de radicación';
            console.error(errorMsg);
            mostrarError(errorMsg);
            return;
        }
        
        // Mostrar loading
        btnBuscar.disabled = true;
        btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
        
        // Mostrar mensaje de carga
        resultadoDiv.innerHTML = '<div class="alert alert-info">Buscando proceso, por favor espere...</div>';
        resultadoDiv.style.display = 'block';
        
        try {
            const url = '{{ route("cases.buscar.proceso") }}';
            console.log('Realizando petición AJAX a:', url);
            console.log('Headers:', {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            });
            console.log('Body:', JSON.stringify({
                numero_radicacion: numeroRadicacion
            }));
            
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    numero_radicacion: numeroRadicacion
                })
            });

            console.log('Estado de la respuesta:', response.status, response.statusText);
            
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            let data;
            
            if (contentType && contentType.includes('application/json')) {
                data = await response.json();
                console.log('Respuesta JSON recibida:', data);
            } else {
                const text = await response.text();
                console.error('La respuesta no es JSON:', text);
                throw new Error('La respuesta del servidor no es un JSON válido');
            }
            
            if (!data) {
                throw new Error('No se recibieron datos en la respuesta');
            }
            
            if (data.success) {
                mostrarResultadoExitoso(data);
            } else {
                throw new Error(data.message || 'Error al buscar el proceso');
            }
        } catch (error) {
            console.error('Error en la petición:', error);
            mostrarError('Error al procesar la solicitud: ' + (error.message || 'Error desconocido'));
            
            // Mostrar más detalles en la consola si están disponibles
            if (error.response) {
                console.error('Detalles del error:', {
                    status: error.response.status,
                    statusText: error.response.statusText,
                    data: error.response.data
                });
            }
        } finally {
            btnBuscar.disabled = false;
            btnBuscar.innerHTML = 'Buscar';
        }
    });
    
    function mostrarResultadoExitoso(data) {
        resultadoDiv.style.display = 'block';
        
        if (data.data && Array.isArray(data.data) && data.data.length > 0) {
            const proceso = data.data[0];
            let html = `
                <div class="alert alert-success">${data.message || 'Proceso encontrado correctamente'}</div>
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Detalles del Proceso</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Número de Radicación:</strong><br>${proceso.numero_radicacion || 'N/A'}</p>
                                <p class="mb-2"><strong>Despacho:</strong><br>${proceso.despacho || 'N/A'}</p>
                                <p class="mb-2"><strong>Ciudad:</strong><br>${proceso.ciudad || 'N/A'}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Fecha de Proceso:</strong><br>${proceso.fecha_proceso ? new Date(proceso.fecha_proceso).toLocaleDateString() : 'N/A'}</p>
                                <p class="mb-2"><strong>Tipo de Proceso:</strong><br>${proceso.tipo_proceso || 'N/A'}</p>
                                <p class="mb-2"><strong>Departamento:</strong><br>${proceso.departamento || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <div class="sujetos-procesales mb-4">
                            <h6 class="border-bottom pb-2">Sujetos Procesales</h6>
                            ${formatearSujetosProcesales(proceso.sujetos_procesales || [])}
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('resultado').style.display = 'none';">
                                <i class="fas fa-times me-1"></i> Cerrar
                            </button>
                            <button type="button" class="btn btn-primary guardar-proceso" 
                                data-proceso='${JSON.stringify(proceso)}'>
                                <i class="fas fa-save me-1"></i> Guardar Caso
                            </button>
                        </div>
                    </div>
                </div>`;
            
            data.data.forEach((proceso, index) => {
                // Asegurarse de que los campos existan para evitar errores
                const procesoData = {
                    id: proceso.id || '',
                    numero_radicacion: proceso.numero_radicacion || proceso.radicado || 'N/A',
                    llave_proceso: proceso.llave_proceso || 'N/A',
                    tipo_proceso: proceso.tipo_proceso || 'N/A',
                    departamento: proceso.departamento || 'N/A',
                    ciudad: proceso.ciudad || 'N/A',
                    despacho: proceso.despacho || 'N/A',
                    fecha_proceso: proceso.fecha_proceso || proceso.fecha_radicacion || 'N/A',
                    fecha_ultima_actuacion: proceso.fecha_ultima_actuacion || 'N/A',
                    sujetos_procesales: proceso.sujetos_procesales || []
                };
                
                html += `
                <div class="card mb-3">
                    <div class="card-header">
                        Proceso #${index + 1}
                    </div>
                    <div class="card-body proceso-container">
                        <h5 class="card-title">Radicado: ${procesoData.numero_radicacion}</h5>
                        <p><strong>Llave del Proceso:</strong> ${procesoData.llave_proceso}</p>
                        <p data-field="tipo_proceso"><strong>Tipo de Proceso:</strong> ${procesoData.tipo_proceso}</p>
                        <p data-field="departamento"><strong>Departamento:</strong> ${procesoData.departamento}</p>
                        <p data-field="ciudad"><strong>Ciudad:</strong> ${procesoData.ciudad}</p>
                        <p data-field="despacho"><strong>Despacho:</strong> ${procesoData.despacho}</p>
                        <p data-field="fecha_proceso"><strong>Fecha Proceso:</strong> ${procesoData.fecha_proceso}</p>
                        <p><strong>Última Actuación:</strong> ${procesoData.fecha_ultima_actuacion}</p>
                        <div class="mb-3">
                            <strong>Sujetos Procesales:</strong>
                            <div class="sujetos-procesales mt-2 p-2 border rounded">
                                ${formatearSujetosProcesales(procesoData.sujetos_procesales)}
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm guardar-proceso" 
                                    data-proceso-id="${procesoData.id}"
                                    data-llave-proceso="${procesoData.llave_proceso}">
                                <i class="material-icons">save</i> Guardar Caso
                            </button>
                            <a href="${'/cases/' + (procesoData.id || '')}" class="btn btn-outline-secondary btn-sm" style="display: ${procesoData.id ? 'inline-flex' : 'none'};">
                                <i class="material-icons">visibility</i> Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>`;
            });
            
            resultadoContenido.innerHTML = html;
            
            // Guardar los procesos en una variable global para usarlos después
            window.procesosEncontrados = data.data;
        } else {
            mostrarError(data.message || 'No se encontraron procesos con el número de radicación proporcionado');
        }
    }
    
    function mostrarError(mensaje) {
        resultadoDiv.style.display = 'block';
        resultadoContenido.innerHTML = `
            <div class="alert alert-danger">
                ${mensaje}
            </div>`;
    }

    // Función para formatear los sujetos procesales
    function formatearSujetosProcesales(sujetos) {
        if (!sujetos || !Array.isArray(sujetos) || sujetos.length === 0) {
            return '<div class="alert alert-info mb-0">No se encontraron sujetos procesales</div>';
        }

        let demandantes = [];
        let demandados = [];
        let otros = [];
        
        // Clasificar los sujetos
        sujetos.forEach(sujeto => {
            if (!sujeto || !sujeto.nombre) return;
            
            const tipo = (sujeto.tipoSujeto || '').toLowerCase();
            const item = `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${sujeto.nombre}</h6>
                        ${sujeto.tipoSujeto ? `<span class="badge bg-info">${sujeto.tipoSujeto}</span>` : ''}
                    </div>
                    ${sujeto.documento ? `<div class="text-muted small">Documento: ${sujeto.documento}</div>` : ''}
                    ${sujeto.tipoIdentificacion ? `<div class="text-muted small">Tipo ID: ${sujeto.tipoIdentificacion}</div>` : ''}
                    ${sujeto.direccion ? `<div class="text-muted small">Dirección: ${sujeto.direccion}</div>` : ''}
                    ${sujeto.correoElectronico ? `<div class="text-muted small">Email: ${sujeto.correoElectronico}</div>` : ''}
                </div>`;
                
            if (tipo.includes('demandante') || tipo.includes('actor')) {
                demandantes.push(item);
            } else if (tipo.includes('demandado')) {
                demandados.push(item);
            } else {
                otros.push(item);
            }
        });
        
        let html = '';
        
        // Mostrar demandantes si existen
        if (demandantes.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-user-tie me-2"></i>Demandante(s)</h6>
                    <div class="list-group list-group-flush">
                        ${demandantes.join('')}
                    </div>
                </div>`;
        }
        
        // Mostrar demandados si existen
        if (demandados.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-danger"><i class="fas fa-user me-2"></i>Demandado(s)</h6>
                    <div class="list-group list-group-flush">
                        ${demandados.join('')}
                    </div>
                </div>`;
        }
        
        // Mostrar otros sujetos si existen
        if (otros.length > 0) {
            html += `
                <div class="mb-3">
                    <h6 class="text-secondary"><i class="fas fa-users me-2"></i>Otros Intervinientes</h6>
                    <div class="list-group list-group-flush">
                        ${otros.join('')}
                    </div>
                </div>`;
        }
        
        return html;
    }
    
    // Función para extraer los datos del proceso del DOM
    function getProcesoData(container, button) {
        const procesoData = {
            proceso_id: button.dataset.procesoId || '',
            llave_proceso: button.dataset.llaveProceso || '',
            numero_radicacion: container.querySelector('.card-title')?.textContent.replace('Radicado:', '').trim() || '',
            tipo_proceso: container.querySelector('[data-field="tipo_proceso"]')?.textContent.replace('Tipo de Proceso:', '').trim() || '',
            departamento: container.querySelector('[data-field="departamento"]')?.textContent.replace('Departamento:', '').trim() || '',
            ciudad: container.querySelector('[data-field="ciudad"]')?.textContent.replace('Ciudad:', '').trim() || '',
            despacho: container.querySelector('[data-field="despacho"]')?.textContent.replace('Despacho:', '').trim() || '',
            fecha_proceso: container.querySelector('[data-field="fecha_proceso"]')?.textContent.replace('Fecha Proceso:', '').trim() || '',
            sujetos_procesales: []
        };
        
        // Obtener sujetos procesales
        const sujetosElements = container.querySelectorAll('.sujeto-procesal');
        sujetosElements.forEach(sujeto => {
            procesoData.sujetos_procesales.push({
                tipo: sujeto.dataset.tipo || '',
                nombre: sujeto.textContent.trim() || ''
            });
        });
        
        return procesoData;
    }
    
    // Manejador del botón guardar
    document.addEventListener('click', async function(e) {
        if (e.target && (e.target.classList.contains('guardar-proceso') || e.target.closest('.guardar-proceso'))) {
            const button = e.target.classList.contains('guardar-proceso') ? e.target : e.target.closest('.guardar-proceso');
            const container = button.closest('.proceso-container');
            
            if (!button.dataset.procesoId) {
                mostrarError('No se pudo obtener la información del proceso');
                return;
            }
            
            const procesoData = getProcesoData(container, button);
            
            // Deshabilitar el botón y mostrar spinner
            button.disabled = true;
            const originalText = button.innerHTML;
            button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            try {
                console.log('Enviando datos del proceso:', procesoData);
                
                const response = await fetch('{{ route("cases.guardar-proceso") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(procesoData)
                });

                console.log('Estado de la respuesta:', response.status, response.statusText);
                
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                let data;
                
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                    console.log('Respuesta JSON recibida:', data);
                } else {
                    const text = await response.text();
                    console.error('La respuesta no es JSON:', text);
                    throw new Error('La respuesta del servidor no es un JSON válido');
                }
                
                if (!data) {
                    throw new Error('No se recibieron datos en la respuesta');
                }
                
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success mt-2';
                    alertDiv.textContent = data.message || 'Caso guardado correctamente';
                    container.insertBefore(alertDiv, button.parentNode.nextSibling);
                    
                    // Ocultar el botón de guardar
                    button.style.display = 'none';
                    
                    // Mostrar el botón de ver detalles si hay un ID de caso
                    const verDetallesBtn = container.querySelector('a[href^="/cases/"]');
                    if (verDetallesBtn && data.case_id) {
                        verDetallesBtn.href = `/cases/${data.case_id}`;
                        verDetallesBtn.style.display = 'inline-flex';
                    }
                } else {
                    // Mostrar mensaje de error
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mt-2';
                    errorDiv.textContent = data.message || 'Error al guardar el caso';
                    container.insertBefore(errorDiv, button.parentNode.nextSibling);
                    
                    // Restaurar el botón
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Error en la petición:', error);
                
                // Mostrar mensaje de error
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-2';
                errorDiv.textContent = 'Error al procesar la solicitud: ' + (error.message || 'Error desconocido');
                container.insertBefore(errorDiv, button.parentNode.nextSibling);
                
                // Restaurar el botón
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    });
});
</script>

<script>
// Script de prueba
console.log('Script de prueba ejecutándose');
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded en script de prueba');
    
    // Verificar si el formulario existe
    const form = document.getElementById('buscarProcesoForm');
    console.log('Formulario encontrado en script de prueba:', form ? 'Sí' : 'No');
    
    // Agregar un manejador de eventos simple
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Evento submit capturado en script de prueba');
            e.preventDefault();
            alert('¡El formulario se ha enviado! (desde script de prueba)');
        });
    }
});
</script>

@endpush

@endsection
