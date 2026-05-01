<?php
// app/Http/Resources/CartItemResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'cart_item_id' => $this->cart_item_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'quantity' => $this->quantity,
            'rental_start_date' => $this->rental_start_date,
            'rental_end_date' => $this->rental_end_date,
            'item_type' => $this->item_type,
            'subtotal' => $this->subtotal,
            'added_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}