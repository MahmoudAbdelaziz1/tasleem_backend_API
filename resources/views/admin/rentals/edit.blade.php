{{-- resources/views/admin/rentals/edit.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'تعديل عقد الإيجار #' . $rental->rental_id)

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit"></i> تعديل عقد الإيجار #{{ $rental->rental_id }}
        </h6>
        <div>
            <a href="{{ route('admin.rentals.show', $rental) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> عرض العقد
            </a>
            <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> رجوع
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.rentals.update', $rental) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="product_id" class="form-label">
                        المنتج <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('product_id') is-invalid @enderror" 
                            id="product_id" 
                            name="product_id" 
                            required>
                        <option value="">اختر المنتج</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" 
                                data-price="{{ $product->price }}"
                                {{ old('product_id', $rental->product_id) == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - {{ number_format($product->price, 2) }} EGP/يوم
                                (المتاح: {{ $product->quantity }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="renter_id" class="form-label">
                        المستأجر <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('renter_id') is-invalid @enderror" 
                            id="renter_id" 
                            name="renter_id" 
                            required>
                        <option value="">اختر المستأجر</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('renter_id', $rental->renter_id) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('renter_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">
                        تاريخ بدء الإيجار <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('start_date') is-invalid @enderror" 
                           id="start_date" 
                           name="start_date" 
                           value="{{ old('start_date', $rental->start_date->format('Y-m-d')) }}" 
                           required>
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">
                        تاريخ انتهاء الإيجار <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control @error('end_date') is-invalid @enderror" 
                           id="end_date" 
                           name="end_date" 
                           value="{{ old('end_date', $rental->end_date->format('Y-m-d')) }}" 
                           required>
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="daily_price" class="form-label">
                        السعر اليومي <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="number" 
                               step="0.01" 
                               class="form-control @error('daily_price') is-invalid @enderror" 
                               id="daily_price" 
                               name="daily_price" 
                               value="{{ old('daily_price', $rental->daily_price) }}" 
                               required>
                        <span class="input-group-text">EGP/يوم</span>
                    </div>
                    @error('daily_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">
                        حالة العقد <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="pending" {{ old('status', $rental->status) == 'pending' ? 'selected' : '' }}>⏳ قيد الانتظار</option>
                        <option value="confirmed" {{ old('status', $rental->status) == 'confirmed' ? 'selected' : '' }}>✅ مؤكد</option>
                        <option value="active" {{ old('status', $rental->status) == 'active' ? 'selected' : '' }}>▶️ نشط</option>
                        <option value="completed" {{ old('status', $rental->status) == 'completed' ? 'selected' : '' }}>✔️ مكتمل</option>
                        <option value="cancelled" {{ old('status', $rental->status) == 'cancelled' ? 'selected' : '' }}>❌ ملغي</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- ملخص العقد -->
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-calculator"></i> ملخص العقد
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>عدد الأيام:</strong>
                            <span id="total_days_display" class="text-primary">{{ $rental->total_days }}</span> يوم
                        </div>
                        <div class="col-md-4">
                            <strong>السعر اليومي:</strong>
                            <span id="daily_price_display" class="text-primary">{{ number_format($rental->daily_price, 2) }}</span> EGP
                        </div>
                        <div class="col-md-4">
                            <strong>الإجمالي:</strong>
                            <span id="total_price_display" class="text-success h5">{{ number_format($rental->total_price, 2) }}</span> EGP
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> تحديث العقد
                </button>
                <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
                <button type="button" class="btn btn-danger float-end" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> حذف العقد
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد حذف العقد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>هل أنت متأكد من حذف عقد الإيجار <strong>#{{ $rental->rental_id }}</strong>؟</p>
                    <p class="text-danger small">
                        <i class="fas fa-info-circle"></i> 
                        سيتم حذف جميع بيانات العقد نهائياً!
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form action="{{ route('admin.rentals.destroy', $rental) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> حذف نهائي
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function calculateTotal() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const dailyPrice = parseFloat(document.getElementById('daily_price').value) || 0;
        
        if (startDate && endDate && dailyPrice > 0) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            
            const total = diffDays * dailyPrice;
            
            document.getElementById('total_days_display').textContent = diffDays;
            document.getElementById('total_price_display').textContent = total.toFixed(2);
        } else {
            document.getElementById('total_days_display').textContent = '0';
            document.getElementById('total_price_display').textContent = '0.00';
        }
    }
    
    document.getElementById('start_date').addEventListener('change', calculateTotal);
    document.getElementById('end_date').addEventListener('change', calculateTotal);
    document.getElementById('daily_price').addEventListener('input', calculateTotal);
    
    document.getElementById('product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('daily_price').value = price;
            calculateTotal();
        }
    });
    
    document.querySelector('form').addEventListener('submit', function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
            e.preventDefault();
            alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
        }
    });
</script>
@endpush