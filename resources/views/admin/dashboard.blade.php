{{-- resources/views/admin/dashboard.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Dashboard')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm"></i> Comprehensive Report
    </a>
</div>

<!-- Statistics Cards Row 1 -->
<div class="row">
    <!-- Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_users'] ?? 0) }}</div>
                        <div class="mt-2 small">
                           
                           
                            <span class="text-info">{{ $stats['total_customers'] ?? 0 }} Customers</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">
                            Total Products
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_products'] ?? 0) }}</div>
                        <div class="mt-2 small">

                            <span class="text-danger">{{ $stats['sold_products'] ?? 0 }} Sold</span>
                            <span class="mx-2">|</span>
                            <span class="text-warning">{{ $stats['rented_products'] ?? 0 }} Rented</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">
                            Purchase Orders
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_orders'] ?? 0) }}</div>
                        <div class="mt-2 small">
                            <span class="text-warning">{{ $stats['pending_orders'] ?? 0 }} Pending</span>
                            <span class="mx-2">|</span>
                            <span class="text-success">{{ $stats['delivered_orders'] ?? 0 }} Completed</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                            Revenue
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_revenue'] ?? 0, 2) }} EGP</div>
                        <div class="mt-2 small">
                            <span class="text-primary">Today: {{ number_format($stats['today_revenue'] ?? 0, 2) }}</span>
                            <span class="mx-2">|</span>
                            <span class="text-success">Month: {{ number_format($stats['month_revenue'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards Row 2 -->
<div class="row">
    <!-- Rentals Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-secondary text-uppercase mb-1">
                            Rental Operations
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_rentals'] ?? 0) }}</div>
                        <div class="mt-2 small">
                            <span class="text-primary">{{ $stats['active_rentals'] ?? 0 }} Active</span>
                            <span class="mx-2">|</span>
                            <span class="text-success">{{ $stats['completed_rentals'] ?? 0 }} Completed</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stat-card card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                            Reviews
                        </div>
                        <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($stats['total_reviews'] ?? 0) }}</div>
                        <div class="mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($stats['avg_rating'] ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="small ms-2">({{ number_format($stats['avg_rating'] ?? 0, 1) }}/5)</span>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300 stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Users Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Users & Orders Statistics</h6>
            </div>
            <div class="card-body">
                <canvas id="usersOrdersChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Revenue Chart -->
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 fw-bold text-primary">Daily Revenue</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Popular Categories -->
<div class="row">
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Most Popular Categories</h6>
            </div>
            <div class="card-body">
                @foreach($popular_categories as $category)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span>{{ $category->name }}</span>
                            <span>{{ $category->total }} Products</span>
                        </div>
                        <div class="progress">
                            @php
                                $max = $popular_categories->max('total');
                                $percentage = $max > 0 ? ($category->total / $max) * 100 : 0;
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Top Products -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Best Selling Products</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Sales Count</th>
                                <th>Views</th>
                                <th>Rating</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top_products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->pay_count }}</td>
                                    <td>{{ number_format($product->view_count) }}</td>
                                    <td>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star fa-xs {{ $i <= round($product->rate) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        ({{ number_format($product->rate, 1) }})
                                     </td>
                                    <td>
                                        <span class="badge bg-{{ $product->status == 'available' ? 'success' : ($product->status == 'rented' ? 'warning' : 'danger') }}">
                                            {{ $product->status }}
                                        </span>
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Recent Users</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recent_users as $user)
                        <div class="list-group-item">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->user_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name) }}" 
                                     class="rounded-circle me-2" width="40" height="40">
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                    <br>
                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                </div>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'seller' ? 'warning' : 'info') }} ms-auto">
                                    {{ $user->role }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Orders -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Recent Orders</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recent_orders as $order)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">Order #{{ $order->order_id }}</h6>
                                    <small>{{ $order->user->name }}</small>
                                    <br>
                                   <small class="text-muted">
                                        {{ optional($order->created_at)->diffForHumans() ?? 'Not specified' }}
                                   </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ $order->status }}
                                    </span>
                                    <br>
                                    <small>{{ number_format($order->total_price, 2) }} EGP</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Rentals -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">Recent Rentals</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($recent_rentals as $rental)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="mb-0">{{ $rental->product->name }}</h6>
                                    <small>{{ $rental->renter->name }}</small>
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($rental->start_date)->format('Y-m-d') }} 
                                        to 
                                        {{ \Carbon\Carbon::parse($rental->end_date)->format('Y-m-d') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $rental->status == 'completed' ? 'success' : ($rental->status == 'active' ? 'primary' : ($rental->status == 'pending' ? 'warning' : 'danger')) }}">
                                        {{ $rental->status }}
                                    </span>
                                    <br>
                                    <small>{{ number_format($rental->total_price, 2) }} EGP</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Users and Orders Chart
    var ctx1 = document.getElementById('usersOrdersChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: {!! json_encode($chart_data['months'] ?? []) !!},
            datasets: [
                {
                    label: 'Users',
                    data: {!! json_encode($chart_data['users'] ?? []) !!},
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    tension: 0.3
                },
                {
                    label: 'Orders',
                    data: {!! json_encode($chart_data['orders'] ?? []) !!},
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.05)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Revenue Chart
    var ctx2 = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenue_chart['days'] ?? []) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($revenue_chart['revenue'] ?? []) !!},
                backgroundColor: '#36b9cc',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' EGP';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush