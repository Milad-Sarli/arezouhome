<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use GeneratesImageVariants;

    protected $fillable = [
        'product_id', 'color_id', 'price_rial', 'quantity', 'image', 'product_code', 'status',
    ];

    protected function casts(): array
    {
        return ['price_rial' => 'integer', 'quantity' => 'integer', 'status' => 'boolean'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Color::class);
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['image'];
    }
}
