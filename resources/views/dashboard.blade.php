{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'لوحة التحكم')

@push('styles')
<style>
    :root {
        --primary: #4f46e5; --success: #10b981; --warning: #f59e0b;
        --danger: #ef4444; --info: #3b82f6; --dark: #1f2937;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --card-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    [dir="rtl"] { direction: rtl; text-align: right; }
    .stat-card { 
        transition: all 0.3s; border-radius: 16px; border: none;
        box-shadow: var(--card-shadow); overflow: hidden; position: relative;
    }
    .stat-card:hover { transform: translateY(-4px); box-shadow: var(--card-hover); }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
        background: linear-gradient(90deg, var(--primary), #818cf8); opacity: 0; transition: 0.3s;
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-icon {
        width: 56px; height: 56px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; transition: 0.3s;
    }
    .stat-card:hover .stat-icon { transform: scale(1.1); }
    .badge-custom {
        font-weight: 600; border-radius: 20px; padding: 0.35rem 0.85rem;
        font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;
    }
    .table th {
        font-weight: 600; font-size: 0.75rem; text-transform: uppercase;
        letter-spacing: 0.05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 1rem;
    }
    .table td { padding: 1rem; vertical-align: middle; }
    .product-thumb {
        width: 52px; height: 52px; border-radius: 12px; object-fit: cover;
        border: 2px solid #f3f4f6; transition: 0.2s;
    }
    .product-thumb:hover { border-color: var(--primary); }
    .progress-custom { height: 6px; border-radius: 10px; overflow: hidden; background: #e5e7eb; }
    .chart-container { position: relative; height: 280px; width: 100%; }
    .alert-custom {
        border-radius: 12px; border: none; box-shadow: var(--card-shadow);
        display: flex; align-items: center; gap: 1rem; padding: 1rem; margin-bottom: 0.75rem;
    }
    .quick-action {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 1.25rem; border-radius: 14px; background: white;
        border: 2px dashed #e5e7eb; color: var(--dark); text-decoration: none;
        transition: 0.2s; gap: 0.5rem;
    }
    .quick-action:hover { border-color: var(--primary); background: #f5f7ff; color: var(--primary); }
    .quick-action i { font-size: 1.5rem; }
    [dir="rtl"] .me-2 { margin-left: 0.5rem !important; margin-right: 0 !important; }
    [dir="rtl"] .ms-2 { margin-right: 0.5rem !important; margin-left: 0 !important; }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
    
    <!-- 🔷 Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">
                <i class="bi bi-speedometer2 text-primary me-2"></i>
                لوحة التحكم
            </h2>
            <p class="text-muted mb-0 small">مرحباً، <strong>{{ $user->name }}</strong> 
                <span class="badge bg-{{ $user->role === 'Admin' ? 'danger' : 'primary' }} ms-2">{{ $user->role }}</span>
            </p>
        </div>
        
        <div class="d-flex gap-2">
            @if( $user->role === 'admin')
            <a href="{{ route('products.create') }}" class="btn btn-primary btn-lg shadow-sm px-4">
                <i class="bi bi-plus-circle me-2"></i> منتج جديد
            </a>
            @endif
            @if($user->role === 'Admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-danger btn-lg shadow-sm px-4">
                <i class="bi bi-shield-lock me-2"></i> لوحة الأدمن
            </a>
            @endif
            <button class="btn btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i>
            </button>
        </div>
    </div>

    <!-- 🔷 تنبيهات ذكية -->
    @if(!empty($alerts) && count($alerts) > 0)
    <div class="row mb-4">
        <div class="col-12">
            @foreach($alerts as $alert)
            <div class="alert alert-{{ $alert['type'] }} alert-custom mb-2" role="alert">
                <i class="{{ $alert['icon'] }} fs-5"></i>
                <div class="flex-grow-1">
                    <p class="mb-0 fw-medium">{{ $alert['message'] }}</p>
                </div>
                <a href="{{ $alert['action'] }}" class="btn btn-sm btn-outline-{{ $alert['type'] }}">
                    عرض <i class="bi bi-arrow-left"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- 🔷 إجراءات سريعة -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('products.create') }}" class="quick-action">
                <i class="bi bi-plus-lg text-primary"></i>
                <span class="small fw-medium">إضافة منتج</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('orders.index') }}" class="quick-action">
                <i class="bi bi-cart text-success"></i>
                <span class="small fw-medium">طلباتي</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            {{-- <a href="{{ route('wishlist.index') }}" class="quick-action"> --}}
                <i class="bi bi-heart text-danger"></i>
                <span class="small fw-medium">المفضلة</span>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('profile.edit') }}" class="quick-action">
                <i class="bi bi-gear text-secondary"></i>
                <span class="small fw-medium">الإعدادات</span>
            </a>
        </div>
    </div>

    <!-- 🔷 بطاقات الإحصائيات -->
    <div class="row g-4 mb-4">
        
        <!-- إحصائيات المشتري (لكل المستخدمين) -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 bg-primary bg-gradient text-white position-relative">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-90 small">إجمالي الطلبات</p>
                            <h3 class="fw-bold mb-0">{{ number_format($buyer_stats['orders_count'] ?? 0) }}</h3>
                            <small class="opacity-75">
                                <i class="bi bi-check-circle"></i> 
                                {{ $buyer_stats['pending_orders'] ?? 0 }} قيد الانتظار
                            </small>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-20">
                            <i class="bi bi-bag-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 bg-info bg-gradient text-white position-relative">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-90 small">الإيجارات النشطة</p>
                            <h3 class="fw-bold mb-0">{{ number_format($buyer_stats['rentals_count'] ?? 0) }}</h3>
                            <small class="opacity-75">
                                <i class="bi bi-calendar-range"></i> عقد حالي
                            </small>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-20">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 bg-warning bg-gradient text-white position-relative">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-90 small">إجمالي المصروفات</p>
                            <h3 class="fw-bold mb-0">{{ number_format($buyer_stats['total_spent'] ?? 0, 2) }} <small class="fs-6">$</small></h3>
                            <small class="opacity-75">
                                <i class="bi bi-wallet2"></i> مدفوعات مكتملة
                            </small>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-20">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 bg-success bg-gradient text-white position-relative">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="mb-1 opacity-90 small">قائمة المفضلة</p>
                            <h3 class="fw-bold mb-0">{{ number_format($buyer_stats['wishlist_count'] ?? 0) }}</h3>
                            <small class="opacity-75">
                                <i class="bi bi-heart"></i> عنصر محفوظ
                            </small>
                        </div>
                        <div class="stat-icon bg-white bg-opacity-20">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- إحصائيات البائع/المؤجر (تظهر فقط لمن لديه منتجات أو أدمن) -->
        @if($seller_stats)
        <div class="col-12 mt-2">
            <h6 class="text-muted border-bottom pb-2 mb-3">
                <i class="bi bi-shop me-2"></i>
                أداء منتجاتك
            </h6>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">منتجاتي</p>
                            <h3 class="fw-bold mb-0">{{ $seller_stats['products_count'] ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-check-circle"></i>
                                {{ $seller_stats['available_products'] ?? 0 }} متاح
                            </small>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">الإيرادات</p>
                            <h3 class="fw-bold mb-0">{{ number_format(($seller_stats['total_revenue'] ?? 0) + ($seller_stats['total_rental_income'] ?? 0), 2) }} <small class="fs-6">$</small></h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 
                                بيع: {{ number_format($seller_stats['total_revenue'] ?? 0, 0) }}$ 
                                | إيجار: {{ number_format($seller_stats['total_rental_income'] ?? 0, 0) }}$
                            </small>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">معدل التحويل</p>
                            <h3 class="fw-bold mb-0">{{ $seller_stats['conversion_rate'] ?? 0 }}%</h3>
                            <small class="text-info">
                                <i class="bi bi-eye"></i> 
                                {{ number_format($seller_stats['total_views'] ?? 0, 0) }} مشاهدة
                            </small>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-funnel"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">تقييم المنتجات</p>
                            <h3 class="fw-bold mb-0">{{ $seller_stats['avg_rating'] ?? 0 }}/5</h3>
                            <small class="text-warning">
                                <i class="bi bi-star-fill"></i>
                                {{ $seller_stats['avg_rating'] >= 4.5 ? 'ممتاز' : ($seller_stats['avg_rating'] >= 3.5 ? 'جيد جداً' : 'جيد') }}
                            </small>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- 🔷 الرسوم البيانية (تظهر لمن لديه منتجات أو أدمن) -->
    @if($seller_stats)
    <div class="row g-4 mb-4">
        <!-- رسم الإيرادات -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up text-primary me-2"></i>
                        تحليل الإيرادات والطلبات
                    </h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-auto" id="chartPeriod">
                            <option value="7">آخر 7 أيام</option>
                            <option value="30" selected>آخر 30 يوم</option>
                            <option value="90">آخر 3 أشهر</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- رسم حالة الطلبات -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart text-success me-2"></i>
                        حالة الطلبات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 200px;">
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                    <div class="mt-3 text-center small text-muted">
                        إجمالي: {{ $seller_stats['products_count'] ?? 0 }} منتج
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 🔷 المحتوى الرئيسي: الطلبات ومنتجاتي -->
    <div class="row g-4">
        
        <!-- آخر الطلبات (كمشتري) -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        آخر طلباتي
                    </h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل <i class="bi bi-arrow-left me-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">المنتج</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_orders as $order)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($order['product_image'])
                                            <img src="{{ asset('storage/' . $order['product_image']) }}" 
                                                 class="product-thumb me-2" alt=""
                                                 onerror="this.src='https://via.placeholder.com/52?text=No+Image'">
                                            @endif
                                            <span class="fw-medium small">{{ Str::limit($order['product_name'], 22) }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-bold small">{{ number_format($order['total_price'], 2) }} $</td>
                                    <td>
                                        <span class="badge-custom bg-{{ $order['status_label'][0] }} text-white">
                                            <i class="{{ $order['status_label'][2] }}"></i>
                                            {{ $order['status_label'][1] }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted d-block">{{ $order['created_at_formatted'] }}</small>
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ $order['created_at_human'] }}
                                        </small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-inbox fs-3 text-muted"></i>
                                        <p class="text-muted mb-0 mt-2 small">لا توجد طلبات حديثة</p>
                                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-primary mt-2">
                                            تصفح المنتجات
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- منتجاتي وأداؤها (تظهر فقط لمن لديه منتجات أو أدمن) -->
        @if($my_products)
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-box-seam text-success me-2"></i>
                        منتجاتي وأداؤها
                    </h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-success">
                        إدارة الكل <i class="bi bi-arrow-left me-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($my_products as $product)
                        <div class="list-group-item px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $product['image'] ? asset('storage/' . $product['image']) : 'https://via.placeholder.com/52?text=No+Image' }}" 
                                         class="product-thumb me-3" alt=""
                                         onerror="this.src='https://via.placeholder.com/52?text=No+Image'">
                                    <div>
                                        <h6 class="mb-0 small fw-medium">{{ Str::limit($product['name'], 25) }}</h6>
                                        <small class="text-muted">
                                            {{ number_format($product['price'], 2) }} $ 
                                            <span class="badge bg-light text-dark ms-1">{{ $product['type'] === 'rental' ? 'إيجار' : 'بيع' }}</span>
                                        </small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge-custom bg-{{ $product['status'] === 'available' ? 'success' : ($product['status'] === 'sold' ? 'secondary' : 'warning') }} text-white">
                                            {{ $product['status'] === 'available' ? 'متاح' : ($product['status'] === 'sold' ? 'مباع' : $product['status']) }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="bi bi-cart-check"></i> {{ $product['orders_count'] }}
                                        </small>
                                    </div>
                                    @if($product['views'] > 0)
                                    <div class="progress-custom mt-2" title="معدل التحويل: {{ $product['conversion'] }}%">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: {{ min($product['conversion'] * 2, 100) }}%"></div>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.65rem;">تحويل: {{ $product['conversion'] }}%</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-box fs-3 text-muted"></i>
                            <p class="text-muted mb-0 mt-2 small">لم تضف أي منتجات بعد</p>
                            <a href="{{ route('products.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus"></i> أضف منتجك الأول
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- 🔷 قسم الاكتشاف: المنتجات المقترحة -->
    <div class="row g-4 mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-stars text-info me-2"></i>
                        منتجات قد تعجبك
                        <small class="text-muted fs-6 d-block">مقترحات مخصصة لك</small>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($recommended as $product)
                        <div class="col-6 col-md-3">
                            <a href="{{ route('products.show', $product->id) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    <img src="{{ $product->images?->first()?->image_url ? asset('storage/' . $product->images->first()->image_url) : 'https://via.placeholder.com/150?text=No+Image' }}" 
                                         class="card-img-top" alt="{{ $product->name }}"
                                         style="height: 120px; object-fit: cover; border-radius: 12px 12px 0 0;"
                                         onerror="this.src='https://via.placeholder.com/150?text=No+Image'">
                                    <div class="card-body p-2">
                                        <h6 class="card-title mb-1 small fw-medium">{{ Str::limit($product->name, 20) }}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong class="text-primary small">{{ number_format($product->price, 0) }}$</strong>
                                            <span class="text-warning small">
                                                <i class="bi bi-star-fill"></i> {{ $product->rate ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @empty
                        <div class="col-12 text-center py-4">
                            <p class="text-muted mb-0 small">استمر في التصفح لتحصل على توصيات مخصصة!</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($seller_stats)
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 📈 رسم الإيرادات
    const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenue_chart['labels'] ?? []),
                datasets: [
                    {
                        label: 'الإيرادات ($)',
                        data: @json($revenue_chart['revenue'] ?? []),
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'عدد الطلبات',
                        data: @json($revenue_chart['orders_count'] ?? []),
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { 
                    legend: { position: 'top', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.dataset.label === 'الإيرادات ($)') {
                                    return `الإيرادات: $${ctx.parsed.y.toLocaleString()}`;
                                }
                                return `الطلبات: ${ctx.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { callback: value => '$' + value.toLocaleString(), font: { size: 10 } },
                        position: 'right'
                    },
                    y1: {
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { callback: value => Math.round(value), font: { size: 10 } },
                        position: 'left',
                        display: false
                    },
                    x: { grid: { display: false }, ticks: { font: { size: 10 }, maxRotation: 45, minRotation: 45 } }
                }
            }
        });
    }

    // 🥧 رسم حالة الطلبات
    const statusCtx = document.getElementById('orderStatusChart')?.getContext('2d');
    if (statusCtx) {
        const statusData = @json($order_status_chart ?? []);
        const labelsMap = {
            'pending': 'انتظار', 'processing': 'جاري المعالجة', 'shipped': 'تم الشحن',
            'delivered': 'مكتمل', 'cancelled': 'ملغي', 'returned': 'مرتجع'
        };
        const colorsMap = {
            'pending': '#f59e0b', 'processing': '#3b82f6', 'shipped': '#8b5cf6',
            'delivered': '#10b981', 'cancelled': '#ef4444', 'returned': '#6b7280'
        };
        
        const labels = Object.keys(statusData).filter(k => statusData[k] > 0);
        const data = labels.map(l => statusData[l]);
        const bgColors = labels.map(l => colorsMap[l] || '#6b7280');
        const arLabels = labels.map(l => labelsMap[l] || l);
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: arLabels.length ? arLabels : ['لا توجد بيانات'],
                datasets: [{
                    data: data.length ? data : [1],
                    backgroundColor: bgColors.length ? bgColors : ['#e5e7eb'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: { size: 10 },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets[0].data.length) {
                                    return data.labels.map((label, i) => {
                                        const total = data.datasets[0].data.reduce((a,b) => a+b, 0);
                                        const value = data.datasets[0].data[i];
                                        const pct = Math.round((value / total) * 100);
                                        return {
                                            text: `${label}: ${value} (${pct}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endpush