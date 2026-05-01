{{-- resources/views/admin/reports/index.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'لوحة التقارير')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-chart-line"></i> لوحة التقارير الرئيسية
        </h6>
    </div>
    <div class="card-body">
        <!-- بطاقات الإحصائيات السريعة -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    إجمالي المستخدمين
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_users'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    إجمالي المنتجات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_products'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-boxes fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    إجمالي الطلبات
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_orders'] ?? 0) }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    الإيرادات الشهرية
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['monthly_revenue'] ?? 0, 2) }} EGP
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الروابط السريعة للتقارير -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-chart-pie"></i> التقارير المتاحة
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-6 mb-2">
                                <a href="{{ route('admin.reports.sales') }}" class="btn btn-outline-primary w-100 py-3">
                                    <i class="fas fa-chart-line fa-2x d-block mb-2"></i>
                                    تقرير المبيعات
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <a href="{{ route('admin.reports.rentals') }}" class="btn btn-outline-info w-100 py-3">
                                    <i class="fas fa-calendar-check fa-2x d-block mb-2"></i>
                                    تقرير الإيجار
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <a href="{{ route('admin.reports.users') }}" class="btn btn-outline-success w-100 py-3">
                                    <i class="fas fa-users fa-2x d-block mb-2"></i>
                                    تقرير المستخدمين
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-2">
                                <a href="{{ route('admin.reports.products') }}" class="btn btn-outline-warning w-100 py-3">
                                    <i class="fas fa-boxes fa-2x d-block mb-2"></i>
                                    تقرير المنتجات
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-2 mt-2">
                                <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-danger w-100 py-3">
                                    <i class="fas fa-dollar-sign fa-2x d-block mb-2"></i>
                                    تقرير الإيرادات
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-2 mt-2">
                                <a href="{{ route('admin.reports.financial') }}" class="btn btn-outline-secondary w-100 py-3">
                                    <i class="fas fa-chart-pie fa-2x d-block mb-2"></i>
                                    التقرير المالي الشامل
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الرسم البياني للإيرادات -->
        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">الإيرادات الشهرية (آخر 12 شهر)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">أفضل المنتجات مبيعاً</h6>
                    </div>
                    <div class="card-body">
                        @foreach($topProducts as $product)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ Str::limit($product->name, 30) }}</span>
                                    <span>{{ number_format($product->pay_count) }} مبيعات</span>
                                </div>
                                <div class="progress">
                                    @php
                                        $max = $topProducts->max('pay_count');
                                        $percentage = $max > 0 ? ($product->pay_count / $max) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">عدد الطلبات الشهرية</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">أفضل البائعين</h6>
                    </div>
                    <div class="card-body">
                        @foreach($topSellers as $seller)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $seller->name }}</span>
                                    <span>{{ number_format($seller->total_sales ?? 0, 2) }} EGP</span>
                                </div>
                                <div class="progress">
                                    @php
                                        $max = $topSellers->max('total_sales');
                                        $percentage = $max > 0 ? (($seller->total_sales ?? 0) / $max) * 100 : 0;
                                    @endphp
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $seller->products_count }} منتج</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Revenue Chart
    var revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueData['months'] ?? []) !!},
            datasets: [{
                label: 'الإيرادات (EGP)',
                data: {!! json_encode($revenueData['revenue'] ?? []) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' EGP';
                        }
                    }
                }
            }
        }
    });

    // Orders Chart
    var ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($ordersData['months'] ?? []) !!},
            datasets: [{
                label: 'عدد الطلبات',
                data: {!! json_encode($ordersData['orders'] ?? []) !!},
                backgroundColor: '#36b9cc',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endpush