@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Editar Usuario</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name" autofocus>

                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="membership_type" class="form-label">Tipo de Membresía</label>
                            <select id="membership_type" class="form-control @error('membership_type') is-invalid @enderror" name="membership_type" required>
                                <option value="trial" {{ old('membership_type', $user->membership_type) === 'trial' ? 'selected' : '' }}>Prueba</option>
                                <option value="free" {{ old('membership_type', $user->membership_type) === 'free' ? 'selected' : '' }}>Gratis</option>
                                <option value="premium" {{ old('membership_type', $user->membership_type) === 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('membership_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_open_cases" class="form-label">Límite de Casos Activos</label>
                            <input id="max_open_cases" type="number" min="1" class="form-control @error('max_open_cases') is-invalid @enderror" name="max_open_cases" value="{{ old('max_open_cases', $user->max_open_cases) }}" required>
                            @error('max_open_cases')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="roles" class="form-label">Roles</label>
                            <select id="roles" class="form-control @error('roles') is-invalid @enderror" name="roles[]" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('roles')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="membership_type" class="form-label">Tipo de Membresía</label>
                            <select id="membership_type" class="form-control @error('membership_type') is-invalid @enderror" name="membership_type" required>
                                <option value="free" {{ $user->membership_type === 'free' ? 'selected' : '' }}>Gratis</option>
                                <option value="premium" {{ $user->membership_type === 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>

                            @error('membership_type')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="max_open_cases" class="form-label">Casos Activos Máximos</label>
                            <input id="max_open_cases" type="number" class="form-control @error('max_open_cases') is-invalid @enderror" 
                                   name="max_open_cases" value="{{ old('max_open_cases', $user->max_open_cases) }}" required min="1">

                            @error('max_open_cases')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Estado</label>
                            <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                                <option value="active" {{ $user->status === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ $user->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>

                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                                <i class="material-icons">arrow_back</i> Volver
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons">save</i> Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
