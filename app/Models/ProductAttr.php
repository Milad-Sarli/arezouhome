<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttr extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'category_id', 'is_important', 'is_filterable', 'sort', 'status',
    ];

    protected function casts(): array
    {
        return [
            'is_important' => 'boolean',
            'is_filterable' => 'boolean',
            'status' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttrValue::class)->orderBy('sort');
    }

    public function isVariant(): bool
    {
        return $this->type === 'variant';
    }
}
