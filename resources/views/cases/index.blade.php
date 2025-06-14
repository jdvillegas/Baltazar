@extends('layouts.app')

@section('title', 'Casos')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Casos</h2>
        <a href="{{ route('cases.create') }}" class="btn btn-primary">
            <i class="material-icons">add</i> Nuevo Caso
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!isset($cases) || $cases->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">
                                    No hay casos registrados
                                </td>
                            </tr>
                        @else
                            @foreach($cases as $case)
                                <tr>
                                    <td>{{ $case->id }}</td>
                                    <td>{{ $case->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $case->status === 'pendiente' ? 'warning' : ($case->status === 'en_proceso' ? 'info' : ($case->status === 'completado' ? 'success' : 'danger')) }}">
                                            {{ ucfirst($case->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <form action="{{ route('cases.destroy', $case) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar este caso?')" data-bs-toggle="tooltip" title="Eliminar">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>
@endpush


