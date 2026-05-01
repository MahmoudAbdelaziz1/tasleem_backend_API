<?php
// app/Http/Controllers/User/ProductController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('owner_id', Auth::id())
            ->with(['category', 'images' => fn($q) => $q->take(1)])
            ->latest()
            ->paginate(12);
            
        return view('dashboard.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', '1')->get();
        return view('dashboard.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:sale,rental,both',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'available'; // الحالة الافتراضية

        $product = Product::create($validated);

        // رفع الصور
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('dashboard.products.index')
            ->with('success', '✅ تم إضافة المنتج بنجاح');
    }

    public function show(Product $product)
    {
        // التأكد أن المنتج يخص المستخدم
        if ($product->owner_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }
        
        $product->load(['category', 'images', 'reviews.user']);
        return view('dashboard.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if ($product->owner_id !== Auth::id()) abort(403);
        
        $categories = Category::where('status', '1')->get();
        return view('dashboard.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->owner_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'quantity' => 'required|integer|min:0',
            'type' => 'required|in:sale,rental,both',
            'status' => 'required|in:available,rented,sold,unavailable',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $product->update($validated);

        // رفع صور جديدة لو موجودة
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create(['image_url' => $path]);
            }
        }

        return redirect()->route('dashboard.products.index')
            ->with('success', '✅ تم تحديث المنتج بنجاح');
    }

    public function destroy(Product $product)
    {
        if ($product->owner_id !== Auth::id()) abort(403);

        // حذف الصور من التخزين
        foreach ($product->images as $image) {
            if ($image->image_url) {
                Storage::disk('public')->delete($image->image_url);
            }
        }
        
        $product->delete();

        return redirect()->route('dashboard.products.index')
            ->with('success', '🗑️ تم حذف المنتج بنجاح');
    }
}