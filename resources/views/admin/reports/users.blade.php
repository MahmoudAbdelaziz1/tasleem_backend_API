{{-- resources/views/admin/reports/users.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تقرير المستخدمين')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users"></i> تقرير المستخدمين
        </h6>
        <a href="{{ route('admin.reports.export', ['type' => 'users']) }}" class="btn btn-success btn-sm">
            <i class="fas fa-download"></i> تصدير CSV
        </a>
    </div>
    <div class="card-body">
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- نموذج الفلترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> فلترة التقرير
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.users') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الدور</label>
                        <select name="role" class="form-control">
                            <option value="">الكل</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>مستخدم</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">الكل</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('admin.reports.users') }}" class="btn btn-secondary">
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
                        <h3>{{ number_format($totalUsers ?? 0) }}</h3>
                        <p>إجمالي المستخدمين</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($activeUsers ?? 0) }}</h3>
                        <p>مستخدمين نشطين</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($inactiveUsers ?? 0) }}</h3>
                        <p>مستخدمين غير نشطين</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($newUsers ?? 0) }}</h3>
                        <p>مستخدمين جدد (30 يوم)</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- توزيع المستخدمين حسب الدور -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">توزيع المستخدمين حسب الدور</div>
                    <div class="card-body">
                        <canvas id="roleChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- المستخدمين حسب الشهر -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">المستخدمين الجدد حسب الشهر</div>
                    <div class="card-body">
                        <canvas id="monthlyChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول المستخدمين -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>الدور</th>
                        <th>الحالة</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users ?? [] as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : 'primary' }}">
                                {{ $user->role == 'admin' ? 'مدير' : 'مستخدم' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->status == '1' ? 'success' : 'danger' }}">
                                {{ $user->status == '1' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($users) && method_exists($users, 'links'))
            <div class="d-flex justify-content-center mt-3">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Role Chart
    @if(isset($usersByRole) && $usersByRole->count() > 0)
        var roleCtx = document.getElementById('roleChart').getContext('2d');
        var roleLabels = [];
        var roleData = [];
        
        @foreach($usersByRole as $role)
            roleLabels.push('{{ $role->role == "admin" ? "مدير" : "مستخدم" }}');
            roleData.push({{ $role->total }});
        @endforeach
        
        new Chart(roleCtx, {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleData,
                    backgroundColor: ['#4e73df', '#1cc88a']
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
    @endif

    // Monthly Chart
    @if(isset($usersByMonth) && $usersByMonth->count() > 0)
        var monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        var monthlyLabels = [];
        var monthlyData = [];
        
        @foreach($usersByMonth->reverse() as $month)
            monthlyLabels.push('{{ $month->month }}');
            monthlyData.push({{ $month->total }});
        @endforeach
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'مستخدمين جدد',
                    data: monthlyData,
                    borderColor: '#36b9cc',
                    backgroundColor: 'rgba(54, 185, 204, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    @endif
});
</script>
@endpush