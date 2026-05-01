<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Payment;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * Main reports dashboard.
     */
    public function index()
    {
        // إحصائيات سريعة
        $stats = [
            'total_users' => User::count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_rentals' => Rental::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'daily_revenue' => Payment::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];

        // بيانات الرسم البياني لآخر 12 شهر
        $revenueData = $this->getMonthlyRevenueData();
        $ordersData = $this->getMonthlyOrdersData();

        // أفضل المنتجات
        $topProducts = Product::with('owner')
            ->orderBy('pay_count', 'desc')
            ->limit(5)
            ->get();

        // أفضل البائعين
        $topSellers = User::withCount(['products', 'orders as total_sales' => function($q) {
                $q->select(DB::raw('SUM(total_price)'));
            }])
            ->orderBy('total_sales', 'desc')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact('stats', 'revenueData', 'ordersData', 'topProducts', 'topSellers'));
    }

    /**
     * Show sales report.
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->get('status');
        $productId = $request->get('product_id');

        $query = Order::with(['user', 'product'])->whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $orders = $query->latest()->get();

        $totalSales = $orders->sum('total_price');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // إحصائيات إضافية
        $stats = [
            'pending' => $orders->where('status', 'pending')->count(),
            'confirmed' => $orders->where('status', 'confirmed')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
            'cancelled' => $orders->where('status', 'cancelled')->count(),
        ];

        $products = Product::all();

        return view('admin.reports.sales', compact(
            'orders', 'totalSales', 'totalOrders', 'averageOrderValue',
            'startDate', 'endDate', 'status', 'productId', 'stats', 'products'
        ));
    }

    /**
     * Show rentals report.
     */
    public function rentals(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->get('status');
        $productId = $request->get('product_id');

        $query = Rental::with(['product', 'renter'])->whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $query->where('status', $status);
        }
        if ($productId) {
            $query->where('product_id', $productId);
        }

        $rentals = $query->latest()->get();

        $totalRentalsRevenue = $rentals->sum('total_price');
        $rentalCount = $rentals->count();
        $averageRentalValue = $rentalCount > 0 ? $totalRentalsRevenue / $rentalCount : 0;
        $totalDaysRented = $rentals->sum('total_days');

        $stats = [
            'pending' => $rentals->where('status', 'pending')->count(),
            'confirmed' => $rentals->where('status', 'confirmed')->count(),
            'active' => $rentals->where('status', 'active')->count(),
            'completed' => $rentals->where('status', 'completed')->count(),
            'cancelled' => $rentals->where('status', 'cancelled')->count(),
        ];

        $products = Product::where('type', 'rental')->get();

        return view('admin.reports.rentals', compact(
            'rentals', 'totalRentalsRevenue', 'rentalCount', 'averageRentalValue',
            'totalDaysRented', 'startDate', 'endDate', 'status', 'productId', 'stats', 'products'
        ));
    }

    /**
     * Show users report.
     */
    public function users(Request $request)
    {
        $role = $request->get('role');
        $status = $request->get('status');

        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(20);

        $totalUsers = User::count();
        $activeUsers = User::where('status', '1')->count();
        $inactiveUsers = User::where('status', '0')->count();
        $newUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        
        $usersByRole = User::select('role', DB::raw('count(*) as total'))
            ->groupBy('role')
            ->get();

        $usersByMonth = User::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return view('admin.reports.users', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'newUsers',
            'usersByRole', 'usersByMonth', 'users', 'role', 'status'
        ));
    }

    /**
     * Show products report.
     */
    public function products(Request $request)
    {
        $categoryId = $request->get('category_id');
        $type = $request->get('type');

        $query = Product::with(['category', 'owner']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        if ($type) {
            $query->where('type', $type);
        }

        $products = $query->latest()->paginate(20);

        $totalProducts = Product::count();
        $activeProducts = Product::where('status', '1')->count();
        $inactiveProducts = Product::where('status', '0')->count();
        $saleProducts = Product::where('type', 'sale')->count();
        $rentalProducts = Product::where('type', 'rental')->count();
        
        $topProducts = Product::orderBy('pay_count', 'desc')->take(10)->get();
        $topRated = Product::orderBy('rate', 'desc')->take(10)->get();
        $mostViewed = Product::orderBy('view_count', 'desc')->take(10)->get();
        
        $categories = Category::all();

        return view('admin.reports.products', compact(
            'totalProducts', 'activeProducts', 'inactiveProducts',
            'saleProducts', 'rentalProducts', 'topProducts',
            'topRated', 'mostViewed', 'products', 'categories', 'categoryId', 'type'
        ));
    }

    /**
     * Show revenue report.
     */
    public function revenue(Request $request)
    {
        $year = $request->get('year', now()->year);
        $years = range(2020, now()->year);

        $monthlyRevenue = DB::table('payments')
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(amount) as total'))
            ->whereYear('created_at', $year)
            ->where('status', 'completed')
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // إعداد بيانات الرسم البياني
        $revenueData = [];
        for ($i = 1; $i <= 12; $i++) {
            $found = $monthlyRevenue->firstWhere('month', $i);
            $revenueData[] = $found ? $found->total : 0;
        }

        // إجمالي الإيرادات للسنة
        $totalRevenue = array_sum($revenueData);

        // إيرادات اليوم
        $dailyRevenue = Payment::where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('amount');

        // إيرادات الشهر الحالي
        $currentMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        // توزيع الإيرادات حسب طريقة الدفع
        $paymentMethods = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return view('admin.reports.revenue', compact(
            'revenueData', 'year', 'years', 'totalRevenue',
            'dailyRevenue', 'currentMonthRevenue', 'paymentMethods'
        ));
    }

    /**
     * Show financial summary report.
     */
/**
 * Show financial summary report.
 */
/**
 * Show financial summary report.
 */
/**
 * Show financial summary report.
 */
/**
 * Show financial summary report.
 */
public function financial(Request $request)
{
    try {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfYear()->format('Y-m-d'));

        // ✅ إيرادات المبيعات
        $salesRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->sum('total_price');

        // ✅ إيرادات الإيجار
        $rentalsRevenue = Rental::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->sum('total_price');

        // ✅ إجمالي الإيرادات
        $totalRevenue = $salesRevenue + $rentalsRevenue;

        // ✅ عدد المعاملات
        $salesCount = Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $rentalsCount = Rental::whereBetween('created_at', [$startDate, $endDate])->count();

        // ✅ متوسط قيمة المعاملة
        $avgOrderValue = $salesCount > 0 ? $salesRevenue / $salesCount : 0;

        // ✅ العمولات
        $commissionRate = 0.10;
        $totalCommission = $totalRevenue * $commissionRate;

        // ✅ استخدم paginate() بدلاً من simplePaginate()
        $salesOrders = Order::with(['user', 'product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'sales_page');

        $rentalContracts = Rental::with(['renter', 'product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'rentals_page');

        return view('admin.reports.financial', compact(
            'startDate', 'endDate', 'salesRevenue', 'rentalsRevenue',
            'totalRevenue', 'salesCount', 'rentalsCount', 'avgOrderValue',
            'commissionRate', 'totalCommission', 'salesOrders', 'rentalContracts'
        ));
    } catch (\Exception $e) {
        return back()->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}

    /**
     * Export report to CSV/Excel.
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'sales');
        $format = $request->get('format', 'csv');
        
        switch ($type) {
            case 'sales':
                return $this->exportSales($request, $format);
            case 'rentals':
                return $this->exportRentals($request, $format);
            case 'users':
                return $this->exportUsers($format);
            case 'products':
                return $this->exportProducts($format);
            default:
                return redirect()->back()->with('error', 'نوع التقرير غير صحيح');
        }
    }

    private function exportSales($request, $format)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $orders = Order::with(['user', 'product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = "sales_report_{$startDate}_to_{$endDate}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // BOM for Arabic

            fputcsv($file, [
                'رقم الطلب', 'العميل', 'المنتج', 'الكمية',
                'سعر الوحدة', 'الإجمالي', 'الحالة', 'التاريخ'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_id,
                    $order->user->name ?? 'غير محدد',
                    $order->product->name ?? 'غير محدد',
                    $order->quantity,
                    number_format($order->unit_price, 2),
                    number_format($order->total_price, 2),
                    $order->status,
                    $order->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportRentals($request, $format)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $rentals = Rental::with(['product', 'renter'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $filename = "rentals_report_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($rentals) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'رقم العقد', 'المستأجر', 'المنتج', 'فترة الإيجار',
                'عدد الأيام', 'السعر اليومي', 'الإجمالي', 'الحالة', 'التاريخ'
            ]);

            foreach ($rentals as $rental) {
                fputcsv($file, [
                    $rental->rental_id,
                    $rental->renter->name ?? 'غير محدد',
                    $rental->product->name ?? 'غير محدد',
                    $rental->start_date . ' إلى ' . $rental->end_date,
                    $rental->total_days,
                    number_format($rental->daily_price, 2),
                    number_format($rental->total_price, 2),
                    $rental->status,
                    $rental->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportUsers($format)
    {
        $users = User::all();

        $filename = "users_report_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID', 'الاسم', 'البريد الإلكتروني', 'الهاتف',
                'المدينة', 'الدور', 'الحالة', 'تاريخ التسجيل'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? '—',
                    $user->city ?? '—',
                    $user->role == 'admin' ? 'مدير' : 'مستخدم',
                    $user->status == '1' ? 'نشط' : 'غير نشط',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportProducts($format)
    {
        $products = Product::with(['category', 'owner'])->get();

        $filename = "products_report_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID', 'اسم المنتج', 'السعر', 'النوع', 'الكمية',
                'المالك', 'التصنيف', 'المشاهدات', 'المبيعات', 'التقييم', 'الحالة'
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    number_format($product->price, 2),
                    $product->type == 'sale' ? 'بيع' : 'إيجار',
                    $product->quantity,
                    $product->owner->name ?? 'غير محدد',
                    $product->category->name ?? 'غير محدد',
                    $product->view_count,
                    $product->pay_count,
                    number_format($product->rate, 1),
                    $product->status == '1' ? 'نشط' : 'غير نشط',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getMonthlyRevenueData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            
            $data['months'][] = $month->format('M Y');
            $data['revenue'][] = $revenue;
        }
        return $data;
    }

    private function getMonthlyOrdersData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $data['months'][] = $month->format('M Y');
            $data['orders'][] = $count;
        }
        return $data;
    }
}