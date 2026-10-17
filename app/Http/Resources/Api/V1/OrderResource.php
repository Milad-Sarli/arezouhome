<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ref_code' => $this->ref_code,
            'status' => $this->status,
            'status_label' => $this->statusLabel(),
            'items' => $this->items->map(fn ($i) => [
                'product_id' => $i->product_id,
                'product_slug' => $i->product?->slug,
                'product_name' => $i->product_name,
                'quantity' => $i->quantity,
                'unit_price_rial' => (int) $i->unit_price_rial,
                'total_rial' => (int) $i->total_rial,
            ]),
            'post_price_rial' => (int) $this->post_price_rial,
            'discount_price_rial' => (int) $this->discount_price_rial,
            'final_price_rial' => (int) $this->final_price_rial,
            'shipment' => $this->shipment?->title,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
