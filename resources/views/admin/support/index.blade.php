@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Support Tickets') }}</span>
                    <a href="{{ route('admin.support.create') }}" class="btn btn-primary btn-sm">
                        {{ __('Create New Ticket') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Created By') }}</th>
                                    <th>{{ __('Priority') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.support.show', $ticket) }}">
                                                {{ $ticket->subject }}
                                            </a>
                                        </td>
                                        <td>{{ $ticket->user->name }}</td>
                                        <td>
                                            @php
                                                $priorityClasses = [
                                                    'low' => 'badge bg-info',
                                                    'medium' => 'badge bg-warning',
                                                    'high' => 'badge bg-danger',
                                                ];
                                            @endphp
                                            <span class="{{ $priorityClasses[$ticket->priority] ?? 'badge bg-secondary' }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'open' => 'badge bg-primary',
                                                    'in_progress' => 'badge bg-info',
                                                    'resolved' => 'badge bg-success',
                                                    'closed' => 'badge bg-secondary',
                                                ];
                                            @endphp
                                            <span class="{{ $statusClasses[$ticket->status] ?? 'badge bg-secondary' }}">
                                                {{ str_replace('_', ' ', ucfirst($ticket->status)) }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.support.show', $ticket) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.support.edit', $ticket) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.support.destroy', $ticket) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">{{ __('No tickets found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
