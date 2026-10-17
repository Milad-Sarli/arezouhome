<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Brand extends Model
{
    use GeneratesImageVariants;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'logo', 'description', 'status'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['logo'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('api.brands'));
        static::deleted(fn () => Cache::forget('api.brands'));
    }
}
