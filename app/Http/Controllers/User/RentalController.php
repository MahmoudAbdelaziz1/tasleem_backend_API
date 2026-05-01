<?php
// app/Http/Controllers/User/RentalController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::where('renter_id', Auth::id())
            ->with(['product:id,name,price,type', 'product.images'])
            ->latest()
            ->paginate(15);
            
        return view('dashboard.rentals.index', compact('rentals'));
    }

    public function show(Rental $rental)
    {
        if ($rental->renter_id !== Auth::id()) abort(403);
        
        $rental->load(['product', 'product.owner', 'payment']);
        
        // حساب الأيام المتبقية
        $daysRemaining = null;
        if ($rental->status === 'active') {
            $endDate = Carbon::parse($rental->end_date);
            $today = Carbon::today();
            $daysRemaining = $endDate->isPast() ? 0 : $today->diffInDays($endDate);
        }
        
        return view('dashboard.rentals.show', compact('rental', 'daysRemaining'));
    }

    public function extend(Rental $rental, Request $request)
    {
        if ($rental->renter_id !== Auth::id()) abort(403);
        if ($rental->status !== 'active') {
            return back()->with('error', '❌ لا يمكن تمديد هذا العقد');
        }
        
        $validated = $request->validate([
            'extra_days' => 'required|integer|min:1|max:30',
        ]);
        
        // حساب السعر الإضافي
        $extraPrice = $rental->daily_price * $validated['extra_days'];
        
        // هنا يتم إضافة منطق الدفع للإضافي (يمكن ربطه بـ Payment Gateway)
        
        $rental->end_date = Carbon::parse($rental->end_date)->addDays($validated['extra_days']);
        $rental->total_price += $extraPrice;
        $rental->total_days += $validated['extra_days'];
        $rental->save();
        
        return redirect()->route('dashboard.rentals.show', $rental)
            ->with('success', '✅ تم تمديد فترة الإيجار بنجاح');
    }
}