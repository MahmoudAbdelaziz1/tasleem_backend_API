{{-- resources/views/admin/layouts/header.blade.php --}}
<nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-4">
    <!-- Sidebar Toggle -->
    <button class="btn btn-link d-md-none" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Navbar Content -->
    <div class="navbar-collapse collapse">
        <ul class="navbar-nav ms-auto">
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="notificationsDropdown" 
                   role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <span class="badge bg-danger rounded-pill">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown">
                    <h6 class="dropdown-header">الإشعارات</h6>
                    <a class="dropdown-item" href="#">
                        <div class="small text-gray-500">منذ 5 دقائق</div>
                        <span class="fw-bold">طلب جديد #12345</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="small text-gray-500">منذ ساعة</div>
                        <span class="fw-bold">تم تأكيد الدفع</span>
                    </a>
                    <a class="dropdown-item" href="#">
                        <div class="small text-gray-500">منذ 3 ساعات</div>
                        <span class="fw-bold">مستخدم جديد سجل</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-center small" href="#">عرض كل الإشعارات</a>
                </div>
            </li>
            
            <!-- User Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" 
                   role="button" data-bs-toggle="dropdown">
                    <img src="{{ Auth::user()->user_photo ?? 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name) }}" 
                         class="rounded-circle me-2" width="30" height="30">
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="fas fa-user fa-sm me-2"></i>
                        الملف الشخصي
                    </a>
                    {{-- <a class="dropdown-item" href="{{ route('admin.settings.index') }}"> --}}
                        <i class="fas fa-cog fa-sm me-2"></i>
                        الإعدادات
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt fa-sm me-2"></i>
                        تسجيل الخروج
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</nav>