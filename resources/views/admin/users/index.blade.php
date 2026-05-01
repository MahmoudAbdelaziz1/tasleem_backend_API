{{-- resources/views/admin/users/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'User Management')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> Users List
        </h6>
        <div>
            <a href="{{ route('admin.users.sellers') }}" class="btn btn-info btn-sm">
                <i class="fas fa-store"></i> Sellers
            </a>
            <a href="{{ route('admin.users.customers') }}" class="btn btn-success btn-sm">
                <i class="fas fa-shopping-cart"></i> Customers
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New User
            </a>
        </div>
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
            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th width="60">Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Products</th>
                        <th>Orders</th>
                        <th>Rentals</th>
                        <th>Registration Date</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $index => $user)
                    <tr>
                        <td class="text-center">{{ $users->firstItem() + $index }}</td>
                        <td class="text-center">
                            @if($user->user_photo)
                                <img src="{{ $user->user_photo }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle"
                                     width="45" height="45"
                                     style="object-fit: cover; border: 2px solid #ddd;">
                            @else
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user->name }}</strong>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                                <i class="fas {{ $user->role == 'admin' ? 'fa-crown' : 'fa-user' }}"></i>
                                {{ $user->role == 'admin' ? 'Admin' : 'User' }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.users.toggle-status', $user) }}" 
                                  method="POST" 
                                  class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="btn btn-sm {{ $user->status == '1' ? 'btn-success' : 'btn-secondary' }}">
                                    <i class="fas {{ $user->status == '1' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $user->status == '1' ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">
                                {{ $user->products_count ?? 0 }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-warning">
                                {{ $user->orders_count ?? 0 }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary">
                                {{ $user->rentals_count ?? 0 }}
                            </span>
                        </td>
                        <td>
                            <small>{{ $user->created_at->format('Y-m-d') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user) }}" 
                                   class="btn btn-sm btn-info" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" 
                                   class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <button type="button" 
                                        class="btn btn-sm btn-danger" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal{{ $user->id }}"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>

                            <!-- Delete Modal -->
                            <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Confirm Delete</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center">
                                                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                                                <p>Are you sure you want to delete the user <strong>"{{ $user->name }}"</strong>?</p>
                                                <p class="text-danger small">All user data including products and orders will be deleted!</p>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
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
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#usersTable').DataTable({
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