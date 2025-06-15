@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('View Ticket') }} #{{ $support->id }}</span>
                    <div>
                        <a href="{{ route('admin.support.edit', $support) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> {{ __('Edit') }}
                        </a>
                        <a href="{{ route('admin.support.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to List') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>{{ $support->subject }}</h5>
                            <p class="text-muted">
                                {{ $support->description }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>{{ __('Status') }}:</strong>
                                        @php
                                            $statusClasses = [
                                                'open' => 'badge bg-primary',
                                                'in_progress' => 'badge bg-info',
                                                'resolved' => 'badge bg-success',
                                                'closed' => 'badge bg-secondary',
                                            ];
                                        @endphp
                                        <span class="{{ $statusClasses[$support->status] ?? 'badge bg-secondary' }}">
                                            {{ str_replace('_', ' ', ucfirst($support->status)) }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('Priority') }}:</strong>
                                        @php
                                            $priorityClasses = [
                                                'low' => 'badge bg-info',
                                                'medium' => 'badge bg-warning',
                                                'high' => 'badge bg-danger',
                                            ];
                                        @endphp
                                        <span class="{{ $priorityClasses[$support->priority] ?? 'badge bg-secondary' }}">
                                            {{ ucfirst($support->priority) }}
                                        </span>
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('Created By') }}:</strong>
                                        {{ $support->user->name }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('Assigned To') }}:</strong>
                                        {{ $support->assignee->name ?? 'Unassigned' }}
                                    </div>
                                    <div class="mb-3">
                                        <strong>{{ __('Created At') }}:</strong>
                                        {{ $support->created_at->format('Y-m-d H:i') }}
                                    </div>
                                    @if($support->resolved_at)
                                        <div class="mb-3">
                                            <strong>{{ __('Resolved At') }}:</strong>
                                            {{ $support->resolved_at->format('Y-m-d H:i') }}
                                        </div>
                                        <div class="mb-3">
                                            <strong>{{ __('Resolved By') }}:</strong>
                                            {{ $support->resolver->name ?? 'N/A' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($support->resolution)
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>{{ __('Resolution') }}</strong>
                            </div>
                            <div class="card-body">
                                {{ $support->resolution }}
                            </div>
                        </div>
                    @endif

                    @if($support->status !== 'resolved' && $support->status !== 'closed')
                        <div class="card">
                            <div class="card-header">
                                <strong>{{ __('Mark as Resolved') }}</strong>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.support.resolve', $support) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="resolution" class="form-label">{{ __('Resolution Details') }}</label>
                                        <textarea class="form-control @error('resolution') is-invalid @enderror" 
                                                  id="resolution" 
                                                  name="resolution" 
                                                  rows="3" 
                                                  required>{{ old('resolution') }}</textarea>
                                        @error('resolution')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> {{ __('Mark as Resolved') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
