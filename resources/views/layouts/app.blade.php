<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Baltazar' }} - Tu Aliado Estratégico</title>
        <!-- SweetAlert2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
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
        </style>
    </head>
    <body>
        <div class="sidebar">
            <div class="sidebar-header">
                <h3 class="text-center mb-4">Baltazar</h3>
            </div>
            <nav class="nav flex-column">
                <!-- Dashboard -->
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="material-icons">dashboard</i>
                    Dashboard
                </a>

                <!-- Casos -->
                <a class="nav-link {{ request()->routeIs('cases.*') ? 'active' : '' }}" href="{{ route('cases.index') }}">
                    <i class="material-icons">folder</i>
                    Casos
                </a>

                <!-- Configuración -->
                <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}">
                    <i class="material-icons">settings</i>
                    Configuración
                </a>

                <!-- Opciones de Administración (solo para administradores) -->
                @if(auth()->check() && auth()->user()->hasRole('admin'))
                    <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                        <i class="material-icons">people</i>
                        Gestión de Usuarios
                    </a>
                    <a class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}" href="{{ route('notifications.index') }}">
                        <i class="material-icons">notifications</i>
                        Notificaciones
                    </a>
                    <a class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}" href="{{ route('admin.support.index') }}">
                        <i class="material-icons">support_agent</i>
                        Soporte
                    </a>
                @endif
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

            <main>
                @yield('content')
            </main>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- Custom Scripts -->
        @stack('scripts')
    </body>
</html>
