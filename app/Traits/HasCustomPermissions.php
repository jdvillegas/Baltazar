<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionDeniedException extends \Exception
{
    public function render(Request $request): Response
    {
        return response()->json([
            'message' => 'No tienes permisos para realizar esta acción.',
            'status' => 'error'
        ], 403);
    }
}

trait HasCustomPermissions
{
    protected function authorizePermission(string $permission): void
    {
        if (!auth()->check()) {
            throw new PermissionDeniedException('Debes estar autenticado para realizar esta acción.');
        }

        if (!auth()->user()->can($permission)) {
            throw new PermissionDeniedException('No tienes permisos para realizar esta acción.');
        }
    }
}
