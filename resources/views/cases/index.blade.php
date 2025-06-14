@extends('layouts.app')

@php
    use Carbon\Carbon;
@endphp

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
        @php
            $user = auth()->user();
            $openCases = \App\Models\CaseModel::where('status', 'pendiente')
                ->orWhere('status', 'en_proceso')
                ->orWhere('status', 'anulado')
                ->count();
        @endphp
        @if($openCases < $user->max_open_cases)
            <a href="{{ route('cases.create') }}" class="btn btn-primary">
                <i class="material-icons">add</i> Nuevo Caso
            </a>
        @else
            <div class="btn btn-secondary disabled">
                <i class="material-icons">add</i> Límite alcanzado
            </div>
        @endif
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
                                        <span class="badge {{ $case->status === 'anulado' ? 'bg-secondary text-dark' : ($case->status === 'pendiente' ? 'bg-warning text-dark' : ($case->status === 'en_proceso' ? 'bg-info text-dark' : ($case->status === 'completado' ? 'bg-success text-dark' : 'bg-danger text-dark'))) }}">
                                            {{ ucfirst($case->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $case->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        @if($case->status !== 'anulado')
                                            <div class="btn-group">
                                                <a href="{{ route('cases.edit', $case) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Editar">
                                                    <i class="material-icons">edit</i>
                                                </a>
                                                <form action="{{ route('cases.destroy', $case) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de anular este caso?')" data-bs-toggle="tooltip" title="Anular">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="text-muted small">
                                                Acciones no disponibles
                                            </div>
                                            <div class="text-muted small">
                                                Anulado el {{ $case->anulled_at ? \Carbon\Carbon::parse($case->anulled_at)->format('Y-m-d H:i') : 'Fecha no disponible' }}
                                            </div>
                                            <div class="text-muted small">
                                                <i class="material-icons">delete</i> Anulado
                                            </div>
                                        @endif
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


