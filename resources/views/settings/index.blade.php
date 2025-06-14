@extends('layouts.app')

@section('title', 'Configuración')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <h2>Configuración</h2>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información General</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="company_name" class="form-label">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                   value="{{ old('company_name', $settings['company_name']) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="timezone" class="form-label">Zona Horaria</label>
                            <select class="form-select" id="timezone" name="timezone" required>
                                <option value="America/New_York" {{ old('timezone', $settings['timezone']) === 'America/New_York' ? 'selected' : '' }}>
                                    America/New_York
                                </option>
                                <option value="America/Bogota" {{ old('timezone', $settings['timezone']) === 'America/Bogota' ? 'selected' : '' }}>
                                    America/Bogota
                                </option>
                                <option value="America/Mexico_City" {{ old('timezone', $settings['timezone']) === 'America/Mexico_City' ? 'selected' : '' }}>
                                    America/Mexico_City
                                </option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Notificaciones</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Email de Notificaciones</label>
                        <input type="email" class="form-control" name="email_notifications" 
                               value="{{ old('email_notifications', $settings['email_notifications'] ? 'activado' : '') }}">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="emailNotifications" name="email_notifications" 
                                   {{ old('email_notifications', $settings['email_notifications']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="emailNotifications">
                                Activar notificaciones por email
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


