<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'rental_id',
        'user_id',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
    ];

    /**
     *Relationships
     */

 
    public function user()
    {
        return $this->belongsTo(User::class);
    }

   
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }


    public function rental()
    {
        return $this->belongsTo(Rental::class, 'rental_id', 'rental_id');
    }

    /**
     * Accessors & Mutators
     */


    public function getDescriptionAttribute()
    {
        if ($this->order_id) {
            return "Paid the order cost#{$this->order_id}";
        } elseif ($this->rental_id) {
            return "Paid the rental cost#{$this->rental_id}";
        }
        return "Unrelated payment";
    }

    
    public function isSuccessful()
    {
        return $this->status === 'completed';
    }

    /**
     * Scopes
     */

    
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }


    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }


    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

  
    public function scopeWithMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
           
            if (is_null($payment->order_id) && is_null($payment->rental_id)) {
                throw ValidationException::withMessages([
                    'order_id' => 'must select the order or rental',
                    'rental_id' => 'must select the order or rental'
                ]);
            }

            
            if (!is_null($payment->order_id) && !is_null($payment->rental_id)) {
                throw ValidationException::withMessages([
                    'order_id' => 'dont pay the order and rental together',
                    'rental_id' => 'dont pay the order and rental together'
                ]);
            }
        });

        static::saved(function ($payment) {
           
            if ($payment->status === 'completed') {
                if ($payment->order_id) {
                    $payment->order->update(['payment_status' => 'paid']);
                } elseif ($payment->rental_id) {
                    $payment->rental->update(['status' => 'confirmed']);
                }
            }
        });
    }
}