<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Permission;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        try {
            $user = auth()->user();
            if (!$user->can($permission)) {
                abort(403, 'No tienes permisos para realizar esta acción.');
            }
        } catch (\Exception $e) {
            \Log::error('Error en middleware CheckPermission: ' . $e->getMessage());
            abort(500, 'Error interno del servidor');
        }

        return $next($request);
    }
}
