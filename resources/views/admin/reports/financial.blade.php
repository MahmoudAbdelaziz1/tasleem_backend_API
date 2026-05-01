{{-- resources/views/admin/reports/financial.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'التقرير المالي الشامل')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-pie"></i> التقرير المالي الشامل
        </h6>
        <div>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fas fa-print"></i> طباعة
            </button>
        </div>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- نموذج اختيار الفترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calendar-alt"></i> اختيار الفترة
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.financial') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date', $startDate ?? now()->startOfYear()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date', $endDate ?? now()->endOfYear()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> عرض
                            </button>
                            <a href="{{ route('admin.reports.financial') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقات الإحصائيات الرئيسية -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>إجمالي الإيرادات</small>
                                <h3 class="mb-0">{{ number_format($totalRevenue ?? 0, 2) }} EGP</h3>
                            </div>
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>إيرادات المبيعات</small>
                                <h3 class="mb-0">{{ number_format($salesRevenue ?? 0, 2) }} EGP</h3>
                            </div>
                            <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>إيرادات الإيجار</small>
                                <h3 class="mb-0">{{ number_format($rentalsRevenue ?? 0, 2) }} EGP</h3>
                            </div>
                            <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>العمولة المستحقة ({{ ($commissionRate ?? 10) * 100 }}%)</small>
                                <h3 class="mb-0">{{ number_format($totalCommission ?? 0, 2) }} EGP</h3>
                            </div>
                            <i class="fas fa-percent fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول تفاصيل المبيعات -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-shopping-cart"></i> تفاصيل المبيعات
                </div>
                <span class="badge bg-light text-dark">إجمالي: {{ number_format($salesRevenue ?? 0, 2) }} EGP</span>
            </div>
            <div class="card-body">
                @if(isset($salesOrders) && $salesOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesOrders as $order)
                                <tr>
                                    <td>#{{ $order->order_id }}</a></td>
                                    <td>{{ $order->user->name ?? 'غير محدد' }}</a></td>
                                    <td>{{ Str::limit($order->product->name ?? 'غير محدد', 40) }}</a></td>
                                    <td>{{ $order->quantity }}</a></td>
                                    <td>{{ number_format($order->total_price, 2) }} EGP</strong></a></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ $order->status }}
                                        </span>
                                     </a></td>
                                    <td>{{ $order->created_at->format('Y-m-d') }}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <th colspan="4" class="text-end">الإجمالي:</th>
                                    <th colspan="3">{{ number_format($salesOrders->sum('total_price'), 2) }} EGP</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $salesOrders->appends(request()->except('sales_page'))->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> لا توجد مبيعات في الفترة المحددة
                    </div>
                @endif
            </div>
        </div>

        <!-- جدول تفاصيل الإيجار -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-check"></i> تفاصيل عقود الإيجار
                </div>
                <span class="badge bg-light text-dark">إجمالي: {{ number_format($rentalsRevenue ?? 0, 2) }} EGP</span>
            </div>
            <div class="card-body">
                @if(isset($rentalContracts) && $rentalContracts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>رقم العقد</th>
                                    <th>المستأجر</th>
                                    <th>المنتج</th>
                                    <th>المدة (أيام)</th>
                                    <th>السعر اليومي</th>
                                    <th>الإجمالي</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rentalContracts as $rental)
                                <tr>
                                    <td>#{{ $rental->rental_id }}</a></td>
                                    <td>{{ $rental->renter->name ?? 'غير محدد' }}</a></td>
                                    <td>{{ Str::limit($rental->product->name ?? 'غير محدد', 40) }}</a></td>
                                    <td>{{ $rental->total_days }} يوم</a></td>
                                    <td>{{ number_format($rental->daily_price, 2) }} EGP</a></td>
                                    <td>{{ number_format($rental->total_price, 2) }} EGP</strong></a></td>
                                    <td>
                                        <span class="badge bg-{{ $rental->status == 'completed' ? 'success' : ($rental->status == 'active' ? 'primary' : 'warning') }}">
                                            {{ $rental->status }}
                                        </span>
                                     </a></td>
                                    <td>{{ $rental->created_at->format('Y-m-d') }}</a></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-active">
                                <tr>
                                    <th colspan="5" class="text-end">الإجمالي:</th>
                                    <th colspan="3">{{ number_format($rentalContracts->sum('total_price'), 2) }} EGP</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $rentalContracts->appends(request()->except('rentals_page'))->links() }}
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> لا توجد عقود إيجار في الفترة المحددة
                    </div>
                @endif
            </div>
        </div>

        <!-- ملخص العمولات -->
        <div class="card">
            <div class="card-header bg-warning">
                <i class="fas fa-file-invoice-dollar"></i> ملخص العمولات والضرائب
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted">نسبة العمولة</h6>
                            <h3 class="text-warning">{{ ($commissionRate ?? 10) * 100 }}%</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted">إجمالي العمولة</h6>
                            <h3 class="text-warning">{{ number_format($totalCommission ?? 0, 2) }} EGP</h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3 text-center">
                            <h6 class="text-muted">صافي الإيرادات</h6>
                            <h3 class="text-success">{{ number_format(($totalRevenue ?? 0) - ($totalCommission ?? 0), 2) }} EGP</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush