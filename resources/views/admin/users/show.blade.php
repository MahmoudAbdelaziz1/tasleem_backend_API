{{-- resources/views/admin/users/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'User Details: ' . $user->name)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-user-circle"></i> User Details: {{ $user->name }}
        </h6>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Basic User Information -->
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                @if($user->user_photo)
                    <img src="{{ $user->user_photo }}" 
                         alt="{{ $user->name }}" 
                         class="img-fluid rounded-circle"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd;">
                @else
                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                         style="width: 150px; height: 150px;">
                        <i class="fas fa-user fa-5x text-white"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th width="35%">Name:</th><td><strong>{{ $user->name }}</strong></td></tr>
                            <tr><th>Email:</th><td>{{ $user->email }}</td></tr>
                            <tr><th>Phone:</th><td>{{ $user->phone ?? '—' }}</td></tr>
                            <tr><th>Role:</th>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'primary' }} fs-6">
                                        <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-user' }}"></i>
                                        {{ $user->role == 'admin' ? 'Admin' : 'User' }}
                                    </span>
                                 </td>
                            </tr>
                            <tr><th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ $user->status == '1' ? 'success' : 'danger' }} fs-6">
                                        <i class="fas {{ $user->status == '1' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $user->status == '1' ? 'Active' : 'Inactive' }}
                                    </span>
                                 </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr><th>City:</th><td>{{ $user->city ?? '—' }}</td></tr>
                            <tr><th>Address:</th><td>{{ $user->address ?? '—' }}</td></tr>
                            <tr><th>Postal Code:</th><td>{{ $user->post_code ?? '—' }}</td></tr>
                            <tr><th>Registration Date:</th><td>{{ $user->created_at->format('Y-m-d h:i A') }}</td></tr>
                            <tr><th>Last Updated:</th><td>{{ $user->updated_at->format('Y-m-d h:i A') }}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-boxes fa-2x mb-2"></i>
                        <h3>{{ $user->products_count ?? 0 }}</h3>
                        <p>Products</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                        <h3>{{ $user->orders_count ?? 0 }}</h3>
                        <p>Orders</p>
                        <small>{{ $completedOrders ?? 0 }} Completed</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x mb-2"></i>
                        <h3>{{ $user->rentals_count ?? 0 }}</h3>
                        <p>Rentals</p>
                        <small>{{ $activeRentals ?? 0 }} Active</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h3>{{ $user->reviews_count ?? 0 }}</h3>
                        <p>Reviews</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales and Revenue -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h4>{{ number_format($totalSpent ?? 0, 2) }} EGP</h4>
                        <p>Total Purchases</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-hand-holding-usd fa-2x mb-2"></i>
                        <h4>{{ number_format($totalRentalSpent ?? 0, 2) }} EGP</h4>
                        <p>Total Rental Payments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        @if($user->products->count() > 0)
        <div class="mt-4">
            <h5 class="text-primary">
                <i class="fas fa-boxes"></i> User's Recent Products
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr><th>#</th><th>Product Name</th><th>Price</th><th>Type</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->products as $index => $product)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ number_format($product->price, 2) }} EGP</td>
                            <td><span class="badge bg-{{ $product->type == 'sale' ? 'success' : 'info' }}">{{ $product->type == 'sale' ? 'Sale' : 'Rent' }}</span></td>
                            <td><span class="badge bg-{{ $product->status == '1' ? 'success' : 'danger' }}">{{ $product->status == '1' ? 'Active' : 'Inactive' }}</span></td>
                            <td><a href="{{ route('admin.products.show', $product) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Recent Orders -->
        @if($user->orders->count() > 0)
        <div class="mt-4">
            <h5 class="text-success">
                <i class="fas fa-shopping-cart"></i> User's Recent Orders
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr><th>#</th><th>Order Number</th><th>Product</th><th>Quantity</th><th>Total</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        @foreach($user->orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>#{{ $order->order_id }}</td>
                            <td>{{ $order->product->name ?? '—' }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->total_price, 2) }} EGP</td>
                            <td><span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">{{ $order->status }}</span></td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
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