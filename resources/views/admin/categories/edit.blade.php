{{-- resources/views/admin/categories/edit.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Edit Category: ' . $category->name)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> Edit Category: {{ $category->name }}
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Category Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $category->name) }}" 
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Category Status <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('status') is-invalid @enderror" 
                                id="status" 
                                name="status" 
                                required>
                            <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        <small class="text-muted">Inactive categories will not appear in the store</small>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Category Image</label>
                        <div class="text-center mb-3">
                            <div id="imagePreview" class="border rounded p-3 mb-2" style="min-height: 150px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                @if($category->photo)
                                    <img src="{{ $category->photo }}" 
                                         style="max-width: 100%; max-height: 150px; object-fit: contain;">
                                @else
                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                    <span class="text-muted ms-2">No image selected</span>
                                @endif
                            </div>
                        </div>
                        <input type="file" 
                               class="form-control @error('photo') is-invalid @enderror" 
                               id="photo" 
                               name="photo" 
                               accept="image/*">
                        <small class="text-muted">Leave blank if you don't want to change the image</small>
                        @error('photo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Category
                </button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('photo').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.innerHTML = `<img src="${event.target.result}" style="max-width: 100%; max-height: 150px; object-fit: contain;">`;
            }
            reader.readAsDataURL(file);
        } else {
            @if($category->photo)
                preview.innerHTML = `<img src="{{ $category->photo }}" style="max-width: 100%; max-height: 150px; object-fit: contain;">`;
            @else
                preview.innerHTML = `<i class="fas fa-cloud-upload-alt fa-3x text-muted"></i>
                                     <span class="text-muted ms-2">No image selected</span>`;
            @endif
        }
    });
</script>
@endpush