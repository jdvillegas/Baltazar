<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\User;

class NotificationsController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => 'required|exists:notifications,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $notification = Notification::findOrFail($validated['notification_id']);
        $user = User::findOrFail($validated['user_id']);

        $notification->markAsRead($user);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Notificación marcada como leída.');
    }
}
