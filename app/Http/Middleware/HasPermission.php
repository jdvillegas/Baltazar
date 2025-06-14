<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;

class HasPermission extends PermissionMiddleware
{
    protected $roleOrPermission = 'permission';

    public function handle($request, Closure $next, $permission, $guard = null)
    {
        try {
            $this->authorize($permission, $guard);
        } catch (\Exception $e) {
            \Log::error('Error en middleware HasPermission: ' . $e->getMessage());
            abort(500, 'Error interno del servidor');
        }

        return $next($request);
    }
}
