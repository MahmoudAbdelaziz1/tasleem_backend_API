<?php
// app/Models/ProductImage.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_images';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'image_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'image_url',
        'alt_text',
    ];

    /**
     * Relationships
     */

   
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Accessors & Mutators
     */

   
    public function getImageUrlAttribute($value)
    {
        return asset('storage/' . $value);
    }
}