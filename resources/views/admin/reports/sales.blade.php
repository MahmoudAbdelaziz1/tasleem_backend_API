{{-- resources/views/admin/reports/sales.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تقرير المبيعات')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line"></i> تقرير المبيعات
        </h6>
        <div>
            <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
               class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> تصدير CSV
            </a>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">
                <i class="fas fa-print"></i> طباعة
            </button>
        </div>
    </div>
    <div class="card-body">
        <!-- نموذج الفلترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> فلترة التقرير
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.sales') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">حالة الطلب</label>
                        <select name="status" class="form-control">
                            <option value="">الكل</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ $status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="shipped" {{ $status == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                            <option value="delivered" {{ $status == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
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
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('admin.reports.sales') }}" class="btn btn-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalOrders) }}</h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalSales, 2) }} EGP</h3>
                        <p>إجمالي المبيعات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($averageOrderValue, 2) }} EGP</h3>
                        <p>متوسط قيمة الطلب</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($stats['delivered'] ?? 0) }}</h3>
                        <p>الطلبات المكتملة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- توزيع حالات الطلبات -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">توزيع حالات الطلبات</div>
                    <div class="card-body">
                        <canvas id="statusChart" style="height: 200px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">ملخص</div>
                    <div class="card-body">
                        <table class="table">
                            <tr>
                                <td>قيد الانتظار</td>
                                <td class="text-end">{{ number_format($stats['pending'] ?? 0) }}</td>
                                <td class="text-end">{{ $totalOrders > 0 ? round(($stats['pending'] ?? 0) / $totalOrders * 100, 1) : 0 }}%</td>
                            </tr>
                            <tr><td>مؤكد</td><td class="text-end">{{ number_format($stats['confirmed'] ?? 0) }}</td><td class="text-end">{{ $totalOrders > 0 ? round(($stats['confirmed'] ?? 0) / $totalOrders * 100, 1) : 0 }}%</td></tr>
                            <tr><td>تم الشحن</td><td class="text-end">{{ number_format($stats['shipped'] ?? 0) }}</td><td class="text-end">{{ $totalOrders > 0 ? round(($stats['shipped'] ?? 0) / $totalOrders * 100, 1) : 0 }}%</td></tr>
                            <tr><td>تم التسليم</td><td class="text-end">{{ number_format($stats['delivered'] ?? 0) }}</td><td class="text-end">{{ $totalOrders > 0 ? round(($stats['delivered'] ?? 0) / $totalOrders * 100, 1) : 0 }}%</td></tr>
                            <tr><td>ملغي</td><td class="text-end">{{ number_format($stats['cancelled'] ?? 0) }}</td><td class="text-end">{{ $totalOrders > 0 ? round(($stats['cancelled'] ?? 0) / $totalOrders * 100, 1) : 0 }}%</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الطلبات -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="salesTable">
                <thead class="table-dark">
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الإجمالي</th>
                        <th>الحالة</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->order_id }}</td>
                        <td>{{ $order->user->name ?? 'غير محدد' }}</td>
                        <td>{{ Str::limit($order->product->name ?? 'غير محدد', 30) }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>{{ number_format($order->unit_price, 2) }} EGP</td>
                        <td><strong>{{ number_format($order->total_price, 2) }} EGP</strong></td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'delivered' ? 'success' : ($order->status == 'pending' ? 'warning' : 'danger') }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($orders->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p>لا توجد بيانات في الفترة المحددة</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    var statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['قيد الانتظار', 'مؤكد', 'تم الشحن', 'تم التسليم', 'ملغي'],
            datasets: [{
                data: [
                    {{ $stats['pending'] ?? 0 }},
                    {{ $stats['confirmed'] ?? 0 }},
                    {{ $stats['shipped'] ?? 0 }},
                    {{ $stats['delivered'] ?? 0 }},
                    {{ $stats['cancelled'] ?? 0 }}
                ],
                backgroundColor: ['#f6c23e', '#36b9cc', '#4e73df', '#1cc88a', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>
@endpush