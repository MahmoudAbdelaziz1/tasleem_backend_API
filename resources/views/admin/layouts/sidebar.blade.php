{{-- resources/views/admin/layouts/sidebar.blade.php --}}
<nav class="sidebar col-2 p-3">
    <div class="text-center mb-4">
        <h4 class="text-white">{{ config('app.name') }}</h4>
        <p class="text-white-50 small">Control Panel</p>
    </div>
    
    <hr class="bg-white">
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                Dashboard
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 px-3">Users</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.users.index') }}" 
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                All Users
            </a>
        </li>

        
        <li class="nav-item mt-3">
            <small class="text-white-50 px-3">Products</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.products.index') }}" 
               class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fas fa-boxes"></i>
                All Products
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.categories.index') }}" class="nav-link">
                <i class="fas fa-tags"></i>
                Categories
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 px-3">Orders</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.orders.index') }}" 
               class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                Purchase Orders
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.rentals.index') }}" 
               class="nav-link {{ request()->routeIs('admin.rentals.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i>
                Rental Orders
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 px-3">Finance</small>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.payments.index') }}" class="nav-link">
                <i class="fas fa-credit-card"></i>
                Payments
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.reports.index') }}" 
               class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
        </li>
        
        <li class="nav-item mt-3">
            <small class="text-white-50 px-3">Settings</small>
        </li>
        <li class="nav-item">
<a href="#" class="nav-link" onclick="return false;">                <i class="fas fa-cog"></i>
                Settings
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.logs.index') }}" class="nav-link">
                <i class="fas fa-history"></i>
                System Logs
            </a>
        </li>
    </ul>
</nav>