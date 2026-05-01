<?php
// app/Models/CartItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_items';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'cart_item_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'rental_start_date',
        'rental_end_date',
        'item_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rental_start_date' => 'date',
        'rental_end_date' => 'date',
    ];

    /**
     * Relationships
     */

  
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Accessors & Mutators
     */

    
    public function getSubtotalAttribute()
    {
        if ($this->item_type === 'purchase') {
            return $this->quantity * $this->product->price;
        } else {
            $days = Carbon::parse($this->rental_start_date)
                         ->diffInDays(Carbon::parse($this->rental_end_date)) + 1;
            return $days * $this->product->rental_price_per_day;
        }
    }

    /**
     * Scopes
     */

    // 
    public function scopePurchases($query)
    {
        return $query->where('item_type', 'purchase');
    }


    public function scopeRentals($query)
    {
        return $query->where('item_type', 'rental');
    }


    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cartItem) {
           
            if ($cartItem->item_type === 'rental') {
                if (is_null($cartItem->rental_start_date) || is_null($cartItem->rental_end_date)) {
                    throw ValidationException::withMessages([
                        'rental_start_date' => 'You Must Put A Start Date',
                        'rental_end_date' => 'You Must Put A End Date'
                    ]);
                }

                $start = Carbon::parse($cartItem->rental_start_date);
                $end = Carbon::parse($cartItem->rental_end_date);
                
                if ($end <= $start) {
                    throw ValidationException::withMessages([
                        'rental_end_date' => 'End Date Must Be After Start Date'
                    ]);
                }

               
                if (!$cartItem->product->isForRent()) {
                    throw ValidationException::withMessages([
                        'product_id' => 'This Product Is Not Avilabel For Rental'
                    ]);
                }
            } else {
                
                if (!$cartItem->product->isForSale()) {
                    throw ValidationException::withMessages([
                        'product_id' => 'This Product Is Not Avilabel For Paying'
                    ]);
                }
            }

          
            if ($cartItem->quantity > $cartItem->product->quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'The Quantity Is Not Avilable'
                ]);
            }
        });
    }
}