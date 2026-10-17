<?php

namespace App\Http\Resources\Api\V1;

use App\Services\ImageVariants;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'brand' => $this->whenLoaded('brand', fn () => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'warranty' => $this->whenLoaded('warranty', fn () => [
                'title' => $this->warranty->title,
                'type' => $this->warranty->type,
                'months' => $this->warranty->months,
            ]),
            'model' => $this->model,
            'product_code' => $this->product_code,
            'price_rial' => (int) $this->price_rial,
            'discount_rial' => (int) $this->discount_rial,
            'final_price_rial' => $this->finalPrice(),
            'quantity' => $this->quantity,
            'in_stock' => (bool) $this->in_stock,
            'weight_gram' => $this->weight_gram,
            'dimensions_cm' => [
                'length' => $this->length_cm,
                'width' => $this->width_cm,
                'height' => $this->height_cm,
            ],
            'image' => $this->image,
            'image_variants' => $this->image ? ImageVariants::urls($this->image) : [],
            'ar_model_url' => $this->ar_model_url ? (str_starts_with($this->ar_model_url, 'http') ? $this->ar_model_url : asset('storage/'.$this->ar_model_url)) : null,
            'ar_ios_url' => $this->ar_ios_url ? (str_starts_with($this->ar_ios_url, 'http') ? $this->ar_ios_url : asset('storage/'.$this->ar_ios_url)) : null,
            'description' => $this->description,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($v) => [
                'id' => $v->id,
                'color' => $v->color ? ['name' => $v->color->name, 'hex' => $v->color->hex] : null,
                'price_rial' => (int) $v->price_rial ?: $this->finalPrice(),
                'quantity' => $v->quantity,
                'image' => $v->image,
            ])),
            'specs' => $this->whenLoaded('specs', fn () => $this->specs->map(fn ($s) => [
                'attr' => $s->attr?->name,
                'value' => $s->value,
            ])),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
