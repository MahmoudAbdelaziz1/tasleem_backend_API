<?php
// app/Http/Resources/CategoryResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'category_id' => $this->category_id,
            'name' => $this->name,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'status' => $this->status,
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}