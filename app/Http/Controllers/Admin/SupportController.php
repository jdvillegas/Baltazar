<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket; // Assuming SupportTicket model exists

class SupportController extends Controller
{
    public function resolve(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'resolution' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'resolved',
            'resolution' => $validated['resolution'],
            'resolved_at' => now(),
            'resolved_by' => auth()->id()
        ]);

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket resuelto exitosamente.');
    }
}
