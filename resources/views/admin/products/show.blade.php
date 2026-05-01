{{-- resources/views/admin/products/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Product Details: ' . $product->name)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Product Details: {{ $product->name }}</h6>
        <div>
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                @if($product->images->count() > 0)
                    <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($product->images as $key => $image)
                                <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                    <img src="{{ $image->image_url }}" 
                                         class="d-block w-100" 
                                         alt="{{ $image->alt_text ?? $product->name }}"
                                         style="height: 300px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                        @if($product->images->count() > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>
                @else
                    <img src="{{ asset('images/default-product.png') }}" 
                         class="img-fluid rounded" 
                         alt="{{ $product->name }}"
                         style="height: 300px; width: 100%; object-fit: cover;">
                @endif
            </div>
            <div class="col-md-8">
                <h3>{{ $product->name }}</h3>
                <div class="mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($product->rate) ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                    <span class="text-muted">({{ $product->reviews->count() }} reviews)</span>
                </div>
                
                <table class="table table-bordered mt-3">
                    <tr>
                        <th width="30%">Price</th>
                        <td>{{ number_format($product->price, 2) }} EGP</td>
                    </tr>
                    <tr>
                        <th>Type</th>
                        <td>
                            <span class="badge bg-{{ $product->type == 'sale' ? 'success' : 'info' }}">
                                {{ $product->type == 'sale' ? 'For Sale' : 'For Rent' }}
                            </span>
                         </td>
                    </tr>
                    <tr>
                        <th>Available Quantity</th>
                        <td>{{ $product->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>{{ $product->category->name ?? 'Not specified' }}</td>
                    </tr>
                    <tr>
                        <th>Owner</th>
                        <td>
                            {{ $product->owner->name ?? 'Not specified' }}
                            <br>
                            <small class="text-muted">{{ $product->owner->email ?? '' }}</small>
                         </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $product->status == '1' ? 'success' : 'danger' }}">
                                {{ $product->status == '1' ? 'Active' : 'Inactive' }}
                            </span>
                         </td>
                    </tr>
                    <tr>
                        <th>Statistics</th>
                        <td>
                            <i class="fas fa-eye"></i> {{ number_format($product->view_count) }} views |
                            <i class="fas fa-shopping-cart"></i> {{ number_format($product->pay_count) }} purchases |
                            <i class="fas fa-cart-plus"></i> {{ number_format($product->addingToCart_count) }} added to cart
                         </td>
                    </tr>
                    <tr>
                        <th>Added Date</th>
                        <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Last Updated</th>
                        <td>{{ $product->updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                划table>
                
                <div class="mt-3">
                    <h5>Description</h5>
                    <p>{{ $product->description ?: 'No description' }}</p>
                </div>
            </div>
        </div>

        <!-- Reviews -->
        @if($product->reviews->count() > 0)
        <div class="mt-4">
            <h5>Reviews ({{ $product->reviews->count() }})</h5>
            <div class="list-group">
                @foreach($product->reviews as $review)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $review->user->name }}</strong>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star fa-sm {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="mb-0 mt-2">{{ $review->comment }}</p>
                        <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Previous Orders -->
        @if($product->orders->count() > 0)
        <div class="mt-4">
            <h5>Previous Orders ({{ $product->orders->count() }})</h5>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Order Number</th>
                            <th>Buyer</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->orders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->user->name }}</td>
                            <td>{{ $order->quantity }}</td>
                            <td>{{ number_format($order->total_price, 2) }} EGP</td>
                            <td>
                                <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ $order->status }}
                                </span>
                             </td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                划table
            </div>
        </div>
        @endif
    </div>
</div>
@endsection