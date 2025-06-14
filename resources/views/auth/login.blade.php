<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Iniciar Sesión - Baltazar</title>
        
        <!-- Material Design Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom CSS -->
        <style>
            .auth-container {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .auth-card {
                max-width: 400px;
                width: 100%;
                padding: 30px;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .auth-card h1 {
                text-align: center;
                margin-bottom: 30px;
                color: #1a237e;
            }
            .form-control:focus {
                border-color: #1a237e;
                box-shadow: 0 0 0 0.2rem rgba(26, 35, 126, 0.25);
            }
            .btn-primary {
                background-color: #1a237e;
                border-color: #1a237e;
            }
            .btn-primary:hover {
                background-color: #0d1a5b;
                border-color: #0d1a5b;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-card">
                <h1 class="h3 mb-3 fw-normal">Iniciar Sesión</h1>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        <label for="email">Correo Electrónico</label>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                        <label for="password">Contraseña</label>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                Mantener sesión iniciada
                            </label>
                        </div>
                    </div>

                    <button class="w-100 btn btn-lg btn-primary" type="submit">
                        <i class="material-icons">lock_open</i> Iniciar Sesión
                    </button>
                </form>

                <div class="text-center mt-3">
                    <a href="{{ route('register') }}" class="text-decoration-none">
                        ¿No tienes cuenta? Regístrate
                    </a>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
