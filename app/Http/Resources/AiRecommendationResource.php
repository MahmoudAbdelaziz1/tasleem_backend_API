<?php
// app/Http/Resources/AiRecommendationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AiRecommendationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'rec_id' => $this->rec_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'product' => new ProductResource($this->whenLoaded('product')),
            'score' => $this->score,
            'algorithm_type' => $this->algorithm_type,
            'reason' => $this->reason,
            'metadata' => $this->metadata,
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}