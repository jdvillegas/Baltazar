<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Exceptions\Handler;
use Spatie\Permission\Exceptions\UnauthorizedException;

class HasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        try {
            if (!auth()->check()) {
                return redirect()->route('login');
            }

            // Verificar si el usuario tiene el rol
            if (!auth()->user()->hasRole($role)) {
                // Registrar el error en el log
                \Log::error('Usuario intentó acceder sin permisos: ' . auth()->user()->email . ' intentó acceder a ' . $request->path() . ' pero no tiene el rol ' . $role);
                
                // Redirigir a la página de inicio con mensaje de error
                return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esta sección');
            }

            return $next($request);
        } catch (\Exception $e) {
            // Registrar cualquier otro error
            \Log::error('Error en middleware HasRole: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Error al verificar permisos');
        }
    }
}
