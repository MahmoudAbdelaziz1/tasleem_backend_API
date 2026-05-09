{{-- resources/views/admin/payments/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Payment Details #{{ $payment->payment_id }}')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-receipt"></i> Payment Details #{{ $payment->payment_id }}
                </h6>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-muted">Payment Information</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Amount:</th>
                                <td class="font-weight-bold text-primary">
                                    {{ number_format($payment->amount, 2) }} EGP
                                </td>
                            </tr>
                            <tr>
                                <th>Payment Method:</th>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
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
                            </tr>
                            <tr>
                                <th>Transaction ID:</th>
                                <td class="text-monospace">{{ $payment->transaction_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $payment->created_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                            <tr>
                                <th>Updated At:</th>
                                <td>{{ $payment->updated_at->format('Y-m-d H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold text-muted">Related To</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">User:</th>
                                <td>
                                    <a href="{{ route('admin.users.show', $payment->user) }}">
                                        {{ $payment->user->name ?? 'N/A' }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $payment->user->email ?? '' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Type:</th>
                                <td>
                                    @if($payment->order_id)
                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" 
                                           class="badge badge-info">
                                            Order #{{ $payment->order_id }}
                                        </a>
                                    @elseif($payment->rental_id)
                                        <a href="{{ route('admin.rentals.show', $payment->rental_id) }}" 
                                           class="badge badge-success">
                                            Rental #{{ $payment->rental_id }}
                                        </a>
                                    @else
                                        <span class="badge badge-secondary">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.show', $payment->user) }}" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-user"></i> View User
                    </a>
                    @if($payment->order_id)
                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" 
                           class="btn btn-outline-info">
                            <i class="fas fa-shopping-cart"></i> View Order
                        </a>
                    @endif
                    @if($payment->rental_id)
                        <a href="{{ route('admin.rentals.show', $payment->rental_id) }}" 
                           class="btn btn-outline-success">
                            <i class="fas fa-calendar-check"></i> View Rental
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
