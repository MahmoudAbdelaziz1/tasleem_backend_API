<?php
// app/Http/Controllers/Admin/RentalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function index(Request $request)
    {
        $query = Rental::with(['product', 'renter', 'payment']);
        
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
       
        if ($request->filled('renter_id')) {
            $query->where('renter_id', $request->renter_id);
        }
        
       
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        
      
        if ($request->filled('date_from')) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('end_date', '<=', $request->date_to);
        }
        
        $rentals = $query->latest()->paginate(15);
        
      
        $stats = [
            'total' => Rental::count(),
            'pending' => Rental::where('status', 'pending')->count(),
            'confirmed' => Rental::where('status', 'confirmed')->count(),
            'active' => Rental::where('status', 'active')->count(),
            'completed' => Rental::where('status', 'completed')->count(),
            'cancelled' => Rental::where('status', 'cancelled')->count(),
            'total_revenue' => Rental::where('status', 'completed')->sum('total_price'),
            'active_rentals_value' => Rental::where('status', 'active')->sum('total_price'),
        ];
        
        $renters = User::whereHas('rentals')->get();
        $products = Product::where('type', 'rental')->get();
        
        return view('admin.rentals.index', compact('rentals', 'stats', 'renters', 'products'));
    }

    public function show(Rental $rental)
    {
        $rental->load(['product', 'renter', 'payment', 'product.owner']);
        
        
        $userRentals = Rental::where('renter_id', $rental->renter_id)
            ->where('rental_id', '!=', $rental->rental_id)
            ->latest()
            ->limit(5)
            ->get();
        
      
        $productRentals = Rental::where('product_id', $rental->product_id)
            ->where('rental_id', '!=', $rental->rental_id)
            ->latest()
            ->limit(5)
            ->get();
        
 
        $daysRemaining = null;
        $isOverdue = false;
        if ($rental->status == 'active') {
            $endDate = Carbon::parse($rental->end_date);
            $today = Carbon::today();
            if ($endDate->isPast()) {
                $isOverdue = true;
                $daysRemaining = 0;
            } else {
                $daysRemaining = $today->diffInDays($endDate);
            }
        }
        
        return view('admin.rentals.show', compact('rental', 'userRentals', 'productRentals', 'daysRemaining', 'isOverdue'));
    }

    public function create()
    {
        $products = Product::where('type', 'rental')
            ->where('status', '1')
            ->where('quantity', '>', 0)
            ->get();
        $users = User::where('status', '1')->get();
        
        return view('admin.rentals.create', compact('products', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'renter_id' => 'required|exists:users,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'daily_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
        ]);
        
      
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;
        $totalPrice = $validated['daily_price'] * $totalDays;
        
        $validated['total_days'] = $totalDays;
        $validated['total_price'] = $totalPrice;
        
      
        $conflicting = Rental::where('product_id', $validated['product_id'])
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })->exists();
            
        if ($conflicting) {
            return back()->with('error', 'This product is not available during this period')->withInput();
        }
        
        Rental::create($validated);
        
        
        $product = Product::find($validated['product_id']);
        $product->decrement('quantity');
        
        return redirect()->route('admin.rentals.index')
            ->with('success', 'The lease agreement has been successfully established');
    }

    public function edit(Rental $rental)
    {
        $products = Product::where('type', 'rental')->get();
        $users = User::all();
        
        return view('admin.rentals.edit', compact('rental', 'products', 'users'));
    }

    public function update(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'renter_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_price' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
        ]);
        
       
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $totalDays = $start->diffInDays($end) + 1;
        $totalPrice = $validated['daily_price'] * $totalDays;
        
        $validated['total_days'] = $totalDays;
        $validated['total_price'] = $totalPrice;
        
  
        $conflicting = Rental::where('product_id', $validated['product_id'])
            ->where('rental_id', '!=', $rental->rental_id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })->exists();
            
        if ($conflicting) {
            return back()->with('error', 'This product is not available during this time')->withInput();
        }
        
        $oldStatus = $rental->status;
        $rental->update($validated);
        
       
        if ($oldStatus != 'cancelled' && $validated['status'] == 'cancelled') {
            $rental->product->increment('quantity');
        } elseif ($oldStatus == 'cancelled' && $validated['status'] != 'cancelled') {
            $rental->product->decrement('quantity');
        }
        
        return redirect()->route('admin.rentals.index')
            ->with('success', 'The lease agreement has been successfully updated.');
    }

    public function updateStatus(Request $request, Rental $rental)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,active,completed,cancelled',
        ]);
        
        $oldStatus = $rental->status;
        $rental->update($validated);
        
        
        if ($oldStatus != 'cancelled' && $validated['status'] == 'cancelled') {
            $rental->product->increment('quantity');
        } elseif ($oldStatus == 'cancelled' && $validated['status'] != 'cancelled') {
            $rental->product->decrement('quantity');
        }
        
        return redirect()->route('admin.rentals.index')
            ->with('success', 'Lease agreement status successfully updated');
    }
    
    public function destroy(Rental $rental)
    {

        if ($rental->status != 'cancelled') {
            $rental->product->increment('quantity');
        }
        
        $rental->delete();
        
        return redirect()->route('admin.rentals.index')
            ->with('success', 'The lease agreement has been successfully deleted.');
    }
    
    public function print(Rental $rental)
    {
        $rental->load(['product', 'renter', 'product.owner']);
        return view('admin.rentals.print', compact('rental'));
    }
    
    public function contract(Rental $rental)
    {
        $rental->load(['product', 'renter', 'product.owner']);
        return view('admin.rentals.contract', compact('rental'));
    }
}