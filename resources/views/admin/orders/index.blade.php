{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Order Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-shopping-cart"></i> Orders List
        </h6>
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
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Orders</h6>
                                <h3 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Pending</h6>
                                <h3 class="mb-0">{{ number_format($stats['pending'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Delivered</h6>
                                <h3 class="mb-0">{{ number_format($stats['delivered'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Cancelled</h6>
                                <h3 class="mb-0">{{ number_format($stats['cancelled'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-times-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> Filter Orders
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Order Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="ordersTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>#</th>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $index => $order)
                    <tr>
                        <td class="text-center">
                            <input type="checkbox" class="order-checkbox" value="{{ $order->order_id }}">
                        </td>
                        <td class="text-center">{{ $orders->firstItem() + $index }}</td>
                        <td>
                            <span class="badge bg-dark">#{{ $order->order_id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->user && $order->user->user_photo)
                                    <img src="{{ $order->user->user_photo }}" 
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
                                    <strong>{{ $order->user->name ?? 'Not specified' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $order->user->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($order->product && $order->product->primary_image)
                                    <img src="{{ $order->product->primary_image }}" 
                                         class="rounded me-2" 
                                         width="40" height="40"
                                         style="object-fit: cover;">
                                @endif
                                <div>
                                    <strong>{{ Str::limit($order->product->name ?? 'Not specified', 30) }}</strong>
                                    <br>
                                    <small class="text-muted">By: {{ $order->product->owner->name ?? '—' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">{{ $order->quantity }}</td>
                        <td class="text-center">{{ number_format($order->unit_price, 2) }} EGP</td>
                        <td class="text-center">
                            <strong class="text-primary">{{ number_format($order->total_price, 2) }} EGP</strong>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }} class="bg-warning">⏳ Pending</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }} class="bg-info">✅ Confirmed</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }} class="bg-primary">🚚 Shipped</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }} class="bg-success">📦 Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }} class="bg-danger">❌ Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <small>{{ $order->created_at->format('Y-m-d') }}</small>
                            <br>
                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order) }}" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.orders.print', $order) }}" 
                                   class="btn btn-sm btn-secondary" title="Print" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('admin.orders.invoice', $order) }}" 
                                   class="btn btn-sm btn-success" title="Invoice" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>
                            </div>
                         </td>
                     </tr>
                    @endforeach
                </tbody>
             </table>
        </div>
        
        <!-- Bulk Actions -->
        <div class="row mt-3 align-items-center">
            <div class="col-md-6">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Select All
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item select-all-btn" href="#">Select All</a></li>
                        <li><a class="dropdown-item deselect-all-btn" href="#">Deselect All</a></li>
                    </ul>
                </div>
                
                <div class="btn-group ms-2">
                    <button type="button" class="btn btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                        Update Status for Selected
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item bulk-status" data-status="pending" href="#">Pending</a></li>
                        <li><a class="dropdown-item bulk-status" data-status="confirmed" href="#">Confirmed</a></li>
                        <li><a class="dropdown-item bulk-status" data-status="shipped" href="#">Shipped</a></li>
                        <li><a class="dropdown-item bulk-status" data-status="delivered" href="#">Delivered</a></li>
                        <li><a class="dropdown-item bulk-status" data-status="cancelled" href="#">Cancelled</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <strong>Total Revenue: </strong>
                <span class="text-success h5">{{ number_format($stats['total_revenue'] ?? 0, 2) }} EGP</span>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>

<!-- Bulk Update Form -->
<form id="bulkUpdateForm" action="{{ route('admin.orders.bulk-update-status') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="order_ids" id="bulkOrderIds">
    <input type="hidden" name="status" id="bulkStatus">
</form>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    // Select All functionality
    $('#selectAll').change(function() {
        $('.order-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    $('.select-all-btn').click(function(e) {
        e.preventDefault();
        $('.order-checkbox').prop('checked', true);
        $('#selectAll').prop('checked', true);
    });
    
    $('.deselect-all-btn').click(function(e) {
        e.preventDefault();
        $('.order-checkbox').prop('checked', false);
        $('#selectAll').prop('checked', false);
    });
    
    // Bulk status update
    $('.bulk-status').click(function(e) {
        e.preventDefault();
        var selectedOrders = [];
        $('.order-checkbox:checked').each(function() {
            selectedOrders.push($(this).val());
        });
        
        if (selectedOrders.length === 0) {
            alert('Please select at least one order');
            return;
        }
        
        if (confirm('Are you sure you want to update the status of ' + selectedOrders.length + ' order(s)?')) {
            $('#bulkOrderIds').val(selectedOrders.join(','));
            $('#bulkStatus').val($(this).data('status'));
            $('#bulkUpdateForm').submit();
        }
    });
});
</script>
@endpush