{{-- resources/views/admin/rentals/create.blade.php --}}
@extends('admin.layouts.master')

@section('title', 'إضافة عقد إيجار جديد')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle"></i> إضافة عقد إيجار جديد
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.rentals.store') }}" method="POST" id="rentalForm">
            @csrf
            
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
                                data-quantity="{{ $product->quantity }}"
                                data-name="{{ $product->name }}"
                                {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} - {{ number_format($product->price, 2) }} EGP/يوم
                                (المتاح: {{ $product->quantity }})
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted" id="product_availability"></small>
                </div>

                <!-- باقي الحقول كما هي ... -->
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
                            <option value="{{ $user->id }}" {{ old('renter_id') == $user->id ? 'selected' : '' }}>
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
                           value="{{ old('start_date', date('Y-m-d')) }}" 
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
                           value="{{ old('end_date') }}" 
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
                               value="{{ old('daily_price') }}" 
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
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>⏳ قيد الانتظار</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>✅ مؤكد</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>▶️ نشط</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>✔️ مكتمل</option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>❌ ملغي</option>
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
                            <span id="total_days_display" class="text-primary">0</span> يوم
                        </div>
                        <div class="col-md-4">
                            <strong>السعر اليومي:</strong>
                            <span id="daily_price_display" class="text-primary">0.00</span> EGP
                        </div>
                        <div class="col-md-4">
                            <strong>الإجمالي:</strong>
                            <span id="total_price_display" class="text-success h5">0.00</span> EGP
                        </div>
                    </div>
                </div>
            </div>

            <!-- رسائل التحذير -->
            <div id="warningMessages" class="mt-3" style="display: none;">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span id="warningText"></span>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-save"></i> إنشاء العقد
                </button>
                <a href="{{ route('admin.rentals.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let availabilityCache = {};
    
    function calculateTotal() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const dailyPrice = parseFloat(document.getElementById('daily_price').value) || 0;
        
        // تحديث عرض السعر اليومي
        document.getElementById('daily_price_display').textContent = dailyPrice.toFixed(2);
        
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
    
    // التحقق من توفر المنتج في الفترة المحددة
    async function checkProductAvailability() {
        const productId = document.getElementById('product_id').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const submitBtn = document.getElementById('submitBtn');
        const warningDiv = document.getElementById('warningMessages');
        const warningText = document.getElementById('warningText');
        
        if (!productId || !startDate || !endDate) {
            if (warningDiv) warningDiv.style.display = 'none';
            if (submitBtn) submitBtn.disabled = false;
            return true;
        }
        
        // التحقق من صحة التواريخ
        if (new Date(endDate) <= new Date(startDate)) {
            warningText.innerHTML = '⚠️ تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء';
            warningDiv.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }
        
        // التحقق من الكمية المتاحة
        const selectedOption = document.getElementById('product_id').options[document.getElementById('product_id').selectedIndex];
        const availableQuantity = parseInt(selectedOption.getAttribute('data-quantity') || 0);
        
        if (availableQuantity <= 0) {
            warningText.innerHTML = '❌ هذا المنتج غير متوفر حالياً (الكمية: 0)';
            warningDiv.style.display = 'block';
            submitBtn.disabled = true;
            return false;
        }
        
        try {
            // إظهار حالة التحميل
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';
            
            // استدعاء API للتحقق من التوفر
            const response = await fetch(`/admin/rentals/check-availability/${productId}?start_date=${startDate}&end_date=${endDate}`);
            const data = await response.json();
            
            if (data.available) {
                warningDiv.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> إنشاء العقد';
                return true;
            } else {
                warningText.innerHTML = `⚠️ ${data.message || 'المنتج غير متوفر في هذه الفترة'}`;
                warningDiv.style.display = 'block';
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> إنشاء العقد';
                return false;
            }
        } catch (error) {
            console.error('Error checking availability:', error);
            warningDiv.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> إنشاء العقد';
            return true; // السماح بالمحاولة على أي حال
        }
    }
    
    // تحديث معلومات المنتج عند الاختيار
    function updateProductInfo() {
        const selectedOption = document.getElementById('product_id').options[document.getElementById('product_id').selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        const quantity = selectedOption.getAttribute('data-quantity');
        const productName = selectedOption.getAttribute('data-name');
        const availabilitySpan = document.getElementById('product_availability');
        
        if (price) {
            document.getElementById('daily_price').value = price;
            
            if (quantity && parseInt(quantity) <= 0) {
                availabilitySpan.innerHTML = '<span class="text-danger">⚠️ هذا المنتج غير متوفر حالياً</span>';
                availabilitySpan.style.color = 'red';
            } else if (quantity) {
                availabilitySpan.innerHTML = `<span class="text-success">✓ متوفر (الكمية: ${quantity})</span>`;
                availabilitySpan.style.color = 'green';
            }
            
            calculateTotal();
        }
        
        // إعادة التحقق من التوفر
        checkProductAvailability();
    }
    
    // مستمعات الأحداث
    document.getElementById('start_date').addEventListener('change', () => {
        calculateTotal();
        checkProductAvailability();
    });
    
    document.getElementById('end_date').addEventListener('change', () => {
        calculateTotal();
        checkProductAvailability();
    });
    
    document.getElementById('daily_price').addEventListener('input', calculateTotal);
    
    // جلب سعر المنتج تلقائياً والتحقق من التوفر
    document.getElementById('product_id').addEventListener('change', updateProductInfo);
    
    // التحقق من صحة التواريخ قبل الإرسال
    document.getElementById('rentalForm').addEventListener('submit', async function(e) {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const productId = document.getElementById('product_id').value;
        
        if (!productId) {
            e.preventDefault();
            alert('الرجاء اختيار المنتج');
            return false;
        }
        
        if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
            e.preventDefault();
            alert('تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء');
            return false;
        }
        
        // التحقق النهائي من التوفر
        const isAvailable = await checkProductAvailability();
        if (!isAvailable) {
            e.preventDefault();
            alert('المنتج غير متوفر في الفترة المحددة. الرجاء اختيار فترة أخرى أو منتج آخر.');
            return false;
        }
    });
    
    // تحديث عند تحميل الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
        if (document.getElementById('product_id').value) {
            updateProductInfo();
        }
    });
</script>
@endpush