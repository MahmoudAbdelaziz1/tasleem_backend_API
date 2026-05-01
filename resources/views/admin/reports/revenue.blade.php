{{-- resources/views/admin/reports/revenue.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تقرير الإيرادات')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-dollar-sign"></i> تقرير الإيرادات
        </h6>
    </div>
    <div class="card-body">
        <!-- نموذج اختيار السنة -->
        <div class="row mb-4">
            <div class="col-md-4">
                <form method="GET" action="{{ route('admin.reports.revenue') }}" class="form-inline">
                    <div class="input-group">
                        <select name="year" class="form-control" onchange="this.form.submit()">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                        <span class="input-group-text">السنة</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- بطاقات الإحصائيات -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalRevenue, 2) }} EGP</h3>
                        <p>إجمالي إيرادات {{ $year }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($currentMonthRevenue, 2) }} EGP</h3>
                        <p>إيرادات الشهر الحالي</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($dailyRevenue, 2) }} EGP</h3>
                        <p>إيرادات اليوم</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- الرسم البياني للإيرادات الشهرية -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">الإيرادات الشهرية {{ $year }}</div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="height: 350px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- توزيع طرق الدفع -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">توزيع طرق الدفع</div>
                    <div class="card-body">
                        <canvas id="paymentChart" style="height: 250px;"></canvas>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                @foreach($paymentMethods as $method)
                                <tr>
                                    <td>{{ $method->payment_method }}</td>
                                    <td>{{ number_format($method->total, 2) }} EGP</td>
                                    <td>{{ $totalRevenue > 0 ? round(($method->total / $totalRevenue) * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول الإيرادات الشهرية -->
        <div class="card mt-4">
            <div class="card-header">تفاصيل الإيرادات الشهرية</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>الشهر</th>
                                <th>الإيرادات</th>
                                <th>النسبة</th>
                                <th>التراكمي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $cumulative = 0;
                            @endphp
                            @foreach($revenueData as $index => $revenue)
                                @php
                                    $cumulative += $revenue;
                                    $monthName = date('F', mktime(0, 0, 0, $index + 1, 1));
                                @endphp
                                <tr>
                                    <td>{{ $monthName }}</td>
                                    <td>{{ number_format($revenue, 2) }} EGP</td>
                                    <td>{{ $totalRevenue > 0 ? round(($revenue / $totalRevenue) * 100, 1) : 0 }}%</td>
                                    <td>{{ number_format($cumulative, 2) }} EGP</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-active">
                            <tr>
                                <th>الإجمالي</th>
                                <th>{{ number_format($totalRevenue, 2) }} EGP</th>
                                <th>100%</th>
                                <th>-</th>
                            </tr>
                        </tfoot>
                    </table>
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
    var revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'],
            datasets: [{
                label: 'الإيرادات',
                data: {{ json_encode($revenueData) }},
                backgroundColor: '#4e73df',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw.toLocaleString() + ' EGP';
                        }
                    }
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

    var paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($paymentMethods->pluck('payment_method')) !!},
            datasets: [{
                data: {!! json_encode($paymentMethods->pluck('total')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
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