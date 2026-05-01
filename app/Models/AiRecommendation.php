<?php
// app/Models/AiRecommendation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiRecommendation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ai_recommendations';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'rec_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'score',
        'algorithm_type',
        'reason',
        'metadata',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:4',
        'metadata' => 'array',
        'expires_at' => 'datetime',
    ];

    /**
     *  Relationships
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

    
    public function isValid()
    {
        return !$this->expires_at || $this->expires_at->isFuture();
    }

   
    public function getScorePercentageAttribute()
    {
        return round($this->score * 100, 2) . '%';
    }

    /**
     * Scopes
     */

    public function scopeValid($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }


    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

  
    public function scopeOfAlgorithm($query, $type)
    {
        return $query->where('algorithm_type', $type);
    }

  
    public function scopeTopScore($query, $limit = 10)
    {
        return $query->orderBy('score', 'desc')->limit($limit);
    }
}