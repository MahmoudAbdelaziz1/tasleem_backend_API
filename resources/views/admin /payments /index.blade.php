{{-- resources/views/admin/payments/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Payments Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-credit-card"></i> Payments List
        </h6>
        <span class="badge badge-primary badge-pill">{{ $payments->total() }} Total</span>
    </div>
    <div class="card-body">
        
        <!-- Search & Filters -->
        <div class="row mb-3">
            <div class="col-md-4">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Search by transaction ID..." 
                               value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-8 text-right">
                <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" 
                   class="btn btn-success btn-sm {{ request('status') == 'completed' ? 'active' : '' }}">
                    Completed
                </a>
                <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" 
                   class="btn btn-warning btn-sm {{ request('status') == 'pending' ? 'active' : '' }}">
                    Pending
                </a>
                <a href="{{ route('admin.payments.index', ['status' => 'failed']) }}" 
                   class="btn btn-danger btn-sm {{ request('status') == 'failed' ? 'active' : '' }}">
                    Failed
                </a>
                <a href="{{ route('admin.payments.index') }}" 
                   class="btn btn-secondary btn-sm {{ !request('status') ? 'active' : '' }}">
                    All
                </a>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Transaction ID</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_id }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $payment->user) }}" class="text-decoration-none">
                                    {{ $payment->user->name ?? 'N/A' }}
                                </a>
                                <br>
                                <small class="text-muted">{{ $payment->user->email ?? '' }}</small>
                            </td>
                            <td>
                                @if($payment->order_id)
                                    <span class="badge badge-info">Order #{{ $payment->order_id }}</span>
                                @elseif($payment->rental_id)
                                    <span class="badge badge-success">Rental #{{ $payment->rental_id }}</span>
                                @else
                                    <span class="badge badge-secondary">N/A</span>
                                @endif
                            </td>
                            <td class="font-weight-bold text-primary">
                                {{ number_format($payment->amount, 2) }} EGP
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'completed' => 'success',
                                        'pending' => 'warning', 
                                        'failed' => 'danger',
                                        'refunded' => 'secondary'
                                    ];
                                    $color = $statusColors[$payment->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-monospace">
                                    {{ Str::limit($payment->transaction_id, 15) ?? 'N/A' }}
                                </small>
                            </td>
                            <td>{{ $payment->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.payments.show', $payment) }}" 
                                       class="btn btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                No payments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $payments->withQueryString()->links() }}
        </div>

    </div>
</div>
@endsection

@push('styles')
<style>
    .badge-pill {
        padding: 0.5em 1em;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush
