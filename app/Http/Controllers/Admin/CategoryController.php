<?php
// app/Http/Controllers/Admin/CategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        
        $categories = Category::withCount('products') 
            ->latest()
            ->paginate(15);
            
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:0,1',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('categories', 'public');
            $validated['photo'] = $path;
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'تم إضافة التصنيف بنجاح');
    }

    public function show(Category $category)
    {
        
        $category->loadCount('products');
        $category->load(['products' => function($q) {
            $q->latest()->limit(10);
        }, 'products.owner']);
        
        $productsCount = $category->products_count; // استخدم products_count من loadCount
        $activeProducts = $category->products()->where('status', '1')->count();
        
        return view('admin.categories.show', compact('category', 'productsCount', 'activeProducts'));
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->category_id . ',category_id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:0,1',
        ]);

        if ($request->hasFile('photo')) {
           
            if ($category->photo && Storage::disk('public')->exists($category->photo)) {
                Storage::disk('public')->delete($category->photo);
            }
            
            $path = $request->file('photo')->store('categories', 'public');
            $validated['photo'] = $path;
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success','updated successfully');
    }

    public function destroy(Category $category)
    {
        
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'This category cannot be deleted because it contains related products');
        }


        if ($category->photo && Storage::disk('public')->exists($category->photo)) {
            Storage::disk('public')->delete($category->photo);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'The category has been successfully deleted');
    }
    
    // تغيير حالة التصنيف
    public function toggleStatus(Category $category)
    {
        $category->status = $category->status == '1' ? '0' : '1';
        $category->save();
        
        return redirect()->route('admin.categories.index')
            ->with('success', 'The classification status has been successfully changed');
    }
}