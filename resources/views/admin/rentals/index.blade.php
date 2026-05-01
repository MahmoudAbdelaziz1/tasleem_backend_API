{{-- resources/views/admin/rentals/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'إدارة عقود الإيجار')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-check"></i> عقود الإيجار
        </h6>
        <a href="{{ route('admin.rentals.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> إضافة عقد إيجار جديد
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

        <!-- بطاقات الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">إجمالي العقود</h6>
                                <h3 class="mb-0">{{ number_format($stats['total'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-file-contract fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">قيد الانتظار</h6>
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
                                <h6 class="mb-0">نشط حالياً</h6>
                                <h3 class="mb-0">{{ number_format($stats['active'] ?? 0) }}</h3>
                            </div>
                            <i class="fas fa-play-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">إيرادات الإيجار</h6>
                                <h5 class="mb-0">{{ number_format($stats['total_revenue'] ?? 0, 2) }} EGP</h5>
                            </div>
                            <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث والفلترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> فلترة العقود
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.rentals.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">حالة العقد</label>
                        <select name="status" class="form-control">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ قيد الانتظار</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✅ مؤكد</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>▶️ نشط</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>✔️ مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المستأجر</label>
                        <select name="renter_id" class="form-control">
                            <option value="">الكل</option>
                            @foreach($renters ?? [] as $renter)
                                <option value="{{ $renter->id }}" {{ request('renter_id') == $renter->id ? 'selected' : '' }}>
                                    {{ $renter->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المنتج</label>
                        <select name="product_id" class="form-control">
                            <option value="">الكل</option>
                            @foreach($products ?? [] as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- جدول عقود الإيجار -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="rentalsTable">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th>المنتج</th>
                        <th>المستأجر</th>
                        <th>فترة الإيجار</th>
                        <th>المدة (أيام)</th>
                        <th>السعر اليومي</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>تاريخ الإنشاء</th>
                        <th width="150">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentals as $index => $rental)
                    <tr>
                        <td class="text-center">{{ $rentals->firstItem() + $index }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($rental->product && $rental->product->primary_image)
                                    <img src="{{ $rental->product->primary_image }}" 
                                         class="rounded me-2" 
                                         width="40" height="40"
                                         style="object-fit: cover;">
                                @endif
                                <div>
                                    <strong>{{ Str::limit($rental->product->name ?? 'غير محدد', 25) }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $rental->product_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($rental->renter && $rental->renter->user_photo)
                                    <img src="{{ $rental->renter->user_photo }}" 
                                         class="rounded-circle me-2" 
                                         width="30" height="30"
                                         style="object-fit: cover;">
                                @endif
                                <div>
                                    <strong>{{ $rental->renter->name ?? 'غير محدد' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $rental->renter->email ?? '' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small>
                                <i class="fas fa-calendar-alt text-primary"></i> {{ \Carbon\Carbon::parse($rental->start_date)->format('Y-m-d') }}<br>
                                <i class="fas fa-calendar-check text-success"></i> {{ \Carbon\Carbon::parse($rental->end_date)->format('Y-m-d') }}
                            </small>
                        </td>
                        <td class="text-center">{{ $rental->total_days }} يوم</td>
                        <td class="text-center">{{ number_format($rental->daily_price, 2) }} EGP</td>
                        <td class="text-center">
                            <strong class="text-primary">{{ number_format($rental->total_price, 2) }} EGP</strong>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.rentals.update-status', $rental) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                    <option value="pending" {{ $rental->status == 'pending' ? 'selected' : '' }} class="bg-warning">⏳ قيد الانتظار</option>
                                    <option value="confirmed" {{ $rental->status == 'confirmed' ? 'selected' : '' }} class="bg-info">✅ مؤكد</option>
                                    <option value="active" {{ $rental->status == 'active' ? 'selected' : '' }} class="bg-primary">▶️ نشط</option>
                                    <option value="completed" {{ $rental->status == 'completed' ? 'selected' : '' }} class="bg-success">✔️ مكتمل</option>
                                    <option value="cancelled" {{ $rental->status == 'cancelled' ? 'selected' : '' }} class="bg-danger">❌ ملغي</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-center">
                            <small>{{ $rental->created_at->format('Y-m-d') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.rentals.show', $rental) }}" 
                                   class="btn btn-sm btn-info" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.rentals.edit', $rental) }}" 
                                   class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.rentals.print', $rental) }}" 
                                   class="btn btn-sm btn-secondary" title="طباعة" target="_blank">
                                    <i class="fas fa-print"></i>
                                </a>
                                <a href="{{ route('admin.rentals.contract', $rental) }}" 
                                   class="btn btn-sm btn-success" title="عقد إيجار" target="_blank">
                                    <i class="fas fa-file-signature"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-center mt-3">
            {{ $rentals->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#rentalsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true,
        searching: false,
        paging: false,
        info: false
    });
});
</script>
@endpush