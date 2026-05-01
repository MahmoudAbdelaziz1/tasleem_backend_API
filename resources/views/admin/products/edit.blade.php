{{-- resources/views/admin/products/edit.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Edit Product: ' . $product->name)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> Edit Product: {{ $product->name }}
        </h6>
        <div>
            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> View Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Right Column -->
                <div class="col-md-8">
                    <!-- Product Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Product Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $product->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="5">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Price -->
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">
                                Price <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       step="0.01" 
                                       class="form-control @error('price') is-invalid @enderror" 
                                       id="price" 
                                       name="price" 
                                       value="{{ old('price', $product->price) }}" 
                                       required>
                                <span class="input-group-text">EGP</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="col-md-6 mb-3">
                            <label for="quantity" class="form-label">
                                Available Quantity <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   id="quantity" 
                                   name="quantity" 
                                   value="{{ old('quantity', $product->quantity) }}" 
                                   min="0"
                                   required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Category -->
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">
                                Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" 
                                    name="category_id" 
                                    required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" 
                                        {{ old('category_id', $product->category_id) == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                        @if($category->status != '1') (Inactive) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Owner -->
                        <div class="col-md-6 mb-3">
                            <label for="owner_id" class="form-label">
                                Owner (Seller) <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('owner_id') is-invalid @enderror" 
                                    id="owner_id" 
                                    name="owner_id" 
                                    required>
                                <option value="">Select Owner</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ old('owner_id', $product->owner_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('owner_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Type -->
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">
                                Product Type <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" 
                                    name="type" 
                                    required>
                                <option value="sale" {{ old('type', $product->type) == 'sale' ? 'selected' : '' }}>
                                    For Sale Only
                                </option>
                                <option value="rental" {{ old('type', $product->type) == 'rental' ? 'selected' : '' }}>
                                    For Rent Only
                                </option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">
                                Product Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" 
                                    name="status" 
                                    required>
                                <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle"></i> Active - Listed for Sale
                                </option>
                                <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>
                                    <i class="fas fa-ban"></i> Inactive - Hidden
                                </option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Inactive products will not appear in the store</small>
                        </div>
                    </div>
                </div>

                <!-- Left Column - Images -->
                <div class="col-md-4">
                    <!-- Current Product Images -->
                    <div class="card mb-3">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-images"></i> Product Images
                        </div>
                        <div class="card-body">
                            @if($product->images->count() > 0)
                                <div class="row g-2">
                                    @foreach($product->images as $index => $image)
                                        <div class="col-6 mb-2">
                                            <div class="position-relative">
                                                <img src="{{ $image->image_url }}" 
                                                     alt="{{ $image->alt_text ?? $product->name }}"
                                                     class="img-fluid rounded"
                                                     style="height: 100px; width: 100%; object-fit: cover;">
                                                <button type="button" 
                                                        class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                                        onclick="deleteImage({{ $image->image_id }}, '{{ $image->image_url }}')"
                                                        style="border-radius: 50%;">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                            @else
                                <div class="alert alert-warning text-center">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No images for this product
                                </div>
                            @endif

                            <!-- Add New Images -->
                            <label class="form-label">Add New Images</label>
                            <input type="file" 
                                   class="form-control @error('images') is-invalid @enderror" 
                                   id="images" 
                                   name="images[]" 
                                   accept="image/*"
                                   multiple>
                            <small class="text-muted">You can select multiple images at once (Maximum: 5 images)</small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <!-- New Images Preview -->
                            <div id="newImagesPreview" class="row mt-3 g-2"></div>
                        </div>
                    </div>

                    <!-- Quick Statistics -->
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <i class="fas fa-chart-line"></i> Product Statistics
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td><i class="fas fa-eye text-primary"></i> Views:</td>
                                    <td class="text-end"><strong>{{ number_format($product->view_count) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-shopping-cart text-success"></i> Sales Count:</td>
                                    <td class="text-end"><strong>{{ number_format($product->pay_count) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-cart-plus text-warning"></i> Added to Cart:</td>
                                    <td class="text-end"><strong>{{ number_format($product->addingToCart_count) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-star text-warning"></i> Rating:</td>
                                    <td class="text-end">
                                        <strong>{{ number_format($product->rate, 1) }}/5</strong>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star fa-xs {{ $i <= round($product->rate) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-calendar-alt text-info"></i> Added Date:</td>
                                    <td class="text-end"><small>{{ $product->created_at->format('Y-m-d') }}</small></td>
                                </tr>
                                <tr>
                                    <td><i class="fas fa-edit text-info"></i> Last Updated:</td>
                                    <td class="text-end"><small>{{ $product->updated_at->format('Y-m-d') }}</small></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Product
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Delete Product
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Product Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>Are you sure you want to delete the product <strong>"{{ $product->name }}"</strong>?</p>
                    <p class="text-danger small">
                        <i class="fas fa-info-circle"></i> 
                        All product images and related data will be permanently deleted!
                    </p>
                    @if($product->orders()->count() > 0 || $product->rentals()->count() > 0)
                        <div class="alert alert-danger mt-2">
                            <i class="fas fa-warning"></i>
                            Warning: This product has {{ $product->orders()->count() }} order(s) and 
                            {{ $product->rentals()->count() }} rental(s) associated with it!
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.products.destroy', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete Permanently
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Form for deleting image -->
<form id="deleteImageForm" action="{{ route('admin.products.delete-image') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
    <input type="hidden" name="image_id" id="delete_image_id">
    <input type="hidden" name="product_id" value="{{ $product->id }}">
</form>
@endsection

@push('scripts')
<script>
    // Preview new images before upload
    document.getElementById('images').addEventListener('change', function(e) {
        const preview = document.getElementById('newImagesPreview');
        preview.innerHTML = '';
        
        const files = Array.from(e.target.files);
        
        if (files.length > 5) {
            alert('You can upload a maximum of 5 images');
            this.value = '';
            return;
        }
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const col = document.createElement('div');
                col.className = 'col-6';
                col.innerHTML = `
                    <div class="position-relative">
                        <img src="${event.target.result}" 
                             class="img-fluid rounded"
                             style="height: 100px; width: 100%; object-fit: cover;">
                        <button type="button" 
                                class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1"
                                onclick="removeNewImage(this, ${index})"
                                style="border-radius: 50%;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                preview.appendChild(col);
            }
            reader.readAsDataURL(file);
        });
    });
    
    // Remove image from preview
    window.removeNewImage = function(button, index) {
        const preview = document.getElementById('newImagesPreview');
        const files = document.getElementById('images').files;
        
        // Remove image from files
        const dt = new DataTransfer();
        for (let i = 0; i < files.length; i++) {
            if (i !== index) dt.items.add(files[i]);
        }
        document.getElementById('images').files = dt.files;
        
        // Remove element from preview
        button.closest('.col-6').remove();
        
        // Re-index preview
        const remainingImages = preview.querySelectorAll('.col-6');
        remainingImages.forEach((img, newIndex) => {
            const btn = img.querySelector('button');
            btn.setAttribute('onclick', `removeNewImage(this, ${newIndex})`);
        });
    };
    
    // Delete existing image
    window.deleteImage = function(imageId, imageUrl) {
        if (confirm('Are you sure you want to delete this image?')) {
            document.getElementById('delete_image_id').value = imageId;
            document.getElementById('deleteImageForm').submit();
        }
    };
    
    // Validate form
    document.querySelector('form').addEventListener('submit', function(e) {
        const price = document.getElementById('price').value;
        const quantity = document.getElementById('quantity').value;
        
        if (price <= 0) {
            e.preventDefault();
            alert('Price must be greater than 0');
            return false;
        }
        
        if (quantity < 0) {
            e.preventDefault();
            alert('Quantity cannot be negative');
            return false;
        }
    });
</script>
@endpush

@push('styles')
<style>
    .input-group-text {
        background-color: #f8f9fa;
    }
    .card-header i {
        margin-left: 5px;
    }
    .position-relative .btn-danger {
        opacity: 0.9;
        transition: opacity 0.3s;
    }
    .position-relative .btn-danger:hover {
        opacity: 1;
    }
    select.form-control option:checked {
        background-color: #4e73df;
        color: white;
    }
</style>
@endpush