<?php
// app/Http/Resources/PaymentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'payment_id' => $this->payment_id,
            'order' => new OrderResource($this->whenLoaded('order')),
            'rental' => new RentalResource($this->whenLoaded('rental')),
            'user' => new UserResource($this->whenLoaded('user')),
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
}