{{-- resources/views/admin/orders/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Order Details #' . $order->order_id)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-shopping-cart"></i> Order Details #{{ $order->order_id }}
        </h6>
        <div>
            <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-secondary btn-sm" target="_blank">
                <i class="fas fa-print"></i> Print
            </a>
            <a href="{{ route('admin.orders.invoice', $order) }}" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-file-invoice"></i> Invoice
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Order Status -->
        <div class="alert alert-info mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>Current Order Status:</strong>
                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : ($order->status == 'cancelled' ? 'danger' : 'info')) }} fs-6 ms-2">
                        @if($order->status == 'pending') ⏳ Pending
                        @elseif($order->status == 'confirmed') ✅ Confirmed
                        @elseif($order->status == 'shipped') 🚚 Shipped
                        @elseif($order->status == 'delivered') 📦 Delivered
                        @elseif($order->status == 'cancelled') ❌ Cancelled
                        @endif
                    </span>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-flex gap-2 justify-content-end">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select w-auto">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Order Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle"></i> Order Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr><th width="40%">Order Number:</th><td><strong>#{{ $order->order_id }}</strong></td></tr>
                            <tr><th>Order Date:</th><td>{{ $order->created_at->format('Y-m-d h:i A') }}</td></tr>
                            <tr><th>Last Updated:</th><td>{{ $order->updated_at->format('Y-m-d h:i A') }}</td></tr>
                            <tr><th>Payment Status:</th>
                                <td>
                                    @if($order->payment)
                                        <span class="badge bg-{{ $order->payment->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ $order->payment->status == 'completed' ? 'Paid' : 'Pending' }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger">Not Paid</span>
                                    @endif
                                 </td>
                            </tr>
                            <tr><th>Payment Method:</th><td>{{ $order->payment->payment_method ?? '—' }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user"></i> Customer Information
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($order->user && $order->user->user_photo)
                                <img src="{{ $order->user->user_photo }}" 
                                     class="rounded-circle me-3" 
                                     width="60" height="60"
                                     style="object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-0">{{ $order->user->name ?? 'Not specified' }}</h5>
                                <p class="mb-0 text-muted">{{ $order->user->email ?? '' }}</p>
                                <p class="mb-0"><i class="fas fa-phone"></i> {{ $order->user->phone ?? '—' }}</p>
                            </div>
                        </div>
                        <hr>
                        <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> {{ $order->user->address ?? '—' }}</p>
                        <p><i class="fas fa-city"></i> <strong>City:</strong> {{ $order->user->city ?? '—' }}</p>
                        <a href="{{ route('admin.users.show', $order->user) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> View Customer Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Product Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-box"></i> Product Information
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            @if($order->product && $order->product->primary_image)
                                <img src="{{ $order->product->primary_image }}" 
                                     class="rounded me-3" 
                                     width="100" height="100"
                                     style="object-fit: cover;">
                            @endif
                            <div>
                                <h5>{{ $order->product->name ?? 'Not specified' }}</h5>
                                <p class="text-muted">{{ Str::limit($order->product->description ?? '', 100) }}</p>
                                <p><i class="fas fa-tag"></i> Price: {{ number_format($order->unit_price, 2) }} EGP</p>
                                <p><i class="fas fa-store"></i> Seller: {{ $order->product->owner->name ?? '—' }}</p>
                                <a href="{{ route('admin.products.show', $order->product) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View Product
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Information -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <i class="fas fa-truck"></i> Shipping Information
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Quantity Ordered:</th>
                                <td>{{ $order->quantity }} unit(s)</td>
                            </tr>
                            <tr>
                                <th>Unit Price:</th>
                                <td>{{ number_format($order->unit_price, 2) }} EGP</td>
                            </tr>
                            <tr>
                                <th>Subtotal:</th>
                                <td>{{ number_format($order->quantity * $order->unit_price, 2) }} EGP</td>
                            </tr>
                            <tr>
                                <th>Delivery Fee:</th>
                                <td>0.00 EGP</td>
                            </tr>
                            <tr class="table-active">
                                <th><strong>Grand Total:</strong></th>
                                <td><strong class="text-success h5">{{ number_format($order->total_price, 2) }} EGP</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previous Orders from Same Customer -->
        @if($userOrders && $userOrders->count() > 0)
        <div class="mt-4">
            <h5 class="text-primary">
                <i class="fas fa-history"></i> Previous Orders from Same Customer
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Order Number</th>
                            <th>Product</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userOrders as $prevOrder)
                        <tr>
                            <td>#{{ $prevOrder->order_id }}</td>
                            <td>{{ Str::limit($prevOrder->product->name ?? '—', 30) }}</td>
                            <td>{{ number_format($prevOrder->total_price, 2) }} EGP</td>
                            <td>
                                <span class="badge bg-{{ $prevOrder->status == 'delivered' ? 'success' : 'warning' }}">
                                    {{ $prevOrder->status }}
                                </span>
                              </td>
                            <td>{{ $prevOrder->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $prevOrder) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                              </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection