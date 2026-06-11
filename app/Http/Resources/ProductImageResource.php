<?php
// app/Http/Resources/ProductImageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        $rawUrl = $this->image_url ? trim($this->image_url) : null;

        return [
            'image_id' => $this->image_id,
            'product_id' => $this->product_id,
            
            'image_url' => $rawUrl 
                ? (filter_var($rawUrl, FILTER_VALIDATE_URL) 
                    ? $rawUrl                     
                    : asset('storage/' . $rawUrl))  
                : null,
                
            'alt_text' => $this->alt_text,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
