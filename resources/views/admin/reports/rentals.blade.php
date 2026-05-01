{{-- resources/views/admin/reports/rentals.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تقرير الإيجار')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-calendar-check"></i> تقرير الإيجار
        </h6>
        <a href="{{ route('admin.reports.export', ['type' => 'rentals', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
           class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> تصدير CSV
        </a>
    </div>
    <div class="card-body">
        <!-- نموذج الفلترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> فلترة التقرير
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.rentals') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">حالة العقد</label>
                        <select name="status" class="form-control">
                            <option value="">الكل</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="completed" {{ $status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ $status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المنتج</label>
                        <select name="product_id" class="form-control">
                            <option value="">الكل</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $productId == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <a href="{{ route('admin.reports.rentals') }}" class="btn btn-secondary">إعادة تعيين</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($rentalCount) }}</h3>
                        <p>عدد عقود الإيجار</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalRentalsRevenue, 2) }} EGP</h3>
                        <p>إجمالي الإيرادات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($averageRentalValue, 2) }} EGP</h3>
                        <p>متوسط قيمة العقد</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalDaysRented) }}</h3>
                        <p>إجمالي أيام التأجير</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول عقود الإيجار -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>رقم العقد</th>
                        <th>المستأجر</th>
                        <th>المنتج</th>
                        <th>الفترة</th>
                        <th>عدد الأيام</th>
                        <th>السعر اليومي</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rentals as $rental)
                    <tr>
                        <td>#{{ $rental->rental_id }}</td>
                        <td>{{ $rental->renter->name ?? 'غير محدد' }}</td>
                        <td>{{ Str::limit($rental->product->name ?? 'غير محدد', 30) }}</td>
                        <td>{{ $rental->start_date }} → {{ $rental->end_date }}</td>
                        <td>{{ $rental->total_days }} يوم</td>
                        <td>{{ number_format($rental->daily_price, 2) }} EGP</td>
                        <td><strong>{{ number_format($rental->total_price, 2) }} EGP</strong></td>
                        <td>
                            <span class="badge bg-{{ $rental->status == 'completed' ? 'success' : ($rental->status == 'active' ? 'primary' : 'warning') }}">
                                {{ $rental->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($rentals->isEmpty())
            <div class="alert alert-info text-center">لا توجد بيانات في الفترة المحددة</div>
        @endif
    </div>
</div>
@endsection