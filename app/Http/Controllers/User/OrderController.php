<?php
// app/Http/Controllers/User/OrderController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['product:id,name,price', 'product.images'])
            ->latest()
            ->paginate(15);
            
        return view('dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        // التأكد أن الطلب يخص المستخدم
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        $order->load(['product', 'product.owner', 'payment']);
        return view('dashboard.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        
        // السماح بالإلغاء فقط إذا كان الطلب معلق
        if ($order->status !== 'pending') {
            return back()->with('error', '❌ لا يمكن إلغاء هذا الطلب الآن');
        }
        
        $order->update(['status' => 'cancelled']);
        
        // إعادة الكمية للمنتج
        $order->product->increment('quantity', $order->quantity);
        
        return redirect()->route('dashboard.orders.index')
            ->with('success', '✅ تم إلغاء الطلب بنجاح');
    }
}