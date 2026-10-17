<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use GeneratesImageVariants;
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'brand_id', 'warranty_id', 'model', 'product_code',
        'price_rial', 'discount_rial', 'quantity', 'weight_gram',
        'length_cm', 'width_cm', 'height_cm', 'image', 'ar_model_url', 'ar_ios_url', 'description',
        'meta_description', 'keywords', 'in_stock', 'is_featured',
        'is_special', 'status',
    ];

    protected function casts(): array
    {
        return [
            'price_rial' => 'integer',
            'discount_rial' => 'integer',
            'quantity' => 'integer',
            'keywords' => 'array',
            'in_stock' => 'boolean',
            'is_featured' => 'boolean',
            'is_special' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function warranty(): BelongsTo
    {
        return $this->belongsTo(Warranty::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function questions()
    {
        return $this->hasMany(ProductQuestion::class);
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            if ($p->discount_rial > $p->price_rial) {
                $p->discount_rial = $p->price_rial;
            }
        });
    }

    public function finalPrice(): int
    {
        return max(0, $this->price_rial - $this->discount_rial);
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['image'];
    }
}
