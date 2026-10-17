<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Slider extends Model
{
    protected $fillable = ['title', 'link', 'status', 'sort'];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class)->orderBy('sort');
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('api.sliders'));
        static::deleted(fn () => Cache::forget('api.sliders'));
    }
}
