<?php

namespace App\Models;

use App\Models\Concerns\GeneratesImageVariants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Slide extends Model
{
    use GeneratesImageVariants;

    protected $fillable = [
        'slider_id', 'image', 'title', 'link', 'button_title',
        'button_link', 'description', 'sort', 'status',
    ];

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function slider(): BelongsTo
    {
        return $this->belongsTo(Slider::class);
    }

    /** @return string[] */
    public function variantImageFields(): array
    {
        return ['image'];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('api.sliders'));
        static::deleted(fn () => Cache::forget('api.sliders'));
    }
}
