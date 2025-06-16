<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupportTicket;
use App\Models\User;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = SupportTicket::with(['user', 'resolver'])->latest()->paginate(10);
        return view('admin.support.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['admin', 'agent']);
        })->get();
        
        return view('admin.support.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'open';

        SupportTicket::create($validated);

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupportTicket $support)
    {
        $support->load(['user', 'resolver']);
        return view('admin.support.show', compact('support'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupportTicket $support)
    {
        $users = User::all();
        return view('admin.support.edit', compact('support', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupportTicket $support)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $support->update($validated);

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupportTicket $support)
    {
        $support->delete();

        return redirect()->route('admin.support.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }

    /**
     * Mark a ticket as resolved.
     */
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
