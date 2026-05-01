<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'photo',
        'status',
    ];

    /**
     *  Relationships
     */

   
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    /**
     * Accessors & Mutators
     */

    
    public function getPhotoAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    /**
     * Scopes
     */

  
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }
}