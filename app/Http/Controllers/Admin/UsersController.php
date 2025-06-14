<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Added the User model

class UsersController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['roles', 'cases' => function($query) {
                $query->where('status', 'pendiente')
                     ->orWhere('status', 'en_proceso')
                     ->orWhere('status', 'anulado');
            }])->paginate(10);

            return view('admin.users.index', compact('users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar los usuarios: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes editar tu propio usuario.');
        }

        try {
            $roles = \App\Models\Role::all();
            return view('admin.users.edit', compact('user', 'roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la información del usuario: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'No puedes editar tu propio usuario.');
        }

        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'email' => 'email|unique:users,email,' . $user->id,
                'membership_type' => 'in:free,premium',
                'max_open_cases' => 'integer|min:1',
                'status' => 'in:active,inactive',
                'roles' => 'array',
                'roles.*' => 'string|exists:roles,name'
            ]);

            // Actualizar información básica
            $user->update($validated);

            // Actualizar roles
            $roles = \App\Models\Role::whereIn('name', $validated['roles'])->get();
            $user->syncRoles($roles);

            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function inactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes inactivar tu propio usuario.');
        }

        try {
            $user->update(['status' => 'inactive']);
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario inactivado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al inactivar el usuario: ' . $e->getMessage());
        }
    }

    public function activate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes activar tu propio usuario.');
        }

        try {
            $user->update(['status' => 'active']);
            return redirect()->route('admin.users.index')
                ->with('success', 'Usuario activado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al activar el usuario: ' . $e->getMessage());
        }
    }
}
