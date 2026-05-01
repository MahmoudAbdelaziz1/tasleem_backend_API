{{-- resources/views/admin/logs/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Activity Logs')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history"></i> Activity Logs
        </h6>
        <div>
            <a href="{{ route('admin.logs.stats') }}" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> Statistics
            </a>
            <a href="{{ route('admin.logs.export') }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#clearModal">
                <i class="fas fa-trash-alt"></i> Clear Old Logs
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['total'] ?? 0) }}</h5>
                        <small>Total Records</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['today'] ?? 0) }}</h5>
                        <small>Today</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['week'] ?? 0) }}</h5>
                        <small>This Week</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['month'] ?? 0) }}</h5>
                        <small>This Month</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['success'] ?? 0) }}</h5>
                        <small>Success</small>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5>{{ number_format($stats['failed'] ?? 0) }}</h5>
                        <small>Failed</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Logs
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.logs.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Module</label>
                        <select name="module" class="form-control">
                            <option value="">All</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}" {{ request('module') == $module ? 'selected' : '' }}>
                                    {{ ucfirst($module) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Action Type</label>
                        <select name="action_type" class="form-control">
                            <option value="">All</option>
                            @foreach($actionTypes as $type)
                                <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="success" {{ request('status') == 'success' ? 'selected' : '' }}>Success</option>
                            <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <select name="user_id" class="form-control">
                            <option value="">All</option>
                            @foreach($users ?? [] as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="logsTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th>User</th>
                        <th>Action Type</th>
                        <th>Action Name</th>
                        <th>Module</th>
                        <th>Entity Type</th>
                        <th>Status</th>
                        <th>IP Address</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th width="80">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $index => $log)
                    <tr>
                        <td class="text-center">{{ $logs->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($log->user && $log->user->user_photo)
                                    <img src="{{ $log->user->user_photo }}" 
                                         class="rounded-circle me-2" 
                                         width="30" height="30"
                                         style="object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                         style="width: 30px; height: 30px;">
                                        <i class="fas fa-user fa-xs text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $log->user->name ?? 'Not registered' }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $log->user_id ?? '—' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $log->action_type == 'create' ? 'success' : ($log->action_type == 'update' ? 'warning' : ($log->action_type == 'delete' ? 'danger' : 'info')) }}">
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
                        <td>
                            <code>{{ $log->action_name }}</code>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $log->module ?? '—' }}</span>
                        </td>
                        <td>
                            @if($log->entity_type)
                                <span class="badge bg-info">{{ $log->entity_type }}</span>
                                <br>
                                <small>ID: {{ $log->entity_id }}</small>
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $log->status == 'success' ? 'success' : 'danger' }}">
                                <i class="fas {{ $log->status == 'success' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $log->status == 'success' ? 'Success' : 'Failed' }}
                            </span>
                        </td>
                        <td>
                            <code>{{ $log->ip_address ?? '—' }}</code>
                        </td>
                        <td>
                            <small>{{ Str::limit($log->message ?? '—', 50) }}</small>
                        </td>
                        <td>
                            <small>{{ $log->created_at->format('Y-m-d') }}</small>
                            <br>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.logs.show', $log) }}" class="btn btn-sm btn-info" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Clear Logs Modal -->
<div class="modal fade" id="clearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Clear Old Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.logs.clear') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Warning: This action cannot be undone!
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Delete logs older than (days)</label>
                        <input type="number" name="days" class="form-control" value="90" min="1" max="365" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module (Optional)</label>
                        <select name="module" class="form-control">
                            <option value="">All Modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module }}">{{ ucfirst($module) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#logsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        searching: false,
        paging: false,
        info: false
    });
});
</script>
@endpush