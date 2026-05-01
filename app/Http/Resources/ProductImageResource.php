<?php
// app/Http/Resources/ProductImageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'image_id' => $this->image_id,
            'product_id' => $this->product_id,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'alt_text' => $this->alt_text,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}