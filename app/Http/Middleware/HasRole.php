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

            if (!auth()->user()->hasRole($role)) {
                throw UnauthorizedException::forRoles([$role]);
            }

            return $next($request);
        } catch (UnauthorizedException $e) {
            return response()->view('errors.403', [], 403);
        }
    }
}
