{{-- resources/views/admin/logs/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Log Details #' . $log->log_id)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history"></i> Log Details #{{ $log->log_id }}
        </h6>
        <div>
            @if($previousLog)
                <a href="{{ route('admin.logs.show', $previousLog) }}" class="btn btn-secondary btn-sm" title="Previous Log">
                    <i class="fas fa-arrow-right"></i> Previous
                </a>
            @endif
            @if($nextLog)
                <a href="{{ route('admin.logs.show', $nextLog) }}" class="btn btn-secondary btn-sm" title="Next Log">
                    Next <i class="fas fa-arrow-left"></i>
                </a>
            @endif
            <a href="{{ route('admin.logs.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle"></i> Basic Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr><th width="35%">Log ID:</th><td><strong>#{{ $log->log_id }}</strong></td></tr>
                            <tr><th>Action Type:</th>
                                <td>
                                    <span class="badge bg-{{ $log->action_type == 'create' ? 'success' : ($log->action_type == 'update' ? 'warning' : ($log->action_type == 'delete' ? 'danger' : 'info')) }} fs-6">
                                        <i class="fas 
                                            {{ $log->action_type == 'create' ? 'fa-plus' : '' }}
                                            {{ $log->action_type == 'update' ? 'fa-edit' : '' }}
                                            {{ $log->action_type == 'delete' ? 'fa-trash' : '' }}
                                            {{ $log->action_type == 'view' ? 'fa-eye' : '' }}
                                            {{ $log->action_type == 'login' ? 'fa-sign-in-alt' : '' }}
                                        "></i>
                                        {{ ucfirst($log->action_type) }}
                                    </span>
                                  </td>
                            </tr>
                            <tr><th>Action Name:</th><td><code>{{ $log->action_name }}</code></td></tr>
                            <tr><th>Module:</th><td><span class="badge bg-secondary">{{ $log->module ?? '—' }}</span></td></tr>
                            <tr><th>Entity Type:</th><td>{{ $log->entity_type ?? '—' }}</td></tr>
                            <tr><th>Entity ID:</th><td>{{ $log->entity_id ?? '—' }}</td></tr>
                            <tr><th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ $log->status == 'success' ? 'success' : 'danger' }} fs-6">
                                        <i class="fas {{ $log->status == 'success' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $log->status == 'success' ? 'Success' : 'Failed' }}
                                    </span>
                                  </td>
                            </tr>
                            <tr><th>Error Code:</th><td>{{ $log->error_code ?? '—' }}</td></tr>
                            <tr><th>Date:</th><td>{{ $log->created_at->format('Y-m-d h:i:s A') }}</td></tr>
                            <tr><th>Last Updated:</th><td>{{ $log->updated_at->format('Y-m-d h:i:s A') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- User and Device Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user"></i> User and Device Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="35%">User:</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($log->user && $log->user->user_photo)
                                            <img src="{{ $log->user->user_photo }}" 
                                                 class="rounded-circle me-2" 
                                                 width="40" height="40"
                                                 style="object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $log->user->name ?? 'Not registered' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                  </td>
                            </tr>
                            <tr><th>User ID:</th><td>{{ $log->user_id ?? '—' }}</td></tr>
                            <tr><th>IP Address:</th><td><code>{{ $log->ip_address ?? '—' }}</code></td></tr>
                            <tr><th>OS/Browser:</th>
                                <td>
                                    <small>{{ Str::limit($log->user_agent ?? '—', 100) }}</small>
                                 </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Message -->
                @if($log->message)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-comment"></i> Message
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $log->message }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Old and New Data -->
        @if($log->old_data || $log->new_data)
        <div class="row">
            @if($log->old_data)
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <i class="fas fa-database"></i> Old Data
                    </div>
                    <div class="card-body">
                        <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;">
                            <code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </pre>
                    </div>
                </div>
            </div>
            @endif

            @if($log->new_data)
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-database"></i> New Data
                    </div>
                    <div class="card-body">
                        <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;">
                            <code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </pre>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Summary of Changes -->
        @if($log->old_data && $log->new_data)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-exchange-alt"></i> Summary of Changes
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr><th>Field</th><th>Old Value</th><th>New Value</th></tr>
                        </thead>
                        <tbody>
                            @foreach($log->new_data as $key => $value)
                                @php
                                    $oldValue = $log->old_data[$key] ?? null;
                                @endphp
                                @if($oldValue != $value)
                                <tr class="{{ $oldValue === null ? 'table-warning' : '' }}">
                                    <td><strong>{{ $key }}</strong></td>
                                    <td>
                                        @if($oldValue === null)
                                            <span class="badge bg-success">New</span>
                                        @else
                                            {{ is_array($oldValue) ? json_encode($oldValue) : $oldValue }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($value === null)
                                            <span class="badge bg-danger">Deleted</span>
                                        @else
                                            {{ is_array($value) ? json_encode($value) : $value }}
                                        @endif
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- JSON Raw Data -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-code"></i> Raw Data (JSON)
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="jsonTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="full-tab" data-bs-toggle="tab" data-bs-target="#full" type="button" role="tab">
                            Full Log
                        </button>
                    </li>
                    @if($log->old_data)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="old-tab" data-bs-toggle="tab" data-bs-target="#old" type="button" role="tab">
                            Old Data
                        </button>
                    </li>
                    @endif
                    @if($log->new_data)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new" type="button" role="tab">
                            New Data
                        </button>
                    </li>
                    @endif
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="full" role="tabpanel">
                        <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; max-height: 400px;">
                            <code>{{ json_encode($log->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </pre>
                    </div>
                    @if($log->old_data)
                    <div class="tab-pane fade" id="old" role="tabpanel">
                        <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; max-height: 400px;">
                            <code>{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </pre>
                    </div>
                    @endif
                    @if($log->new_data)
                    <div class="tab-pane fade" id="new" role="tabpanel">
                        <pre style="background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; max-height: 400px;">
                            <code>{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code>
                        </pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }
    code {
        color: #d63384;
    }
    .table-borderless td, .table-borderless th {
        padding: 8px 0;
    }
</style>
@endpush