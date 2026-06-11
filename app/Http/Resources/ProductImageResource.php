<?php
// app/Http/Resources/ProductImageResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
{
    public function toArray($request)
    {
        $rawUrl = $this->image_url ? trim($this->image_url) : null;
        $finalUrl = null;

        if ($rawUrl) {
  
            if (preg_match('#(https?://.+)#i', $rawUrl, $matches)) {
                $rawUrl = $matches[1]; 
            }

            if (filter_var($rawUrl, FILTER_VALIDATE_URL)) {
                $finalUrl = $rawUrl; 
            } else {
                $finalUrl = asset('storage/' . $rawUrl);   
            }
        }

        return [
            'image_id' => $this->image_id,
            'product_id' => $this->product_id,
            'image_url' => $finalUrl,
            'alt_text' => $this->alt_text,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
