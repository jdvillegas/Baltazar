<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Baltazar</title>
        
        <!-- Material Design Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Custom CSS -->
        <style>
            .sidebar {
                min-height: 100vh;
                background-color: #1a237e;
                color: white;
                padding-top: 20px;
                width: 250px;
                position: fixed;
                left: 0;
                top: 0;
                z-index: 1000;
            }
            .sidebar .nav-link {
                color: rgba(255, 255, 255, 0.7);
                padding: 12px 20px;
                border-radius: 4px;
                margin: 4px 0;
                transition: all 0.3s ease;
            }
            .sidebar .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1);
                color: white;
            }
            .sidebar .nav-link.active {
                background-color: rgba(255, 255, 255, 0.2);
                color: white;
            }
            .sidebar .nav-link i {
                margin-right: 10px;
            }
            .main-content {
                margin-left: 250px;
                padding: 20px;
                min-height: 100vh;
            }
            .top-bar {
                background-color: white;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 15px 20px;
                margin-bottom: 20px;
            }
            .top-bar .logo {
                font-size: 20px;
                font-weight: 600;
                color: #1a237e;
            }
            .top-bar .logo span {
                color: #42a5f5;
            }
            .welcome-message {
                margin-bottom: 30px;
            }
            .stats-card {
                background-color: white;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }
            .stats-card h3 {
                color: #1a237e;
                margin-bottom: 10px;
            }
            .stats-card p {
                color: #666;
                margin-bottom: 0;
            }
        </style>
    </head>
    <body>
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 class="text-center mb-4">Baltazar</h3>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link active" href="{{ route('dashboard') }}">
                    <i class="material-icons">dashboard</i>
                    Dashboard
                </a>
                <a class="nav-link" href="{{ route('cases.index') }}">
                    <i class="material-icons">folder</i>
                    Casos
                </a>
                <a class="nav-link" href="{{ route('settings.index') }}">
                    <i class="material-icons">settings</i>
                    Configuración
                </a>
            </nav>
        </div>

        <div class="main-content">
            <div class="top-bar">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="logo">
                        <i class="material-icons">account_circle</i>
                        <span>Baltazar</span> Tu Aliado Estratégico
                        <small>By Bitbros</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="material-icons">notifications</i>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-dark dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="material-icons">account_circle</i>
                                {{ Auth::user()->name }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#">Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="welcome-message">
                <h2>Bienvenido, {{ Auth::user()->name }}</h2>
                <p>¡Bienvenido a tu panel de control!</p>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3>Membresía</h3>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Días Restantes en Trial:</span>
                                        <span class="badge bg-{{ $daysColor }}">
                                            {{ $daysRemaining }} días
                                        </span>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-{{ $daysColor }}" 
                                             role="progressbar" 
                                             style="width: {{ ($daysRemaining / 90) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3>Casos Abiertos</h3>
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>Casos Activos:</span>
                                        <span class="badge bg-primary">
                                            {{ $openCases }}/{{ $maxOpenCases }}
                                        </span>
                                    </div>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-primary" 
                                             role="progressbar" 
                                             style="width: {{ ($openCases / $maxOpenCases) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3>Estadísticas Generales</h3>
                        <p>Resumen de tu actividad</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3>Casos Recientes</h3>
                        <p>Últimos casos registrados</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3>Notificaciones</h3>
                        <p>Alertas y actualizaciones</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
