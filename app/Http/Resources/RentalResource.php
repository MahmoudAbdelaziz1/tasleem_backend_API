<?php
// app/Http/Resources/RentalResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'rental_id' => $this->rental_id,
            'product' => new ProductResource($this->whenLoaded('product')),
            'renter' => new UserResource($this->whenLoaded('renter')),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'daily_price' => $this->daily_price,
            'total_days' => $this->total_days,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}