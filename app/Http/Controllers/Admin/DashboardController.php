<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Rental;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role !== 'admin') {
            abort(403, 'You are not authorized to access');
        }
        
       
        $stats = [
            'total_users' => User::count(),
            'total_sellers' => User::where('role', 'seller')->count(),
            'total_customers' => User::where('role', 'user')->count(),
            'total_products' => Product::count(),
            'available_products' => Product::where('status', '1')->count(),
            'sold_products' => Product::where('type', 'sale')->count(),
            'rented_products' => Product::where('type', 'rental')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
            'total_rentals' => Rental::count(),
            'active_rentals' => Rental::where('status', 'active')->count(),
            'completed_rentals' => Rental::where('status', 'completed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'today_revenue' => Payment::where('status', 'completed')
                                ->whereDate('created_at', Carbon::today())
                                ->sum('amount'),
            'month_revenue' => Payment::where('status', 'completed')
                                ->whereMonth('created_at', Carbon::now()->month)
                                ->sum('amount'),
            'total_reviews' => Review::count(),
            'avg_rating' => Review::avg('rating') ?? 0,
        ];
        
       
        $recent_users = User::orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
        
      
        $recent_orders = Order::with('user', 'product')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
        
    
        $recent_rentals = Rental::with('renter', 'product')
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
        
     
        $top_products = Product::orderBy('pay_count', 'desc')
                            ->with('owner')
                            ->take(5)
                            ->get();
        

        $active_users = User::select('users.*')
            ->selectSub(function($query) {
                $query->from('orders')
                    ->whereColumn('users.id', 'orders.user_id')
                    ->selectRaw('count(*)');
            }, 'orders_count')
            ->selectSub(function($query) {
                $query->from('rentals')
                    ->whereColumn('users.id', 'rentals.renter_id')
                    ->selectRaw('count(*)');
            }, 'rentals_count')
            ->having('orders_count', '>', 0)
            ->orHaving('rentals_count', '>', 0)
            ->orderBy('orders_count', 'desc')
            ->orderBy('rentals_count', 'desc')
            ->take(5)
            ->get();
        
   
        $data = compact(
            'stats',
            'recent_users',
            'recent_orders',
            'recent_rentals',
            'top_products',
            'active_users'
        );
        
      
        $data['chart_data'] = $this->getChartData();
        $data['revenue_chart'] = $this->getRevenueChart();
        $data['popular_categories'] = $this->getPopularCategories();
        
        return view('admin.dashboard', $data);
    }
    
    private function getChartData()
    {
        $months = collect(range(1, 12))->map(function($month) {
            return Carbon::create()->month($month)->format('F');
        });
        
        $ordersData = [];
        $rentalsData = [];
        
        foreach (range(1, 12) as $month) {
            $ordersData[] = Order::whereMonth('created_at', $month)
                                ->whereYear('created_at', Carbon::now()->year)
                                ->count();
                                
            $rentalsData[] = Rental::whereMonth('created_at', $month)
                                  ->whereYear('created_at', Carbon::now()->year)
                                  ->count();
        }
        
        return [
            'months' => $months,
            'orders' => $ordersData,
            'rentals' => $rentalsData,
        ];
    }
    
    private function getRevenueChart()
    {
        $months = collect(range(1, 12))->map(function($month) {
            return Carbon::create()->month($month)->format('F');
        });
        
        $revenue = [];
        
        foreach (range(1, 12) as $month) {
            $revenue[] = Payment::where('status', 'completed')
                               ->whereMonth('created_at', $month)
                               ->whereYear('created_at', Carbon::now()->year)
                               ->sum('amount');
        }
        
        return [
            'months' => $months,
            'revenue' => $revenue,
        ];
    }
    
    private function getPopularCategories()
    {
        return DB::table('categories')
            ->leftJoin('products', 'categories.category_id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('count(products.id) as total'))
            ->groupBy('categories.name')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
    }
}