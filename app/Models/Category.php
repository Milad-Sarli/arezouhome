<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use GeneratesImageVariants;
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'parent_id', 'sort', 'image', 'banner',
        'description', 'is_important', 'status',
    ];

    protected function casts(): array
    {
        return ['is_important' => 'boolean', 'status' => 'boolean'];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product');
    }

    public function specAttrs(): HasMany
    {
        return $this->hasMany(ProductAttr::class)->where('type', 'spec');
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['image', 'banner'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('api.categories'));
        static::deleted(fn () => Cache::forget('api.categories'));
    }
}
