{{-- resources/views/admin/rentals/show.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تفاصيل عقد الإيجار #' . $rental->rental_id)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-file-contract"></i> تفاصيل عقد الإيجار #{{ $rental->rental_id }}
        </h6>
        <div>
            <a href="{{ route('admin.rentals.print', $rental) }}" class="btn btn-secondary btn-sm" target="_blank">
                <i class="fas fa-print"></i> طباعة
            </a>
            <a href="{{ route('admin.rentals.contract', $rental) }}" class="btn btn-success btn-sm" target="_blank">
                <i class="fas fa-file-signature"></i> عقد إيجار
            </a>
            <a href="{{ route('admin.rentals.edit', $rental) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.rentals.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- حالة العقد -->
        <div class="alert alert-info mb-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <strong>حالة العقد الحالية:</strong>
                    <span class="badge bg-{{ 
                        $rental->status == 'completed' ? 'success' : 
                        ($rental->status == 'active' ? 'primary' : 
                        ($rental->status == 'pending' ? 'warning' : 
                        ($rental->status == 'confirmed' ? 'info' : 'danger'))) 
                    }} fs-6 ms-2">
                        @if($rental->status == 'pending') ⏳ قيد الانتظار
                        @elseif($rental->status == 'confirmed') ✅ مؤكد
                        @elseif($rental->status == 'active') ▶️ نشط
                        @elseif($rental->status == 'completed') ✔️ مكتمل
                        @elseif($rental->status == 'cancelled') ❌ ملغي
                        @endif
                    </span>
                    
                    @if($isOverdue ?? false)
                        <span class="badge bg-danger ms-2">
                            <i class="fas fa-exclamation-triangle"></i> منتهي
                        </span>
                    @elseif(($daysRemaining ?? 0) > 0 && ($daysRemaining ?? 0) <= 3)
                        <span class="badge bg-warning ms-2">
                            <i class="fas fa-hourglass-half"></i> ينتهي بعد {{ $daysRemaining }} يوم
                        </span>
                    @endif
                </div>
                <div class="col-md-6">
                    <form action="{{ route('admin.rentals.update-status', $rental) }}" method="POST" class="d-flex gap-2 justify-content-end">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select w-auto">
                            <option value="pending" {{ $rental->status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="confirmed" {{ $rental->status == 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                            <option value="active" {{ $rental->status == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="completed" {{ $rental->status == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ $rental->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                        <button type="submit" class="btn btn-primary">تحديث الحالة</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- معلومات العقد -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle"></i> معلومات العقد
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr><th width="40%">رقم العقد:</th><td><strong>#{{ $rental->rental_id }}</strong></tr>
                            <tr><th>فترة الإيجار:</th><td>
                                <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($rental->start_date)->format('Y-m-d') }}<br>
                                <i class="fas fa-calendar-check"></i> {{ \Carbon\Carbon::parse($rental->end_date)->format('Y-m-d') }}
                            </tr>
                            <tr><th>المدة:</th><td>{{ $rental->total_days }} يوم</tr>
                            <tr><th>السعر اليومي:</th><td>{{ number_format($rental->daily_price, 2) }} EGP</tr>
                            <tr><th>الإجمالي:</th><td><strong class="text-success h5">{{ number_format($rental->total_price, 2) }} EGP</strong></tr>
                            <tr><th>تاريخ الإنشاء:</th><td>{{ $rental->created_at->format('Y-m-d h:i A') }}</tr>
                            <tr><th>آخر تحديث:</th><td>{{ $rental->updated_at->format('Y-m-d h:i A') }}</tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- معلومات المستأجر -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-user"></i> معلومات المستأجر
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            @if($rental->renter && $rental->renter->user_photo)
                                <img src="{{ $rental->renter->user_photo }}" 
                                     class="rounded-circle me-3" 
                                     width="60" height="60"
                                     style="object-fit: cover;">
                            @else
                                <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center me-3"
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-user fa-2x text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-0">{{ $rental->renter->name ?? 'غير محدد' }}</h5>
                                <p class="mb-0 text-muted">{{ $rental->renter->email ?? '' }}</p>
                                <p class="mb-0"><i class="fas fa-phone"></i> {{ $rental->renter->phone ?? '—' }}</p>
                            </div>
                        </div>
                        <hr>
                        <p><i class="fas fa-map-marker-alt"></i> <strong>العنوان:</strong> {{ $rental->renter->address ?? '—' }}</p>
                        <p><i class="fas fa-city"></i> <strong>المدينة:</strong> {{ $rental->renter->city ?? '—' }}</p>
                        <a href="{{ route('admin.users.show', $rental->renter) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> عرض ملف المستأجر
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- معلومات المنتج -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-box"></i> معلومات المنتج
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            @if($rental->product && $rental->product->primary_image)
                                <img src="{{ $rental->product->primary_image }}" 
                                     class="rounded me-3" 
                                     width="100" height="100"
                                     style="object-fit: cover;">
                            @endif
                            <div>
                                <h5>{{ $rental->product->name ?? 'غير محدد' }}</h5>
                                <p class="text-muted">{{ Str::limit($rental->product->description ?? '', 100) }}</p>
                                <p><i class="fas fa-store"></i> <strong>المالك:</strong> {{ $rental->product->owner->name ?? '—' }}</p>
                                <a href="{{ route('admin.products.show', $rental->product) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> عرض المنتج
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات الدفع -->
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <i class="fas fa-credit-card"></i> معلومات الدفع
                    </div>
                    <div class="card-body">
                        @if($rental->payment)
                            <table class="table table-borderless">
                                <tr><th width="40%">حالة الدفع:</th>
                                    <td>
                                        <span class="badge bg-{{ $rental->payment->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ $rental->payment->status == 'completed' ? 'مدفوع' : 'قيد الانتظار' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr><th>المبلغ المدفوع:</th><td>{{ number_format($rental->payment->amount, 2) }} EGP</tr>
                                <tr><th>طريقة الدفع:</th><td>{{ $rental->payment->payment_method ?? '—' }}</tr>
                                <tr><th>رقم المعاملة:</th><td><code>{{ $rental->payment->transaction_id ?? '—' }}</code></tr>
                                <tr><th>تاريخ الدفع:</th><td>{{ $rental->payment->created_at->format('Y-m-d h:i A') }}</tr>
                            </table>
                        @else
                            <div class="alert alert-warning text-center mb-0">
                                <i class="fas fa-info-circle"></i>
                                لم يتم تسجيل أي دفعة لهذا العقد بعد
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- تأجيرات سابقة لنفس المستأجر -->
        @if($userRentals && $userRentals->count() > 0)
        <div class="mt-4">
            <h5 class="text-primary">
                <i class="fas fa-history"></i> عقود إيجار سابقة لنفس المستأجر
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>رقم العقد</th>
                            <th>المنتج</th>
                            <th>الفترة</th>
                            <th>الإجمالي</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($userRentals as $prevRental)
                        <tr>
                            <td>#{{ $prevRental->rental_id }}</td>
                            <td>{{ Str::limit($prevRental->product->name ?? '—', 30) }}</td>
                            <td>{{ \Carbon\Carbon::parse($prevRental->start_date)->format('Y-m-d') }} → {{ \Carbon\Carbon::parse($prevRental->end_date)->format('Y-m-d') }}</td>
                            <td>{{ number_format($prevRental->total_price, 2) }} EGP</td>
                            <td>
                                <span class="badge bg-{{ $prevRental->status == 'completed' ? 'success' : 'warning' }}">
                                    {{ $prevRental->status }}
                                </span>
                             </td>
                            <td>
                                <a href="{{ route('admin.rentals.show', $prevRental) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                             </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection