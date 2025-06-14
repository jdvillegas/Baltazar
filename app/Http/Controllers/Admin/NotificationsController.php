<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationsController extends Controller
{
    public function index()
    {
        try {
            $notifications = Notification::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('admin.notifications.index', compact('notifications'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar las notificaciones: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::where('id', '!=', auth()->id())
                ->get();
            
            return view('admin.notifications.create', compact('users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la información: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'users' => 'required|array',
                'users.*' => 'integer|exists:users,id',
                'send_now' => 'boolean'
            ]);

            // Crear la notificación
            $notification = Notification::create([
                'title' => $validated['title'],
                'message' => $validated['message'],
                'sender_id' => auth()->id()
            ]);

            // Asignar la notificación a los usuarios seleccionados
            foreach ($validated['users'] as $userId) {
                $notification->users()->attach($userId, [
                    'read_at' => $validated['send_now'] ? now() : null
                ]);
            }

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notificación enviada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al enviar la notificación: ' . $e->getMessage());
        }
    }

    public function show(Notification $notification)
    {
        try {
            $notification->markAsRead(auth()->user());
            return view('admin.notifications.show', compact('notification'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al mostrar la notificación: ' . $e->getMessage());
        }
    }

    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'notification_id' => 'required|exists:notifications,id',
                'user_id' => 'required|exists:users,id',
            ]);

            $notification = Notification::findOrFail($validated['notification_id']);
            $user = User::findOrFail($validated['user_id']);

            $notification->users()->updateExistingPivot($user->id, [
                'read_at' => now()
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notificación marcada como leída.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al marcar la notificación como leída: ' . $e->getMessage());
        }
    }

    public function resend(Notification $notification)
    {
        try {
            $notification->users()->updateExistingPivot(auth()->id(), [
                'read_at' => null
            ]);

            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notificación reenviada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al reenviar la notificación: ' . $e->getMessage());
        }
    }
}
