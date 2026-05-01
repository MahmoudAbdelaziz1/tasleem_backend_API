{{-- resources/views/admin/categories/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'Category Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-tags"></i> Categories List
        </h6>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add New Category
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="categoriesTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th width="80">Image</th>
                        <th>Category Name</th>
                        <th width="100">Products Count</th>
                        <th width="100">Status</th>
                        <th width="100">Added Date</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $index => $category)
                    <tr>
                        <td class="text-center">{{ $categories->firstItem() + $index }}</td>
                        <td class="text-center">
                            @if($category->photo)
                                <img src="{{ $category->photo }}" 
                                     alt="{{ $category->name }}" 
                                     class="rounded-circle"
                                     width="50" height="50"
                                     style="object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-tag text-white fa-2x"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info rounded-pill">
                                {{ $category->products_count }} Products
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="btn btn-sm {{ $category->status == '1' ? 'btn-success' : 'btn-secondary' }}">
                                    <i class="fas {{ $category->status == '1' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $category->status == '1' ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <small>{{ $category->created_at->format('Y-m-d') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.categories.show', $category) }}" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal{{ $category->category_id }}"
                                        title="Delete"
                                        {{ $category->products_count > 0 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $category->category_id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center">
                                                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                                <p>Are you sure you want to delete the category <strong>"{{ $category->name }}"</strong>?</p>
                                                @if($category->products_count > 0)
                                                    <div class="alert alert-danger">
                                                        <i class="fas fa-info-circle"></i> 
                                                        This category cannot be deleted because it has {{ $category->products_count }} product(s) associated with it.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            @if($category->products_count == 0)
                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $categories->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#categoriesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
});
</script>
@endpush