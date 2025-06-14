@if(auth()->user()->hasRole('admin'))
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->is('admin/*') ? 'active' : '' }}" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
            <i class="material-icons">admin_panel_settings</i>
            Administración
        </a>
        <ul class="dropdown-menu">
            <li>
                <a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="material-icons">people</i>
                    Gestión de Usuarios
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('admin.notifications.*') ? 'active' : '' }}" href="{{ route('admin.notifications.index') }}">
                    <i class="material-icons">notifications</i>
                    Notificaciones
                </a>
            </li>
            <li>
                <a class="dropdown-item {{ request()->routeIs('admin.support.*') ? 'active' : '' }}" href="{{ route('admin.support.index') }}">
                    <i class="material-icons">support_agent</i>
                    Soporte
                </a>
            </li>
        </ul>
    </div>
@endif
