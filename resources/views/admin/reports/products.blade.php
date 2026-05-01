{{-- resources/views/admin/reports/products.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تقرير المنتجات')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-boxes"></i> تقرير المنتجات
        </h6>
        <div>
            <a href="{{ route('admin.reports.export', ['type' => 'products']) }}" class="btn btn-success btn-sm">
                <i class="fas fa-download"></i> تصدير CSV
            </a>
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

        <!-- نموذج الفلترة -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter"></i> فلترة التقرير
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.reports.products') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">التصنيف</label>
                        <select name="category_id" class="form-control">
                            <option value="">الكل</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->category_id }}" 
                                    {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">نوع المنتج</label>
                        <select name="type" class="form-control">
                            <option value="">الكل</option>
                            <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>للبيع</option>
                            <option value="rental" {{ request('type') == 'rental' ? 'selected' : '' }}>للإيجار</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('admin.reports.products') }}" class="btn btn-secondary">
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
                        <h3>{{ number_format($totalProducts ?? 0) }}</h3>
                        <p>إجمالي المنتجات</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($activeProducts ?? 0) }}</h3>
                        <p>منتجات نشطة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($inactiveProducts ?? 0) }}</h3>
                        <p>منتجات غير نشطة</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format(($saleProducts ?? 0) + ($rentalProducts ?? 0)) }}</h3>
                        <p>بيع: {{ number_format($saleProducts ?? 0) }} | إيجار: {{ number_format($rentalProducts ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- أفضل المنتجات مبيعاً -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <i class="fas fa-chart-line"></i> أفضل المنتجات مبيعاً
                    </div>
                    <div class="card-body">
                        @forelse($topProducts ?? [] as $product)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span title="{{ $product->name }}">{{ Str::limit($product->name, 25) }}</span>
                                    <span class="badge bg-success">{{ number_format($product->pay_count) }} مبيعات</span>
                                </div>
                                <div class="progress mt-1">
                                    @php
                                        $max = $topProducts->max('pay_count') ?? 1;
                                        $percentage = ($product->pay_count / $max) * 100;
                                    @endphp
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($product->price, 2) }} EGP</small>
                            </div>
                        @empty
                            <p class="text-muted text-center">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- أعلى المنتجات تقييماً -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-info">
                        <i class="fas fa-star"></i> أعلى المنتجات تقييماً
                    </div>
                    <div class="card-body">
                        @forelse($topRated ?? [] as $product)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span title="{{ $product->name }}">{{ Str::limit($product->name, 25) }}</span>
                                    <span>
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star fa-xs {{ $i <= round($product->rate) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        ({{ number_format($product->rate, 1) }})
                                    </span>
                                </div>
                                <div class="progress mt-1">
                                    @php
                                        $max = $topRated->max('rate') ?? 5;
                                        $percentage = ($product->rate / $max) * 100;
                                    @endphp
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ number_format($product->price, 2) }} EGP</small>
                            </div>
                        @empty
                            <p class="text-muted text-center">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- الأكثر مشاهدة -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-secondary">
                        <i class="fas fa-eye"></i> الأكثر مشاهدة
                    </div>
                    <div class="card-body">
                        @forelse($mostViewed ?? [] as $product)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span title="{{ $product->name }}">{{ Str::limit($product->name, 25) }}</span>
                                    <span class="badge bg-primary">{{ number_format($product->view_count) }} مشاهدة</span>
                                </div>
                                <div class="progress mt-1">
                                    @php
                                        $max = $mostViewed->max('view_count') ?? 1;
                                        $percentage = ($product->view_count / $max) * 100;
                                    @endphp
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ $percentage }}%"></div>
                                </div>
                                <small class="text-muted">مبيعات: {{ number_format($product->pay_count) }}</small>
                            </div>
                        @empty
                            <p class="text-muted text-center">لا توجد بيانات</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول المنتجات -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-list"></i> قائمة المنتجات
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="productsTable">
                        <thead class="table-dark">
                            <tr>
                                <th width="50">#</th>
                                <th width="80">الصورة</th>
                                <th>اسم المنتج</th>
                                <th>التصنيف</th>
                                <th>السعر</th>
                                <th>النوع</th>
                                <th>الكمية</th>
                                <th>المالك</th>
                                <th>المشاهدات</th>
                                <th>المبيعات</th>
                                <th>التقييم</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products ?? [] as $index => $product)
                            <tr>
                                <td class="text-center">{{ $products->firstItem() + $index }}</td>
                                <td class="text-center">
                                    @if($product->primary_image)
                                        <img src="{{ $product->primary_image }}" 
                                             alt="{{ $product->name }}"
                                             width="50" height="50"
                                             style="object-fit: cover; border-radius: 5px;">
                                    @else
                                        <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-box text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ Str::limit($product->name, 30) }}</strong>
                                    @if($product->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($product->description, 40) }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ number_format($product->price, 2) }} EGP</td>
                                <td>
                                    <span class="badge bg-{{ $product->type == 'sale' ? 'success' : 'info' }}">
                                        {{ $product->type == 'sale' ? 'بيع' : 'إيجار' }}
                                    </span>
                                 </td>
                                <td>
                                    <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->quantity }}
                                    </span>
                                 </td>
                                <td>{{ $product->owner->name ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ number_format($product->view_count) }}</span>
                                 </td>
                                <td>
                                    <span class="badge bg-warning">{{ number_format($product->pay_count) }}</span>
                                 </td>
                                <td>
                                    <div class="text-nowrap">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star fa-xs {{ $i <= round($product->rate) ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                        ({{ number_format($product->rate, 1) }})
                                    </div>
                                 </td>
                                <td>
                                    <span class="badge bg-{{ $product->status == '1' ? 'success' : 'danger' }}">
                                        {{ $product->status == '1' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                 </td>
                                <td>
                                    <small>{{ $product->created_at->format('Y-m-d') }}</small>
                                 </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                 </td>
                            </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                        لا توجد منتجات
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(isset($products) && method_exists($products, 'links'))
                    <div class="d-flex justify-content-center mt-3">
                        {{ $products->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, .card-header .btn, .no-print, form, .pagination {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table {
            font-size: 10px;
        }
    }
    .progress {
        height: 6px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('#productsTable').DataTable({
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