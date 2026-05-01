<?php
// app/Http/Controllers/User/WishlistController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    public function index()
    {
        // التعامل مع اسم الجدول المرن (wishlist أو wishlists)
        $table = DB::getSchemaBuilder()->hasTable('wishlists') ? 'wishlists' : 'wishlist';
        
        $wishlistItems = DB::table($table)
            ->where('user_id', Auth::id())
            ->join('products', "{$table}.product_id", '=', 'products.id')
            ->select('products.*', "{$table}.created_at as added_at")
            ->latest('added_at')
            ->paginate(12);
            
        return view('dashboard.wishlist.index', compact('wishlistItems'));
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);
        
        $table = DB::getSchemaBuilder()->hasTable('wishlists') ? 'wishlists' : 'wishlist';
        
        // منع التكرار
        $exists = DB::table($table)
            ->where('user_id', Auth::id())
            ->where('product_id', $validated['product_id'])
            ->exists();
            
        if ($exists) {
            return back()->with('info', 'ℹ️ المنتج موجود بالفعل في المفضلة');
        }
        
        DB::table($table)->insert([
            'user_id' => Auth::id(),
            'product_id' => $validated['product_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return back()->with('success', '✅ تمت إضافة المنتج للمفضلة');
    }

    public function remove($productId)
    {
        $table = DB::getSchemaBuilder()->hasTable('wishlists') ? 'wishlists' : 'wishlist';
        
        DB::table($table)
            ->where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->delete();
            
        return back()->with('success', '🗑️ تم إزالة المنتج من المفضلة');
    }
}