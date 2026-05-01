<?php
// app/Http/Controllers/Admin/OrderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'product', 'payment']);
        
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
       
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->latest()->paginate(15);
        
       
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_price'),
        ];
        
        $users = User::whereHas('orders')->get();
        
        return view('admin.orders.index', compact('orders', 'stats', 'users'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'product', 'payment', 'product.owner']);
        
     
        $userOrders = Order::where('user_id', $order->user_id)
            ->where('order_id', '!=', $order->order_id)
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.orders.show', compact('order', 'userOrders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled,returned',
        ]);
        
        $oldStatus = $order->status;
        $order->update($validated);
        
       
        if ($validated['status'] == 'cancelled' && $oldStatus != 'cancelled') {
            $order->product->increment('quantity', $order->quantity);
        }
        
        
        if ($validated['status'] == 'confirmed' && $oldStatus == 'pending') {
            $order->product->decrement('quantity', $order->quantity);
        }
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order status updated successfully');
    }
    
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,order_id',
            'status' => 'required|in:pending,confirmed,shipped,delivered,cancelled,returned',
        ]);
        
        Order::whereIn('order_id', $validated['order_ids'])->update(['status' => $validated['status']]);
        
        return redirect()->route('admin.orders.index')
            ->with('success', 'Order statuses have been successfully updated');
    }
    
    public function print(Order $order)
    {
        $order->load(['user', 'product', 'product.owner']);
        return view('admin.orders.print', compact('order'));
    }
    
    public function invoice(Order $order)
    {
        $order->load(['user', 'product', 'product.owner']);
        return view('admin.orders.invoice', compact('order'));
    }
}