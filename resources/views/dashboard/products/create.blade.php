{{-- resources/views/dashboard/products/create.blade.php --}}
@extends('layouts.app')

@section('title', 'إضافة منتج جديد')

@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-hover: #4338ca;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --gray-50: #f9fafb;
        --gray-200: #e5e7eb;
        --gray-400: #9ca3af;
        --gray-600: #4b5563;
        --gray-800: #1f2937;
    }
    
    [dir="rtl"] { direction: rtl; text-align: right; }
    
    .form-card {
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        border: none;
        background: #fff;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--gray-800);
        margin-bottom: 0.5rem;
    }
    
    .form-label .required { color: var(--danger); margin-right: 0.25rem; }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid var(--gray-200);
        padding: 0.625rem 0.875rem;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
        outline: none;
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: var(--danger);
        padding-right: calc(1.5em + 1.25rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef4444' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.3125rem) center;
        background-size: calc(0.75em + 0.625rem) calc(0.75em + 0.625rem);
    }
    
    [dir="rtl"] .form-control.is-invalid,
    [dir="rtl"] .form-select.is-invalid {
        padding-left: calc(1.5em + 1.25rem);
        padding-right: 0.875rem;
        background-position: left calc(0.375em + 0.3125rem) center;
    }
    
    .invalid-feedback {
        font-size: 0.8rem;
        color: var(--danger);
        margin-top: 0.25rem;
    }
    
    /* Image Upload */
    .image-upload-area {
        border: 2px dashed var(--gray-200);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--gray-50);
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .image-upload-area:hover,
    .image-upload-area.dragover {
        border-color: var(--primary);
        background: rgba(79,70,229,0.05);
    }
    
    .image-upload-area i {
        font-size: 2.5rem;
        color: var(--gray-400);
    }
    
    .image-upload-area .text {
        color: var(--gray-600);
        font-size: 0.9rem;
    }
    
    .image-upload-area .hint {
        color: var(--gray-400);
        font-size: 0.75rem;
    }
    
    .image-preview-container {
        display: none;
        position: relative;
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }
    
    .image-preview-container.show { display: block; }
    
    .image-preview {
        width: 100%;
        height: 180px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid var(--primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .image-remove {
        position: absolute;
        top: -8px;
        left: -8px;
        [dir="rtl"] & { left: auto; right: -8px; }
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--danger);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.8rem;
        transition: transform 0.2s;
    }
    
    .image-remove:hover { transform: scale(1.1); }
    
    /* Multiple Images */
    .images-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    .image-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid var(--gray-200);
    }
    
    .image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .image-item .remove {
        position: absolute;
        top: 4px;
        right: 4px;
        [dir="rtl"] & { right: auto; left: 4px; }
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(239,68,68,0.9);
        color: white;
        border: none;
        font-size: 0.7rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Type Selection */
    .type-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .type-card {
        position: relative;
        border: 2px solid var(--gray-200);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        background: #fff;
    }
    
    .type-card:hover {
        border-color: var(--primary);
        background: rgba(79,70,229,0.05);
    }
    
    .type-card input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    
    .type-card input:checked + .type-content {
        color: var(--primary);
    }
    
    .type-card input:checked ~ .type-content {
        color: var(--primary);
    }
    
    .type-card:has(input:checked) {
        border-color: var(--primary);
        background: rgba(79,70,229,0.05);
        box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
    }
    
    .type-icon {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
        display: block;
    }
    
    .type-name {
        font-weight: 500;
        font-size: 0.9rem;
    }
    
    /* Price Input */
    .price-input-group {
        position: relative;
    }
    
    .price-input-group .currency {
        position: absolute;
        left: 1rem;
        [dir="rtl"] & { left: auto; right: 1rem; }
        color: var(--gray-400);
        font-weight: 500;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    
    .price-input-group input {
        padding-right: 2.5rem;
        [dir="rtl"] & { padding-right: 0.875rem; padding-left: 2.5rem; }
    }
    
    /* Submit Button */
    .btn-submit {
        background: var(--primary);
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s;
    }
    
    .btn-submit:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79,70,229,0.3);
    }
    
    .btn-submit:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .type-cards { grid-template-columns: 1fr; }
        .images-grid { grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-plus-circle text-primary me-2"></i>
                إضافة منتج جديد
            </h2>
            <p class="text-muted mb-0 small">املأ البيانات التالية لإضافة منتجك إلى المتجر</p>
        </div>
        <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <!-- Form Card -->
    <div class="form-card card">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('dashboard.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                @csrf
                
                <div class="row g-4">
                    
                    <!-- 🔹 اسم المنتج -->
                    <div class="col-12">
                        <label class="form-label" for="name">
                            اسم المنتج <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               placeholder="أدخل اسم المنتج"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 🔹 الوصف -->
                    <div class="col-12">
                        <label class="form-label" for="description">
                            وصف المنتج
                        </label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="أضف وصفاً تفصيلياً للمنتج...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">يمكنك استخدام هذا الحقل لشرح ميزات المنتج وتفاصيله</small>
                    </div>

                    <!-- 🔹 النوع والسعر والكمية -->
                    <div class="col-md-4">
                        <label class="form-label">
                            نوع المنتج <span class="required">*</span>
                        </label>
                        <div class="type-cards">
                            <label class="type-card">
                                <input type="radio" name="type" value="sale" {{ old('type') == 'sale' ? 'checked' : '' }} required>
                                <div class="type-content">
                                    <i class="bi bi-cart type-icon text-success"></i>
                                    <span class="type-name">بيع</span>
                                </div>
                            </label>
                            <label class="type-card">
                                <input type="radio" name="type" value="rental" {{ old('type') == 'rental' ? 'checked' : '' }}>
                                <div class="type-content">
                                    <i class="bi bi-calendar-week type-icon text-info"></i>
                                    <span class="type-name">إيجار</span>
                                </div>
                            </label>
                            <label class="type-card">
                                <input type="radio" name="type" value="both" {{ old('type') == 'both' ? 'checked' : '' }}>
                                <div class="type-content">
                                    <i class="bi bi-arrow-left-right type-icon text-primary"></i>
                                    <span class="type-name">كلاهما</span>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="price">
                            السعر <span class="required">*</span>
                        </label>
                        <div class="price-input-group">
                            <input type="number" 
                                   class="form-control @error('price') is-invalid @enderror" 
                                   id="price" 
                                   name="price" 
                                   value="{{ old('price') }}"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   required>
                            <span class="currency">$</span>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label" for="quantity">
                            الكمية المتاحة <span class="required">*</span>
                        </label>
                        <input type="number" 
                               class="form-control @error('quantity') is-invalid @enderror" 
                               id="quantity" 
                               name="quantity" 
                               value="{{ old('quantity', 1) }}"
                               placeholder="1"
                               min="1"
                               required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 🔹 التصنيف -->
                    <div class="col-md-6">
                        <label class="form-label" for="category_id">
                            التصنيف <span class="required">*</span>
                        </label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" 
                                name="category_id"
                                required>
                            <option value="">اختر تصنيفاً...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->category_id }}" 
                                        {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- 🔹 الصورة الرئيسية -->
                    <div class="col-md-6">
                        <label class="form-label">الصورة الرئيسية</label>
                        <input type="file" 
                               class="form-control @error('images.0') is-invalid @enderror" 
                               id="mainImage" 
                               name="images[]"
                               accept="image/*"
                               onchange="previewImage(this, 'mainPreview')">
                        @error('images.0')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        <!-- Preview Area -->
                        <div class="image-preview-container mt-3" id="mainPreviewContainer">
                            <img src="" alt="معاينة" class="image-preview" id="mainPreview">
                            <button type="button" class="image-remove" onclick="removeImage('mainPreviewContainer', 'mainImage')">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 🔹 صور إضافية -->
                    <div class="col-12">
                        <label class="form-label">صور إضافية (اختياري)</label>
                        <div class="image-upload-area" id="extraImagesArea" onclick="document.getElementById('extraImages').click()">
                            <i class="bi bi-images"></i>
                            <span class="text">اضغط لإضافة صور إضافية</span>
                            <span class="hint">PNG, JPG, GIF - حتى 2 ميجابايت لكل صورة</span>
                            <input type="file" 
                                   id="extraImages" 
                                   name="images[]"
                                   accept="image/*"
                                   multiple
                                   class="d-none"
                                   onchange="previewExtraImages(this)">
                        </div>
                        
                        <!-- Preview Grid -->
                        <div class="images-grid" id="extraImagesPreview"></div>
                        
                        @error('images.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-3 mt-5 pt-4 border-top">
                    <a href="{{ route('dashboard.products.index') }}" class="btn btn-outline-secondary px-4">
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-submit" id="submitBtn">
                        <i class="bi bi-check-circle"></i>
                        <span id="submitText">حفظ المنتج</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 🔹 معاينة الصورة الرئيسية
    window.previewImage = function(input, previewId) {
        const preview = document.getElementById(previewId);
        const container = document.getElementById(previewId + 'Container');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.classList.add('show');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // 🔹 إزالة الصورة الرئيسية
    window.removeImage = function(containerId, inputId) {
        document.getElementById(containerId).classList.remove('show');
        document.getElementById(inputId).value = '';
    }
    
    // 🔹 معاينة الصور الإضافية
    window.previewExtraImages = function(input) {
        const previewGrid = document.getElementById('extraImagesPreview');
        previewGrid.innerHTML = '';
        
        if (input.files) {
            Array.from(input.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const item = document.createElement('div');
                        item.className = 'image-item';
                        item.innerHTML = `
                            <img src="${e.target.result}" alt="صورة ${index + 1}">
                            <button type="button" class="remove" onclick="this.parentElement.remove()">
                                <i class="bi bi-x"></i>
                            </button>
                        `;
                        previewGrid.appendChild(item);
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    }
    
    // 🔹 Drag & Drop للصور الإضافية
    const dropArea = document.getElementById('extraImagesArea');
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
    });
    
    dropArea.addEventListener('drop', handleDrop, false);
    
    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        const input = document.getElementById('extraImages');
        
        // دمج الملفات الجديدة مع الموجودة
        const dataTransfer = new DataTransfer();
        Array.from(input.files).forEach(file => dataTransfer.items.add(file));
        Array.from(files).forEach(file => {
            if (file.type.startsWith('image/')) {
                dataTransfer.items.add(file);
            }
        });
        input.files = dataTransfer.files;
        
        // تحديث المعاينة
        previewExtraImages(input);
    }
    
    // 🔹 تحسينات تجربة المستخدم
    const form = document.getElementById('productForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    form.addEventListener('submit', function(e) {
        // تعطيل الزر لمنع الإرسال المكرر
        submitBtn.disabled = true;
        submitText.textContent = 'جاري الحفظ...';
        
        // يمكن إضافة تحقق إضافي هنا إذا لزم
    });
    
    // 🔹 تفعيل Bootstrap Tooltip إذا كان موجوداً
    if (typeof bootstrap !== 'undefined') {
        [...document.querySelectorAll('[data-bs-toggle="tooltip"]')].forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }
});
</script>
@endpush