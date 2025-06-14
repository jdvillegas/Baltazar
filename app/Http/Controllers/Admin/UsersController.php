<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Added the User model

class UsersController extends Controller
{
    public function inactivate(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes inactivar tu propio usuario.');
        }

        $user->update(['status' => 'inactive']);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario inactivado exitosamente.');
    }
}
