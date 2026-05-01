{{-- resources/views/layouts/navigation.blade.php --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand fw-bold" href="{{ url('/') }}">
            <i class="bi bi-shop me-2"></i>
            {{ config('app.name', 'Tasleem') }}
        </a>
        
        <!-- Hamburger Button (للشاشات الصغيرة) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- القائمة الرئيسية -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <!-- Dashboard Link - حسب نوع المستخدم -->
                @auth
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>
                                لوحة التحكم
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
                               href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i>
                                الرئيسية
                            </a>
                        </li>
                    @endif

                    <!-- Admin Links -->
                    @if(auth()->user()->role === 'admin')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" 
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear me-1"></i>
                                الإدارة
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.users.index') }}">
                                        <i class="bi bi-people me-2"></i>المستخدمين
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.products.index') }}">
                                        <i class="bi bi-box me-2"></i>المنتجات
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.categories.index') }}">
                                        <i class="bi bi-tags me-2"></i>التصنيفات
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.orders.index') }}">
                                        <i class="bi bi-cart me-2"></i>الطلبات
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.rentals.index') }}">
                                        <i class="bi bi-calendar me-2"></i>التأجير
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.payments.index') }}">
                                        <i class="bi bi-credit-card me-2"></i>المدفوعات
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.reports.index') }}">
                                        <i class="bi bi-graph-up me-2"></i>التقارير
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.logs.index') }}">
                                        <i class="bi bi-journal-text me-2"></i>السجلات
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endif

                    <!-- Seller Links -->
                    @if(auth()->user()->role === 'seller')
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.products.index') }}">
                                <i class="bi bi-box me-1"></i>
                                منتجاتي
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.orders.index') }}">
                                <i class="bi bi-truck me-1"></i>
                                طلباتي
                            </a>
                        </li>
                    @endif

                    <!-- User Links (لجميع المستخدمين) -->
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-search me-1"></i>
                            بحث
                        </a>
                    </li>
                @endauth
            </ul>

            <!-- القائمة اليمنى (المستخدم) -->
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            تسجيل الدخول
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">
                            <i class="bi bi-person-plus me-1"></i>
                            تسجيل جديد
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" 
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(auth()->user()->user_photo)
                                <img src="{{ asset('storage/' . auth()->user()->user_photo) }}" 
                                     class="rounded-circle me-2" width="30" height="30" 
                                     alt="{{ auth()->user()->name }}">
                            @else
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 30px; height: 30px;">
                                    <i class="bi bi-person-fill text-primary"></i>
                                </div>
                            @endif
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person me-2"></i>الملف الشخصي
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-heart me-2"></i>المفضلة
                                    <span class="badge bg-danger rounded-pill float-end">0</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bi bi-cart3 me-2"></i>السلة
                                    <span class="badge bg-primary rounded-pill float-end">0</span>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

@push('styles')
<style>
    /* تخصيصات إضافية للـ navbar */
    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
        direction: rtl;
    }
    
    .navbar-brand {
        font-size: 1.5rem;
    }
    
    .nav-link {
        font-weight: 500;
        padding: 0.5rem 1rem !important;
        transition: all 0.3s ease;
    }
    
    .nav-link:hover {
        background-color: rgba(255,255,255,0.1);
        border-radius: 5px;
    }
    
    .nav-link.active {
        background-color: rgba(255,255,255,0.2);
        border-radius: 5px;
    }
    
    .dropdown-menu {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        border-radius: 10px;
        margin-top: 0.5rem;
    }
    
    .dropdown-item {
        padding: 0.7rem 1.5rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(-5px);
    }
    
    .dropdown-item i {
        width: 20px;
        text-align: center;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.35rem 0.65rem;
    }
    
    /* للشاشات الصغيرة */
    @media (max-width: 991.98px) {
        .navbar-nav {
            padding: 1rem 0;
        }
        
        .nav-link {
            padding: 0.75rem 1rem !important;
        }
        
        .dropdown-menu {
            background-color: rgba(255,255,255,0.1);
            border: none;
            margin-right: 1rem;
        }
        
        .dropdown-item {
            color: white !important;
        }
        
        .dropdown-item:hover {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // تفعيل Bootstrap dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl)
        });
    });
</script>
@endpush