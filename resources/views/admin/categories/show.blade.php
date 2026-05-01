{{-- resources/views/admin/categories/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Category Details: ' . $category->name)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-tag"></i> Category Details: {{ $category->name }}
        </h6>
        <div>
            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Category Information -->
        <div class="row mb-4">
            <div class="col-md-3 text-center">
                @if($category->photo)
                    <img src="{{ $category->photo }}" 
                         alt="{{ $category->name }}" 
                         class="img-fluid rounded"
                         style="max-width: 200px; border: 3px solid #ddd;">
                @else
                    <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center"
                         style="width: 200px; height: 200px;">
                        <i class="fas fa-tag text-white fa-5x"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Category Name:</th>
                                <td><strong>{{ $category->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge bg-{{ $category->status == '1' ? 'success' : 'danger' }} fs-6">
                                        <i class="fas {{ $category->status == '1' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $category->status == '1' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Products Count:</th>
                                <td>
                                    <span class="badge bg-info fs-6">
                                        <i class="fas fa-boxes"></i> {{ $productsCount }} Products
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Active Products:</th>
                                <td>
                                    <span class="badge bg-success">{{ $activeProducts }} Products</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Added Date:</th>
                                <td>{{ $category->created_at->format('Y-m-d h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $category->updated_at->format('Y-m-d h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products List in this Category -->
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="text-primary">
                    <i class="fas fa-boxes"></i> Products in this Category
                </h5>
                <a href="{{ route('admin.products.index', ['category' => $category->category_id]) }}" 
                   class="btn btn-sm btn-info">
                    <i class="fas fa-list"></i> View All Products
                </a>
            </div>
            
            @if($category->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>Owner</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <img src="{{ $product->primary_image }}" 
                                         alt="{{ $product->name }}"
                                         width="50" height="50"
                                         style="object-fit: cover; border-radius: 5px;">
                                </td>
                                <td>{{ Str::limit($product->name, 30) }}</td>
                                <td>{{ number_format($product->price, 2) }} EGP</td>
                                <td>
                                    <span class="badge bg-{{ $product->type == 'sale' ? 'success' : 'info' }}">
                                        {{ $product->type == 'sale' ? 'Sale' : 'Rent' }}
                                    </span>
                                </td>
                                <td>{{ $product->owner->name ?? 'Not specified' }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->status == '1' ? 'success' : 'danger' }}">
                                        {{ $product->status == '1' ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>There are no products in this category yet</p>
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Statistics -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line fa-2x mb-2"></i>
                        <h4>{{ number_format($category->products->avg('price') ?? 0, 2) }} EGP</h4>
                        <p>Average Product Price</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-2"></i>
                        <h4>{{ number_format($category->products->avg('rate') ?? 0, 1) }}/5</h4>
                        <p>Average Rating</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-eye fa-2x mb-2"></i>
                        <h4>{{ number_format($category->products->sum('view_count') ?? 0) }}</h4>
                        <p>Total Views</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection